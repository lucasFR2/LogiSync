<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display the reports control panel
     */
    public function index()
    {
        // 1. Categories for filters
        $categories = Product::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category')
            ->pluck('category');

        // 2. Locations for filters
        $locations = WarehouseLocation::orderBy('full_code')->get();

        // 3. High-level dashboard statistics
        $stats = [
            'total_cost_value' => Product::ativos()->get()->sum(function($p) {
                return $p->quantity * ($p->cost_price ?? 0);
            }),
            'total_selling_value' => Product::ativos()->get()->sum(function($p) {
                return $p->quantity * ($p->selling_price ?? 0);
            }),
            'total_items_in_stock' => Product::ativos()->sum('quantity'),
            'recent_movements_count' => Inventory::where('created_at', '>=', now()->subDays(30))->count(),
            'monthly_billing' => Invoice::where('status', 'concluída')
                ->where('issued_at', '>=', now()->startOfMonth())
                ->sum('total'),
            'low_stock_alerts' => Product::baixoEstoque()->count(),
        ];

        return view('reports.index', compact('categories', 'locations', 'stats'));
    }

    /**
     * Generate HTML view or trigger file download (PDF/CSV)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string|in:stock_position,stock_movement,billing,low_stock',
        ]);

        $reportType = $request->report_type;
        $filters = $request->only(['start_date', 'end_date', 'category', 'location_id', 'type', 'status']);
        
        $results = collect();
        $summaries = [];

        switch ($reportType) {
            case 'stock_position':
                $query = Product::ativos()->with('location');
                if ($request->filled('category')) {
                    $query->where('category', $request->category);
                }
                if ($request->filled('location_id')) {
                    $query->where('warehouse_location_id', $request->location_id);
                }
                $results = $query->orderBy('name')->get();

                $totalQty = $results->sum('quantity');
                $totalCost = $results->sum(function($p) { return $p->quantity * ($p->cost_price ?? 0); });
                $totalSelling = $results->sum(function($p) { return $p->quantity * ($p->selling_price ?? 0); });

                $summaries = [
                    'Total de Itens Diferentes' => $results->count(),
                    'Quantidade Total Física' => number_format($totalQty, 0, ',', '.'),
                    'Custo Total em Estoque' => 'R$ ' . number_format($totalCost, 2, ',', '.'),
                    'Valor Total de Venda' => 'R$ ' . number_format($totalSelling, 2, ',', '.'),
                    'Lucro Estimado' => 'R$ ' . number_format($totalSelling - $totalCost, 2, ',', '.'),
                ];
                break;

            case 'stock_movement':
                $query = Inventory::with(['product', 'user']);
                if ($request->filled('start_date')) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                }
                if ($request->filled('type')) {
                    $query->where('type', $request->type);
                }
                $results = $query->orderBy('created_at', 'desc')->get();

                $entriesCount = $results->where('type', 'entrada')->count();
                $exitsCount = $results->where('type', 'saida')->count();
                $totalQtyMoved = $results->sum('quantity');

                $summaries = [
                    'Total de Movimentações' => $results->count(),
                    'Quantidade de Entradas' => $entriesCount,
                    'Quantidade de Saídas' => $exitsCount,
                    'Volume Total Movimentado' => number_format($totalQtyMoved, 0, ',', '.'),
                ];
                break;

            case 'billing':
                $query = Invoice::query();
                if ($request->filled('start_date')) {
                    $query->whereDate('issued_at', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('issued_at', '<=', $request->end_date);
                }
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                $results = $query->orderBy('issued_at', 'desc')->get();

                $totalBilled = $results->sum('total');
                $totalDiscount = $results->sum('discount');
                $totalShipping = $results->sum('shipping');

                $summaries = [
                    'Total de Notas' => $results->count(),
                    'Total Faturado (Bruto)' => 'R$ ' . number_format($totalBilled, 2, ',', '.'),
                    'Total de Descontos' => 'R$ ' . number_format($totalDiscount, 2, ',', '.'),
                    'Total de Fretes' => 'R$ ' . number_format($totalShipping, 2, ',', '.'),
                ];
                break;

            case 'low_stock':
                $results = Product::baixoEstoque()->ativos()->with('location')->orderBy('quantity')->get();

                $summaries = [
                    'Itens com Estoque Crítico' => $results->count(),
                    'Quantidade Total Faltante (para estoque máximo)' => number_format($results->sum(function($p) {
                        return max(0, ($p->max_stock ?? 0) - $p->quantity);
                    }), 0, ',', '.'),
                ];
                break;
        }

        // Export Actions
        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', compact('results', 'reportType', 'filters', 'summaries'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('relatorio_' . $reportType . '_' . date('Ymd_His') . '.pdf');
        }

        if ($request->export === 'csv') {
            return $this->downloadCsv($reportType, $results);
        }

        return view('reports.preview', compact('results', 'reportType', 'filters', 'summaries'));
    }

    /**
     * Download report as CSV format
     */
    private function downloadCsv(string $reportType, $results)
    {
        $filename = "relatorio_" . $reportType . "_" . date('Ymd_His') . ".csv";

        $callback = function() use ($reportType, $results) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compliance
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            switch ($reportType) {
                case 'stock_position':
                    fputcsv($file, ['Nome do Produto', 'SKU', 'Código de Barras', 'Categoria', 'Localização', 'Quantidade', 'Preço de Custo (R$)', 'Preço de Venda (R$)', 'Total Custo (R$)', 'Total Venda (R$)'], ';');
                    foreach ($results as $p) {
                        fputcsv($file, [
                            $p->name,
                            $p->sku,
                            $p->barcode,
                            $p->category ?? 'Sem Categoria',
                            $p->location ? $p->location->code : 'Não Alocado',
                            $p->quantity,
                            number_format($p->cost_price, 2, ',', ''),
                            number_format($p->selling_price, 2, ',', ''),
                            number_format($p->quantity * $p->cost_price, 2, ',', ''),
                            number_format($p->quantity * $p->selling_price, 2, ',', ''),
                        ], ';');
                    }
                    break;

                case 'stock_movement':
                    fputcsv($file, ['Data/Hora', 'Produto', 'SKU', 'Tipo', 'Quantidade', 'Usuário', 'Referência / Nota', 'Observações'], ';');
                    foreach ($results as $m) {
                        fputcsv($file, [
                            $m->created_at->format('d/m/Y H:i:s'),
                            $m->product ? $m->product->name : 'Produto Excluído',
                            $m->product ? $m->product->sku : '',
                            ucfirst($m->type),
                            $m->quantity,
                            $m->user ? $m->user->name : 'Sistema',
                            $m->reference ?? 'N/A',
                            $m->notes ?? '',
                        ], ';');
                    }
                    break;

                case 'billing':
                    fputcsv($file, ['Número da Nota', 'Série', 'Destinatário', 'CNPJ/CPF', 'Data Emissão', 'Subtotal (R$)', 'Desconto (R$)', 'Frete (R$)', 'Total Geral (R$)', 'Status'], ';');
                    foreach ($results as $inv) {
                        fputcsv($file, [
                            $inv->number,
                            $inv->series,
                            $inv->recipient_name,
                            $inv->recipient_document,
                            $inv->issued_at ? $inv->issued_at->format('d/m/Y') : 'N/A',
                            number_format($inv->subtotal, 2, ',', ''),
                            number_format($inv->discount, 2, ',', ''),
                            number_format($inv->shipping, 2, ',', ''),
                            number_format($inv->total, 2, ',', ''),
                            ucfirst($inv->status),
                        ], ';');
                    }
                    break;

                case 'low_stock':
                    fputcsv($file, ['Nome do Produto', 'SKU', 'Estoque Atual', 'Ponto de Ressuprimento', 'Estoque Máximo', 'Localização'], ';');
                    foreach ($results as $p) {
                        fputcsv($file, [
                            $p->name,
                            $p->sku,
                            $p->quantity,
                            $p->reorder_level ?? 0,
                            $p->max_stock ?? 0,
                            $p->location ? $p->location->code : 'Não Alocado',
                        ], ';');
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
