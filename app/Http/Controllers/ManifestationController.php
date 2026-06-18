<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomingInvoice;
use App\Models\IncomingInvoiceItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\Logger;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ManifestationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manifestacoes.gerenciar'),
        ];
    }

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

        Logger::log('manifest_action', "O usuário alterou o status da NF-e #{$manifestation->number} para: " . strtoupper($request->status));

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
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NONET);
            libxml_clear_errors();

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
            
            if (!$supplier) {
                // Auto register supplier
                $supplier = Supplier::create([
                    'name' => $supplierName,
                    'cnpj' => $cnpj,
                    'email' => 'compras@' . Str::slug($supplierName) . '.com.br',
                    'phone' => '(00) 00000-0000',
                    'street' => 'Rua do Fornecedor',
                    'number' => '100',
                    'neighborhood' => 'Distrito Industrial',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'zip_code' => '00000-000',
                    'address' => 'Rua do Fornecedor, 100, Distrito Industrial',
                ]);
            }

            $invoice = IncomingInvoice::create([
                'access_key' => $accessKey,
                'number' => (string) $infNFe->ide->nNF,
                'series' => (string) $infNFe->ide->serie,
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplierName,
                'supplier_cnpj' => $cnpj,
                'emission_date' => substr((string) $infNFe->ide->dhEmi, 0, 10),
                'total_amount' => (float) $infNFe->total->ICMSTot->vNF,
                'xml_data' => $xmlString,
            ]);

            foreach ($infNFe->det as $det) {
                $prod = $det->prod;
                $imposto = $det->imposto ?? null;
                $icms = $imposto->ICMS ?? null;
                $icmsNode = null;
                if ($icms) {
                    foreach (['ICMS00', 'ICMS10', 'ICMS20', 'ICMS70', 'ICMS90'] as $nodeName) {
                        if (isset($icms->{$nodeName})) {
                            $icmsNode = $icms->{$nodeName};
                            break;
                        }
                    }
                }
                
                $ipi = $imposto->IPI->IPITrib ?? null;
                $pis = $imposto->PIS->PISAliq ?? null;
                $cofins = $imposto->COFINS->COFINSAliq ?? null;
                $issqn = $imposto->ISSQN ?? null;
                $retencoes = $imposto->retencoes ?? null;
                $reforma = $imposto->reforma2026 ?? null;

                $invoice->items()->create([
                    'description' => (string) $prod->xProd,
                    'barcode'     => isset($prod->cEAN) && (string)$prod->cEAN != 'SEM GTIN' ? (string)$prod->cEAN : null,
                    'ncm'         => (string) $prod->NCM,
                    'cfop'        => (string) $prod->CFOP,
                    'unit'        => (string) $prod->uCom,
                    'quantity'    => (float) $prod->qCom,
                    'unit_price'  => (float) $prod->vUnCom,
                    'total_price' => (float) $prod->vProd,
                    
                    // Taxes
                    'icms_orig'    => $icmsNode ? (int) $icmsNode->orig : 0,
                    'icms_cst'     => $icmsNode ? (string) $icmsNode->CST : '00',
                    'icms_mod_bc'  => $icmsNode ? (int) ($icmsNode->modBC ?? 3) : 3,
                    'icms_red_bc'  => $icmsNode ? (float) ($icmsNode->pRedBC ?? 0) : 0,
                    'icms_rate'    => $icmsNode ? (float) ($icmsNode->pICMS ?? 0) : 0,
                    
                    'icms_st_cst'  => $icmsNode ? (string) ($icmsNode->CST ?? '10') : '10',
                    'icms_st_rate' => $icmsNode ? (float) ($icmsNode->pICMSST ?? 0) : 0,
                    'icms_st_mva'  => $icmsNode ? (float) ($icmsNode->pMVAST ?? 0) : 0,
                    
                    'ipi_cst'      => $ipi ? (string) $ipi->CST : '50',
                    'ipi_rate'     => $ipi ? (float) $ipi->pIPI : 0,
                    
                    'pis_cst'      => $pis ? (string) $pis->CST : '01',
                    'pis_rate'     => $pis ? (float) $pis->pPIS : 0,
                    
                    'cofins_cst'   => $cofins ? (string) $cofins->CST : '01',
                    'cofins_rate'  => $cofins ? (float) $cofins->pCOFINS : 0,
                    
                    'iss_cst'      => $issqn ? (string) ($issqn->cSitTrib ?? '01') : '01',
                    'iss_rate'     => $issqn ? (float) ($issqn->vAliq ?? 0) : 0,
                    
                    'csll_rate'    => $retencoes ? (float) $retencoes->pCSLL : 0,
                    'irpj_rate'    => $retencoes ? (float) $retencoes->pIRPJ : 0,
                    'cpp_rate'     => $retencoes ? (float) $retencoes->pCPP : 0,
                    
                    'ibs_rate'     => $reforma ? (float) $reforma->pIBS : 0,
                    'cbs_rate'     => $reforma ? (float) $reforma->pCBS : 0,
                    'is_rate'      => $reforma ? (float) $reforma->pIS : 0,
                ]);
            }

            DB::commit();
            Logger::log('xml_import', "O usuário importou o XML da NF-e #{$invoice->number} (Chave: {$invoice->access_key})");
            return redirect()->route('manifestations.show', $invoice)->with('success', 'XML processado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao processar XML.');
        }
    }

    private function mockXmlImport($xmlString)
    {
        // Generates a totally fake invoice if the user uploads a non-standard XML just for simulation
        DB::beginTransaction();
        
        $accessKey = str_pad(rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999) . '1234', 44, '0', STR_PAD_LEFT);
        
        $cnpj = '12.345.678/0001-90';
        $supplier = Supplier::where('cnpj', $cnpj)->orWhere('cnpj', preg_replace('/[^0-9]/', '', $cnpj))->first();
        if (!$supplier) {
            $supplier = Supplier::create([
                'name' => 'Fornecedor Simulado LTDA',
                'cnpj' => $cnpj,
                'email' => 'compras@fornecedorsimulado.com.br',
                'phone' => '(11) 99999-9999',
                'street' => 'Av das Nações Unidas',
                'number' => '1000',
                'neighborhood' => 'Pinheiros',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '05425-070',
                'address' => 'Av das Nações Unidas, 1000, Pinheiros',
            ]);
        }

        $invoice = IncomingInvoice::create([
            'access_key' => $accessKey,
            'number' => rand(1000, 99999),
            'series' => '1',
            'supplier_id' => $supplier->id,
            'supplier_name' => 'Fornecedor Simulado LTDA',
            'supplier_cnpj' => $cnpj,
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
                
                // Simulated taxes
                'icms_orig'    => 0,
                'icms_cst'     => '10',
                'icms_mod_bc'  => 3,
                'icms_red_bc'  => 10.00,
                'icms_rate'    => 18.00,
                'icms_st_cst'  => '10',
                'icms_st_mva'  => 40.00,
                'icms_st_rate' => 12.00,
                'ipi_cst'      => '50',
                'ipi_rate'     => 5.00,
                'pis_cst'      => '01',
                'pis_rate'     => 1.65,
                'cofins_cst'   => '01',
                'cofins_rate'  => 7.60,
                'iss_cst'      => '01',
                'iss_rate'     => 3.00,
                'csll_rate'    => 9.00,
                'irpj_rate'    => 15.00,
                'cpp_rate'     => 20.00,
                'ibs_rate'     => 0.10,
                'cbs_rate'     => 0.90,
                'is_rate'      => 1.50,
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
            
            // Impostos
            $icms_orig = 0;
            $icms_cst = "10";
            $icms_mod_bc = 3;
            $icms_red_bc = 10.00;
            $icms_rate = 18.00;
            $icms_base = $totalItem * (1 - $icms_red_bc / 100);
            $icms_value = $icms_base * $icms_rate / 100;
            
            $icms_st_cst = "10";
            $icms_st_mva = 40.00;
            $icms_st_rate = 12.00;
            $icms_st_base = $totalItem * (1 + $icms_st_mva / 100);
            $icms_st_value = max(0, ($icms_st_base * $icms_st_rate / 100) - $icms_value);
            
            $ipi_cst = "50";
            $ipi_enq = "999";
            $ipi_rate = 5.00;
            $ipi_value = $totalItem * $ipi_rate / 100;
            
            $pis_cst = "01";
            $pis_rate = 1.65;
            $pis_value = $totalItem * $pis_rate / 100;
            
            $cofins_cst = "01";
            $cofins_rate = 7.60;
            $cofins_value = $totalItem * $cofins_rate / 100;
            
            $iss_cst = "01";
            $iss_rate = 3.00;
            $iss_value = $totalItem * $iss_rate / 100;
            
            $csll_rate = 9.00;
            $csll_value = $totalItem * $csll_rate / 100;
            
            $irpj_rate = 15.00;
            $irpj_value = $totalItem * $irpj_rate / 100;
            
            $cpp_rate = 20.00;
            $cpp_value = $totalItem * $cpp_rate / 100;
            
            $ibs_rate = 0.10;
            $ibs_value = $totalItem * $ibs_rate / 100;
            
            $cbs_rate = 0.90;
            $cbs_value = $totalItem * $cbs_rate / 100;
            
            $is_rate = 1.50;
            $is_value = $totalItem * $is_rate / 100;

            $xml .= "        <imposto>\n";
            $xml .= "          <ICMS>\n";
            $xml .= "            <ICMS10>\n";
            $xml .= "              <orig>{$icms_orig}</orig>\n";
            $xml .= "              <CST>{$icms_cst}</CST>\n";
            $xml .= "              <modBC>{$icms_mod_bc}</modBC>\n";
            $xml .= "              <pRedBC>" . number_format($icms_red_bc, 2, '.', '') . "</pRedBC>\n";
            $xml .= "              <vBC>" . number_format($icms_base, 2, '.', '') . "</vBC>\n";
            $xml .= "              <pICMS>" . number_format($icms_rate, 2, '.', '') . "</pICMS>\n";
            $xml .= "              <vICMS>" . number_format($icms_value, 2, '.', '') . "</vICMS>\n";
            $xml .= "              <modBCST>4</modBCST>\n";
            $xml .= "              <pMVAST>" . number_format($icms_st_mva, 2, '.', '') . "</pMVAST>\n";
            $xml .= "              <vBCST>" . number_format($icms_st_base, 2, '.', '') . "</vBCST>\n";
            $xml .= "              <pICMSST>" . number_format($icms_st_rate, 2, '.', '') . "</pICMSST>\n";
            $xml .= "              <vICMSST>" . number_format($icms_st_value, 2, '.', '') . "</vICMSST>\n";
            $xml .= "            </ICMS10>\n";
            $xml .= "          </ICMS>\n";
            $xml .= "          <IPI>\n";
            $xml .= "            <cEnq>{$ipi_enq}</cEnq>\n";
            $xml .= "            <IPITrib>\n";
            $xml .= "              <CST>{$ipi_cst}</CST>\n";
            $xml .= "              <vBC>" . number_format($totalItem, 2, '.', '') . "</vBC>\n";
            $xml .= "              <pIPI>" . number_format($ipi_rate, 2, '.', '') . "</pIPI>\n";
            $xml .= "              <vIPI>" . number_format($ipi_value, 2, '.', '') . "</vIPI>\n";
            $xml .= "            </IPITrib>\n";
            $xml .= "          </IPI>\n";
            $xml .= "          <PIS>\n";
            $xml .= "            <PISAliq>\n";
            $xml .= "              <CST>{$pis_cst}</CST>\n";
            $xml .= "              <vBC>" . number_format($totalItem, 2, '.', '') . "</vBC>\n";
            $xml .= "              <pPIS>" . number_format($pis_rate, 2, '.', '') . "</pPIS>\n";
            $xml .= "              <vPIS>" . number_format($pis_value, 2, '.', '') . "</vPIS>\n";
            $xml .= "            </PISAliq>\n";
            $xml .= "          </PIS>\n";
            $xml .= "          <COFINS>\n";
            $xml .= "            <COFINSAliq>\n";
            $xml .= "              <CST>{$cofins_cst}</CST>\n";
            $xml .= "              <vBC>" . number_format($totalItem, 2, '.', '') . "</vBC>\n";
            $xml .= "              <pCOFINS>" . number_format($cofins_rate, 2, '.', '') . "</pCOFINS>\n";
            $xml .= "              <vCOFINS>" . number_format($cofins_value, 2, '.', '') . "</vCOFINS>\n";
            $xml .= "            </COFINSAliq>\n";
            $xml .= "          </COFINS>\n";
            $xml .= "          <ISSQN>\n";
            $xml .= "            <cSitTrib>{$iss_cst}</cSitTrib>\n";
            $xml .= "            <vBC>" . number_format($totalItem, 2, '.', '') . "</vBC>\n";
            $xml .= "            <vAliq>" . number_format($iss_rate, 2, '.', '') . "</vAliq>\n";
            $xml .= "            <vISSQN>" . number_format($iss_value, 2, '.', '') . "</vISSQN>\n";
            $xml .= "          </ISSQN>\n";
            $xml .= "          <retencoes>\n";
            $xml .= "            <pCSLL>" . number_format($csll_rate, 2, '.', '') . "</pCSLL>\n";
            $xml .= "            <vCSLL>" . number_format($csll_value, 2, '.', '') . "</vCSLL>\n";
            $xml .= "            <pIRPJ>" . number_format($irpj_rate, 2, '.', '') . "</pIRPJ>\n";
            $xml .= "            <vIRPJ>" . number_format($irpj_value, 2, '.', '') . "</vIRPJ>\n";
            $xml .= "            <pCPP>" . number_format($cpp_rate, 2, '.', '') . "</pCPP>\n";
            $xml .= "            <vCPP>" . number_format($cpp_value, 2, '.', '') . "</vCPP>\n";
            $xml .= "          </retencoes>\n";
            $xml .= "          <reforma2026>\n";
            $xml .= "            <pIBS>" . number_format($ibs_rate, 2, '.', '') . "</pIBS>\n";
            $xml .= "            <vIBS>" . number_format($ibs_value, 2, '.', '') . "</vIBS>\n";
            $xml .= "            <pCBS>" . number_format($cbs_rate, 2, '.', '') . "</pCBS>\n";
            $xml .= "            <vCBS>" . number_format($cbs_value, 2, '.', '') . "</vCBS>\n";
            $xml .= "            <pIS>" . number_format($is_rate, 2, '.', '') . "</pIS>\n";
            $xml .= "            <vIS>" . number_format($is_value, 2, '.', '') . "</vIS>\n";
            $xml .= "          </reforma2026>\n";
            $xml .= "        </imposto>\n";
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

    /**
     * Tela de conferência interativa de entrada (recebimento)
     */
    public function conferWorkflow(IncomingInvoice $manifestation)
    {
        $manifestation->load(['supplier', 'items']);
        return view('manifestations.confer', compact('manifestation'));
    }

    /**
     * Salvar resultado da conferência de entrada
     */
    public function conferSave(Request $request, IncomingInvoice $manifestation)
    {
        $request->validate([
            'checked_quantities' => 'required|array',
            'checked_quantities.*' => 'numeric|min:0',
        ]);

        $manifestation->load('items');

        $hasDivergence = false;
        $divergences = [];

        foreach ($manifestation->items as $item) {
            $checked = (float) ($request->checked_quantities[$item->id] ?? 0);
            $item->update(['checked_quantity' => $checked]);

            if (abs($checked - (float)$item->quantity) > 0.001) {
                $hasDivergence = true;
                $diff = $checked - (float)$item->quantity;
                $type = $diff > 0 ? 'EXCESSO' : 'FALTA';
                $divergences[] = "{$item->description}: {$type} de " . abs($diff) . " {$item->unit}";
            }
        }

        $status = $hasDivergence ? 'Divergente' : 'Conferida';
        $notes = $hasDivergence
            ? "Divergências encontradas:\n" . implode("\n", $divergences)
            : 'Conferência realizada sem divergências.';

        $manifestation->update([
            'conference_status' => $status,
            'conference_notes'  => $notes,
            'conferred_by'      => auth()->id(),
            'conferred_at'      => now(),
        ]);

        Logger::log('confer_incoming', "O usuário realizou a conferência da NF-e de entrada #{$manifestation->number} com status: {$status}");

        return redirect()->route('manifestations.show', $manifestation)
            ->with('success', "Conferência finalizada com status: {$status}");
    }
}
