<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\Logger;

class InvoiceController extends Controller
{
    /**
     * Listagem de notas fiscais
     */
    public function index(Request $request)
    {
        $query = Invoice::with('user')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('number', 'like', "%$s%")
                  ->orWhere('recipient_name', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $invoices = $query->paginate(15)->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $number    = Invoice::nextNumber();
        $products  = Product::orderBy('name')->get(['id', 'name', 'unit_price', 'unit', 'barcode', 'quantity']);
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('invoices.create', compact('number', 'products', 'suppliers', 'customers'));
    }

    /**
     * Gravar nova nota fiscal
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'               => 'required|in:entrada,saida',
            'recipient_name'     => 'required|string|max:255',
            'recipient_document' => 'nullable|string|max:30',
            'recipient_email'    => 'nullable|email|max:255',
            'recipient_phone'    => 'nullable|string|max:20',
            'recipient_address'  => 'nullable|string|max:255',
            'recipient_city'     => 'nullable|string|max:100',
            'recipient_state'    => 'nullable|string|max:2',
            'recipient_zip'      => 'nullable|string|max:10',
            'payment_method'     => 'required',
            'discount'           => 'nullable|numeric|min:0',
            'shipping'           => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
            'due_date'           => 'nullable|date',
            'issued_at'          => 'nullable|date',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id'  => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0|max:100',
            'items.*.unit'        => 'nullable|string|max:10',
            'items.*.ncm'         => 'nullable|string|max:15',
            'items.*.cfop'        => 'nullable|string|max:10',
            'items.*.icms_rate'   => 'nullable|numeric|min:0',
            'items.*.ipi_rate'    => 'nullable|numeric|min:0',
            'items.*.pis_rate'    => 'nullable|numeric|min:0',
            'items.*.cofins_rate' => 'nullable|numeric|min:0',
        ]);

        $isEmitting = $request->input('action') === 'emit';

        try {
            DB::transaction(function () use ($request, $isEmitting) {
                $subtotal = 0;
                $itemsData = [];

                foreach ($request->items as $item) {
                    $disc  = (float) ($item['discount'] ?? 0);
                    $qty   = (float) $item['quantity'];
                    $price = (float) $item['unit_price'];
                    $total = $qty * $price * (1 - $disc / 100);
                    $subtotal += $total;

                    $itemsData[] = [
                        'product_id'   => $item['product_id'] ?? null,
                        'description'  => $item['description'],
                        'ncm'          => $item['ncm'] ?? '0000.00.00',
                        'cfop'         => $item['cfop'] ?? '5.102',
                        'unit'         => $item['unit'] ?? 'un',
                        'quantity'     => $qty,
                        'unit_price'   => $price,
                        'discount'     => $disc,
                        'total'        => round($total, 2),
                        'icms_base'    => round($total, 2),
                        'icms_rate'    => (float) ($item['icms_rate'] ?? 0),
                        'icms_value'   => round($total * (($item['icms_rate'] ?? 0) / 100), 2),
                        'ipi_rate'     => (float) ($item['ipi_rate'] ?? 0),
                        'ipi_value'    => round($total * (($item['ipi_rate'] ?? 0) / 100), 2),
                        'pis_rate'     => (float) ($item['pis_rate'] ?? 0),
                        'pis_value'    => round($total * (($item['pis_rate'] ?? 0) / 100), 2),
                        'cofins_rate'  => (float) ($item['cofins_rate'] ?? 0),
                        'cofins_value' => round($total * (($item['cofins_rate'] ?? 0) / 100), 2),
                    ];

                    // Stock Deduction / Addition
                    if ($isEmitting && !empty($item['product_id'])) {
                        $product = Product::findOrFail($item['product_id']);
                        
                        if ($request->type === 'saida') {
                            if ($product->quantity < $qty) {
                                throw new \Exception("Estoque insuficiente para o produto '{$product->name}'. Disponível: {$product->quantity}");
                            }
                            $product->decrement('quantity', $qty);
                            $movType = 'saida';
                        } else {
                            $product->increment('quantity', $qty);
                            $movType = 'entrada';
                        }

                        // Gravar movimentação de estoque
                        \App\Models\Inventory::create([
                            'product_id' => $product->id,
                            'quantity'   => $qty,
                            'type'       => $movType,
                            'status'     => 'confirmada',
                            'notes'      => 'Movimentação automática via ' . ($request->type === 'saida' ? 'Saída' : 'Entrada') . ' de NF',
                            'user_id'    => Auth::id(),
                        ]);
                    }
                }

                $discount  = (float) ($request->discount ?? 0);
                $shipping  = (float) ($request->shipping ?? 0);
                $grandTotal = $subtotal - $discount + $shipping;

                $invoice = Invoice::create([
                    'number'             => Invoice::nextNumber(),
                    'series'             => '001',
                    'type'               => $request->type,
                    'status'             => $isEmitting ? 'emitida' : 'rascunho',
                    'recipient_name'     => $request->recipient_name,
                    'recipient_document' => $request->recipient_document,
                    'recipient_email'    => $request->recipient_email,
                    'recipient_phone'    => $request->recipient_phone,
                    'recipient_address'  => $request->recipient_address,
                    'recipient_city'     => $request->recipient_city,
                    'recipient_state'    => $request->recipient_state,
                    'recipient_zip'      => $request->recipient_zip,
                    'subtotal'           => round($subtotal, 2),
                    'discount'           => $discount,
                    'shipping'           => $shipping,
                    'total'              => round($grandTotal, 2),
                    'payment_method'     => $request->payment_method,
                    'notes'              => $request->notes,
                    'due_date'           => $request->due_date,
                    'issued_at'          => $request->issued_at ?? now()->toDateString(),
                    'user_id'            => Auth::id(),
                    'supplier_id'        => $request->supplier_id,
                ]);

                $invoice->items()->createMany($itemsData);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        $invoice = Invoice::latest()->first();
        Logger::log('create_invoice', "O usuário criou a NF #{$invoice->number} para {$invoice->recipient_name}");

        return redirect()->route('invoices.index')
                         ->with('success', $isEmitting ? 'Nota fiscal emitida e estoque atualizado!' : 'Rascunho de nota fiscal salvo!');
    }

    /**
     * Visualizar nota fiscal
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('items.product', 'user', 'supplier');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'emitida') {
            return redirect()->route('invoices.show', $invoice)
                             ->with('error', 'Notas emitidas não podem ser editadas.');
        }

        $invoice->load('items.product', 'supplier');
        $products  = Product::orderBy('name')->get(['id', 'name', 'unit_price', 'unit', 'barcode']);
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('invoices.create', compact('invoice', 'products', 'suppliers', 'customers'));
    }

    /**
     * Atualizar nota fiscal
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'emitida') {
            return redirect()->route('invoices.show', $invoice)
                             ->with('error', 'Notas emitidas não podem ser editadas.');
        }

        $request->validate([
            'type'               => 'required|in:entrada,saida',
            'recipient_name'     => 'required|string|max:255',
            'payment_method'     => 'required',
            'items'              => 'required|array|min:1',
            'items.*.product_id'  => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0|max:100',
            'items.*.unit'        => 'nullable|string|max:10',
            'items.*.ncm'         => 'nullable|string|max:15',
            'items.*.cfop'        => 'nullable|string|max:10',
            'items.*.icms_rate'   => 'nullable|numeric|min:0',
            'items.*.ipi_rate'    => 'nullable|numeric|min:0',
            'items.*.pis_rate'    => 'nullable|numeric|min:0',
            'items.*.cofins_rate' => 'nullable|numeric|min:0',
        ]);

        $isEmitting = $request->input('action') === 'emit';

        try {
            DB::transaction(function () use ($request, $invoice, $isEmitting) {
                $subtotal  = 0;
                $itemsData = [];

                foreach ($request->items as $item) {
                    $disc  = (float) ($item['discount'] ?? 0);
                    $qty   = (float) $item['quantity'];
                    $price = (float) $item['unit_price'];
                    $total = $qty * $price * (1 - $disc / 100);
                    $subtotal += $total;

                    $itemsData[] = [
                        'product_id'   => $item['product_id'] ?? null,
                        'description'  => $item['description'],
                        'ncm'          => $item['ncm'] ?? '0000.00.00',
                        'cfop'         => $item['cfop'] ?? '5.102',
                        'unit'         => $item['unit'] ?? 'un',
                        'quantity'     => $qty,
                        'unit_price'   => $price,
                        'discount'     => $disc,
                        'total'        => round($total, 2),
                        'icms_base'    => round($total, 2),
                        'icms_rate'    => (float) ($item['icms_rate'] ?? 0),
                        'icms_value'   => round($total * (($item['icms_rate'] ?? 0) / 100), 2),
                        'ipi_rate'     => (float) ($item['ipi_rate'] ?? 0),
                        'ipi_value'    => round($total * (($item['ipi_rate'] ?? 0) / 100), 2),
                        'pis_rate'     => (float) ($item['pis_rate'] ?? 0),
                        'pis_value'    => round($total * (($item['pis_rate'] ?? 0) / 100), 2),
                        'cofins_rate'  => (float) ($item['cofins_rate'] ?? 0),
                        'cofins_value' => round($total * (($item['cofins_rate'] ?? 0) / 100), 2),
                    ];

                    // Stock Deduction / Addition ONLY if emitting now
                    if ($isEmitting && !empty($item['product_id'])) {
                        $product = Product::findOrFail($item['product_id']);
                        
                        if ($request->type === 'saida') {
                            if ($product->quantity < $qty) {
                                throw new \Exception("Estoque insuficiente para o produto '{$product->name}'. Disponível: {$product->quantity}");
                            }
                            $product->decrement('quantity', $qty);
                            $movType = 'saida';
                        } else {
                            $product->increment('quantity', $qty);
                            $movType = 'entrada';
                        }

                        \App\Models\Inventory::create([
                            'product_id' => $product->id,
                            'quantity'   => $qty,
                            'type'       => $movType,
                            'status'     => 'confirmada',
                            'notes'      => 'Movimentação automática via ' . ($request->type === 'saida' ? 'Saída' : 'Entrada') . ' de NF',
                            'user_id'    => Auth::id(),
                        ]);
                    }
                }

                $discount   = (float) ($request->discount ?? 0);
                $shipping   = (float) ($request->shipping ?? 0);
                $grandTotal = $subtotal - $discount + $shipping;

                $invoice->update([
                    'type'               => $request->type,
                    'status'             => $isEmitting ? 'emitida' : 'rascunho',
                    'recipient_name'     => $request->recipient_name,
                    'recipient_document' => $request->recipient_document,
                    'recipient_email'    => $request->recipient_email,
                    'recipient_phone'    => $request->recipient_phone,
                    'recipient_address'  => $request->recipient_address,
                    'recipient_city'     => $request->recipient_city,
                    'recipient_state'    => $request->recipient_state,
                    'recipient_zip'      => $request->recipient_zip,
                    'subtotal'           => round($subtotal, 2),
                    'discount'           => $discount,
                    'shipping'           => $shipping,
                    'total'              => round($grandTotal, 2),
                    'payment_method'     => $request->payment_method,
                    'notes'              => $request->notes,
                    'due_date'           => $request->due_date,
                    'issued_at'          => $request->issued_at,
                    'supplier_id'        => $request->supplier_id,
                ]);

                $invoice->items()->delete();
                $invoice->items()->createMany($itemsData);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        Logger::log('update_invoice', "O usuário alterou a NF #{$invoice->number}");

        return redirect()->route('invoices.show', $invoice)
                         ->with('success', $isEmitting ? 'Nota fiscal emitida e estoque atualizado!' : 'Nota fiscal atualizada!');
    }

    /**
     * Cancelar nota fiscal
     */
    public function cancel(Invoice $invoice)
    {
        $invoice->update(['status' => 'cancelada']);

        Logger::log('cancel_invoice', "O usuário cancelou a NF #{$invoice->number}");

        return redirect()->route('invoices.show', $invoice)
                         ->with('success', 'Nota fiscal cancelada!');
    }

    /**
     * Excluir nota fiscal (apenas rascunhos)
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'rascunho') {
            return redirect()->route('invoices.index')
                             ->with('error', 'Apenas rascunhos podem ser excluídos.');
        }

        $invNum = $invoice->number;
        $invoice->delete();

        Logger::log('delete_invoice', "O usuário removeu o rascunho da NF #{$invNum}");

        return redirect()->route('invoices.index')
                         ->with('success', 'Nota fiscal excluída!');
    }

    /**
     * Gerar PDF da nota fiscal
     */
    public function pdf(Invoice $invoice)
    {
        $invoice->load('items.product', 'user', 'supplier');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
                  ->setPaper('a4', 'portrait');

        $filename = 'NF-' . $invoice->number . '.pdf';

        return $pdf->download($filename);
    }
}
