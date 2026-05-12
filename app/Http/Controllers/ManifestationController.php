<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomingInvoice;
use App\Models\IncomingInvoiceItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManifestationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $date_start = $request->query('date_start');
        $date_end = $request->query('date_end');

        $query = IncomingInvoice::with('supplier')->latest('emission_date');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('access_key', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%")
                  ->orWhere('supplier_cnpj', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('manifestation_status', $status);
        }

        if ($date_start) {
            $query->where('emission_date', '>=', $date_start);
        }

        if ($date_end) {
            $query->where('emission_date', '<=', $date_end);
        }

        $invoices = $query->paginate(15)->withQueryString();

        return view('manifestations.index', compact('invoices', 'search', 'status', 'date_start', 'date_end'));
    }

    public function show(IncomingInvoice $manifestation)
    {
        $manifestation->load(['supplier', 'items']);
        return view('manifestations.show', compact('manifestation'));
    }

    public function manifest(Request $request, IncomingInvoice $manifestation)
    {
        $request->validate([
            'status' => 'required|in:ciencia,confirmada,desconhecimento,nao_realizada'
        ]);

        $manifestation->update([
            'manifestation_status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Manifestação registrada com sucesso!');
    }

    public function danfe(IncomingInvoice $manifestation)
    {
        $manifestation->load('items');
        return view('manifestations.danfe', compact('manifestation'));
    }

    public function uploadXml(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml,txt|max:2048' // Some browsers detect XML as txt
        ]);

        try {
            $xmlString = file_get_contents($request->file('xml_file')->getRealPath());
            $xml = simplexml_load_string($xmlString);

            if ($xml === false) {
                return redirect()->back()->with('error', 'Arquivo XML inválido.');
            }

            // Simulate fetching NFe data (fake paths for simulation)
            // Real XML would use: $xml->NFe->infNFe...
            // We'll mock reading to allow testing with simple generic XMLs, but let's try to read standard nfe tags if they exist.
            
            $infNFe = $xml->NFe->infNFe ?? null;
            
            if (!$infNFe && isset($xml->infNFe)) {
                $infNFe = $xml->infNFe;
            }

            if (!$infNFe) {
                // If not a real SEFAZ XML, generate mock data based on the file name or random
                return $this->mockXmlImport($xmlString);
            }

            DB::beginTransaction();

            // Extract Access Key
            $accessKey = (string) $infNFe['Id'];
            $accessKey = str_replace('NFe', '', $accessKey);
            
            // Check if exists
            if (IncomingInvoice::where('access_key', $accessKey)->exists()) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Esta Nota Fiscal já foi importada.');
            }

            // Supplier Data
            $cnpj = (string) $infNFe->emit->CNPJ;
            $supplierName = (string) $infNFe->emit->xNome;
            
            // Find supplier in DB
            $supplier = Supplier::where('cnpj', $cnpj)->orWhere('cnpj', preg_replace('/[^0-9]/', '', $cnpj))->first();

            $invoice = IncomingInvoice::create([
                'access_key' => $accessKey,
                'number' => (string) $infNFe->ide->nNF,
                'series' => (string) $infNFe->ide->serie,
                'supplier_id' => $supplier ? $supplier->id : null,
                'supplier_name' => $supplierName,
                'supplier_cnpj' => $cnpj,
                'emission_date' => substr((string) $infNFe->ide->dhEmi, 0, 10),
                'total_amount' => (float) $infNFe->total->ICMSTot->vNF,
                'xml_data' => $xmlString,
            ]);

            foreach ($infNFe->det as $det) {
                $prod = $det->prod;
                $invoice->items()->create([
                    'description' => (string) $prod->xProd,
                    'barcode'     => isset($prod->cEAN) && (string)$prod->cEAN != 'SEM GTIN' ? (string)$prod->cEAN : null,
                    'ncm'         => (string) $prod->NCM,
                    'cfop'        => (string) $prod->CFOP,
                    'unit'        => (string) $prod->uCom,
                    'quantity'    => (float) $prod->qCom,
                    'unit_price'  => (float) $prod->vUnCom,
                    'total_price' => (float) $prod->vProd,
                ]);
            }

            DB::commit();
            return redirect()->route('manifestations.show', $invoice)->with('success', 'XML processado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao processar XML: ' . $e->getMessage());
        }
    }

    private function mockXmlImport($xmlString)
    {
        // Generates a totally fake invoice if the user uploads a non-standard XML just for simulation
        DB::beginTransaction();
        
        $accessKey = str_pad(rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999) . '1234', 44, '0', STR_PAD_LEFT);
        
        $invoice = IncomingInvoice::create([
            'access_key' => $accessKey,
            'number' => rand(1000, 99999),
            'series' => '1',
            'supplier_name' => 'Fornecedor Simulado LTDA',
            'supplier_cnpj' => '12.345.678/0001-90',
            'emission_date' => now()->toDateString(),
            'total_amount' => 0,
            'xml_data' => $xmlString,
        ]);

        $numItems = rand(1, 5);
        $total = 0;
        for ($i=1; $i<=$numItems; $i++) {
            $qty = rand(10, 50);
            $price = rand(10, 100);
            $totalItem = $qty * $price;
            $total += $totalItem;
            
            $invoice->items()->create([
                'description' => 'Produto Simulado ' . $i . ' (Fallback)',
                'barcode'     => (string) rand(7890000000000, 7899999999999),
                'ncm'         => '12345678',
                'cfop'        => '5102',
                'unit'        => 'UN',
                'quantity'    => $qty,
                'unit_price'  => $price,
                'total_price' => $totalItem,
            ]);
        }

        $invoice->update(['total_amount' => $total]);

        DB::commit();
        return redirect()->route('manifestations.show', $invoice)->with('success', 'XML simulado importado com sucesso!');
    }

    public function generateXml()
    {
        $accessKey = str_pad(rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999) . '1234', 44, '0', STR_PAD_LEFT);
        $number = rand(1000, 99999);
        $date = now()->format('Y-m-d\TH:i:sP');
        
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<nfeProc versao=\"4.00\" xmlns=\"http://www.portalfiscal.inf.br/nfe\">\n";
        $xml .= "  <NFe xmlns=\"http://www.portalfiscal.inf.br/nfe\">\n";
        $xml .= "    <infNFe Id=\"NFe{$accessKey}\" versao=\"4.00\">\n";
        $xml .= "      <ide>\n";
        $xml .= "        <nNF>{$number}</nNF>\n";
        $xml .= "        <serie>1</serie>\n";
        $xml .= "        <dhEmi>{$date}</dhEmi>\n";
        $xml .= "      </ide>\n";
        $xml .= "      <emit>\n";
        $xml .= "        <CNPJ>12345678000190</CNPJ>\n";
        $xml .= "        <xNome>FORNECEDOR SIMULADO DE TESTE LTDA</xNome>\n";
        $xml .= "      </emit>\n";
        $xml .= "      <dest>\n";
        $xml .= "        <CNPJ>00000000000100</CNPJ>\n";
        $xml .= "        <xNome>LOGISYNC WMS</xNome>\n";
        $xml .= "      </dest>\n";
        
        $itemsCount = rand(1, 4);
        $total = 0;
        
        for ($i = 1; $i <= $itemsCount; $i++) {
            $qty = rand(10, 50);
            $price = rand(5, 100);
            $totalItem = $qty * $price;
            $total += $totalItem;
            
            $barcode = rand(7890000000000, 7899999999999);

            $xml .= "      <det nItem=\"{$i}\">\n";
            $xml .= "        <prod>\n";
            $xml .= "          <cProd>PROD-".str_pad($i, 3, '0', STR_PAD_LEFT)."</cProd>\n";
            $xml .= "          <cEAN>{$barcode}</cEAN>\n";
            $xml .= "          <xProd>Produto Fictício {$i} - Importação em Lote</xProd>\n";
            $xml .= "          <NCM>12345678</NCM>\n";
            $xml .= "          <CFOP>5102</CFOP>\n";
            $xml .= "          <uCom>UN</uCom>\n";
            $xml .= "          <qCom>" . number_format($qty, 4, '.', '') . "</qCom>\n";
            $xml .= "          <vUnCom>" . number_format($price, 4, '.', '') . "</vUnCom>\n";
            $xml .= "          <vProd>" . number_format($totalItem, 2, '.', '') . "</vProd>\n";
            $xml .= "        </prod>\n";
            $xml .= "      </det>\n";
        }
        
        $xml .= "      <total>\n";
        $xml .= "        <ICMSTot>\n";
        $xml .= "          <vNF>" . number_format($total, 2, '.', '') . "</vNF>\n";
        $xml .= "        </ICMSTot>\n";
        $xml .= "      </total>\n";
        $xml .= "    </infNFe>\n";
        $xml .= "  </NFe>\n";
        $xml .= "</nfeProc>";

        return response($xml)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="nfe_mock_'.$accessKey.'.xml"');
    }
}
