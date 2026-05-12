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

    protected $invoiceService;

    public function __construct(\App\Services\InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Listagem de notas fiscais
     */
    public function index(Request $request)
    {
        $invoices = Invoice::with('user:id,name')
            ->when($request->search, function($q, $s) {
                $q->where(function ($query) use ($s) {
                    $query->where('number', 'like', "%$s%")
                          ->orWhere('recipient_name', 'like', "%$s%");
                });
            })
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

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
        $customers = \App\Models\Customer::orderBy('name')->get(['id', 'name', 'document', 'email', 'phone', 'address', 'city', 'state', 'zip_code']);

        return view('invoices.create', compact('number', 'products', 'suppliers', 'customers'));
    }

    /**
     * Gravar nova nota fiscal
     */
    public function store(\App\Http\Requests\StoreInvoiceRequest $request)
    {
        $isEmitting = $request->input('action') === 'emit';

        try {
            $invoice = $this->invoiceService->processInvoice($request->validated(), null, $isEmitting);
            
            Logger::log('create_invoice', "O usuário criou a NF #{$invoice->number} para {$invoice->recipient_name}");

            return redirect()->route('invoices.index')
                             ->with('success', $isEmitting ? 'Nota fiscal emitida e estoque atualizado!' : 'Rascunho de nota fiscal salvo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Visualizar nota fiscal
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['items.product', 'user:id,name', 'supplier:id,name']);
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
        $customers = \App\Models\Customer::orderBy('name')->get(['id', 'name', 'document', 'email', 'phone', 'address', 'city', 'state', 'zip_code']);

        return view('invoices.create', compact('invoice', 'products', 'suppliers', 'customers'));
    }

    /**
     * Atualizar nota fiscal
     */
    public function update(\App\Http\Requests\StoreInvoiceRequest $request, Invoice $invoice)
    {
        if ($invoice->status === 'emitida') {
            return redirect()->route('invoices.show', $invoice)
                             ->with('error', 'Notas emitidas não podem ser editadas.');
        }

        $isEmitting = $request->input('action') === 'emit';

        try {
            $this->invoiceService->processInvoice($request->validated(), $invoice, $isEmitting);
            
            Logger::log('update_invoice', "O usuário alterou a NF #{$invoice->number}");

            return redirect()->route('invoices.show', $invoice)
                             ->with('success', $isEmitting ? 'Nota fiscal emitida e estoque atualizado!' : 'Nota fiscal atualizada!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
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
        $invoice->load(['items.product', 'user:id,name', 'supplier:id,name']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
                  ->setPaper('a4', 'portrait');

        $filename = 'NF-' . $invoice->number . '.pdf';

        return $pdf->download($filename);
    }
}
