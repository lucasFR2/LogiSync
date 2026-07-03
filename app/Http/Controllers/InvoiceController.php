<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Carrier;
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
        $query = Invoice::with('user:id,name')
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
        $products  = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $customers = Customer::orderBy('name')->get(['id', 'name', 'document', 'email', 'phone', 'address', 'city', 'state', 'zip_code']);

        $carriers  = \App\Models\Carrier::orderBy('name')->get(['id','name','cnpj','state_registration','street','number','city','state','vehicle_plate','vehicle_uf']);

        $invoice = null; // Garante que a variável existe na view

        return view('invoices.create', compact('number', 'products', 'suppliers', 'customers', 'carriers', 'invoice'));
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
        $invoice->load([
            'user:id,name',
            'conferredBy:id,name',
            'items' => function ($query) {
                $query->select([
                    'id',
                    'invoice_id',
                    'description',
                    'unit',
                    'quantity',
                    'unit_price',
                    'discount',
                    'total',
                ])->orderBy('id');
            },
        ]);
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
        $products  = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $customers = Customer::orderBy('name')->get(['id', 'name', 'document', 'email', 'phone', 'address', 'city', 'state', 'zip_code']);

        $carriers  = \App\Models\Carrier::orderBy('name')->get(['id','name','cnpj','state_registration','street','number','city','state','vehicle_plate','vehicle_uf']);

        return view('invoices.create', compact('invoice', 'products', 'suppliers', 'customers', 'carriers'));
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
 
    /**
     * Gerar PDF do romaneio de entrega (stream)
     */
    public function romaneio(Invoice $invoice)
    {
        $invoice->load(['items.product.location', 'user:id,name', 'carrier', 'conferredBy:id,name']);
 
        $pdf = Pdf::loadView('invoices.romaneio_pdf', compact('invoice'))
                  ->setPaper('a4', 'portrait');
 
        $filename = 'ROMANEIO-NF-' . $invoice->number . '.pdf';
 
        return $pdf->stream($filename);
    }

    /**
     * Realiza a conferência da nota fiscal
     */
    public function confer(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'conference_status' => 'required|in:Pendente,Conferida,Divergente',
            'conference_notes'  => 'nullable|string|max:1000',
        ]);

        $invoice->update([
            'conference_status' => $validated['conference_status'],
            'conference_notes'  => $validated['conference_notes'],
            'conferred_by'      => Auth::id(),
            'conferred_at'      => now(),
        ]);

        Logger::log('confer_invoice', "O usuário realizou a conferência da NF #{$invoice->number} com status: {$validated['conference_status']}");

        if ($validated['conference_status'] === 'Conferida' && $invoice->status === 'emitida' && $invoice->type === 'saida') {
            $this->invoiceService->concludeInvoice($invoice);
            return redirect()->route('invoices.show', $invoice)
                              ->with('success', 'Conferência da nota fiscal atualizada com sucesso e estoque baixado!');
        }

        return redirect()->route('invoices.show', $invoice)
                         ->with('success', 'Conferência da nota fiscal atualizada com sucesso!');
    }

    /**
     * Tela de conferência interativa de saída (expedição)
     */
    public function conferWorkflow(Invoice $invoice)
    {
        $invoice->load(['items.product', 'user:id,name']);
        return view('invoices.confer', compact('invoice'));
    }

    /**
     * Salvar resultado da conferência interativa de saída
     */
    public function conferSave(Request $request, Invoice $invoice)
    {
        $request->validate([
            'checked_quantities' => 'required|array',
            'checked_quantities.*' => 'numeric|min:0',
        ]);

        $invoice->load('items');

        $hasDivergence = false;
        $divergences = [];

        foreach ($invoice->items as $item) {
            $checked = (float) ($request->checked_quantities[$item->id] ?? 0);
            $item->update(['checked_quantity' => $checked]);

            if (abs($checked - (float)$item->quantity) > 0.001) {
                $hasDivergence = true;
                $diff = $checked - (float)$item->quantity;
                $type = $diff > 0 ? 'EXCESSO' : 'FALTA';
                $divergences[] = "{$item->description}: {$type} de " . abs($diff) . " " . ($item->unit ?? 'UN');
            }
        }

        $status = $hasDivergence ? 'Divergente' : 'Conferida';
        $notes = $hasDivergence
            ? "Divergências encontradas:\n" . implode("\n", $divergences)
            : 'Conferência realizada sem divergências.';

        $invoice->update([
            'conference_status' => $status,
            'conference_notes'  => $notes,
            'conferred_by'      => Auth::id(),
            'conferred_at'      => now(),
        ]);

        Logger::log('confer_invoice_workflow', "O usuário realizou a conferência interativa da NF #{$invoice->number} com status: {$status}");

        if ($status === 'Conferida' && $invoice->status === 'emitida' && $invoice->type === 'saida') {
            $this->invoiceService->concludeInvoice($invoice);
        }

        return redirect()->route('invoices.show', $invoice)
                          ->with('success', "Conferência finalizada com status: {$status}" . ($status === 'Conferida' ? " e estoque baixado!" : ""))
                          ->with('open_romaneio', $status === 'Conferida' ? true : null);
    }

    /**
     * Concluir faturamento e dar baixa no estoque
     */
    public function conclude(Invoice $invoice)
    {
        if ($invoice->type !== 'saida') {
            return redirect()->back()->with('error', 'Apenas notas de saída podem ser concluídas.');
        }

        if ($invoice->status === 'concluída') {
            return redirect()->back()->with('error', 'Esta nota já está concluída.');
        }

        if ($invoice->status !== 'emitida') {
            return redirect()->back()->with('error', 'Apenas notas emitidas podem ser concluídas.');
        }

        try {
            $this->invoiceService->concludeInvoice($invoice);

            Logger::log('conclude_invoice', "O faturamento da NF #{$invoice->number} foi concluído manualmente.");

            return redirect()->route('invoices.show', $invoice)
                             ->with('success', 'Nota fiscal concluída e estoque baixado com sucesso!')
                             ->with('open_romaneio', true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao concluir nota: ' . $e->getMessage());
        }
    }
}
