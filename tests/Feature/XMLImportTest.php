<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\IncomingInvoice;
use App\Models\IncomingInvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

test('xml import parses all taxations and product info correctly', function () {
    // 1. Create an admin user to bypass permission checking
    $adminRole = Role::create(['name' => 'admin', 'description' => 'Administrator']);
    $user = User::factory()->create(['role_id' => $adminRole->id]);
    $this->actingAs($user);

    // 2. Define standard NFe XML structure with comprehensive taxes
    $accessKey = '35260612345678000190550010000012341234567890';
    $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>
    <nfeProc versao="4.00" xmlns="http://www.portalfiscal.inf.br/nfe">
      <NFe>
        <infNFe Id="NFe' . $accessKey . '" versao="4.00">
          <ide>
            <nNF>1234</nNF>
            <serie>1</serie>
            <dhEmi>2026-06-18T12:00:00-03:00</dhEmi>
          </ide>
          <emit>
            <CNPJ>12345678000190</CNPJ>
            <xNome>FORNECEDOR DE TESTE LTDA</xNome>
          </emit>
          <dest>
            <CNPJ>00000000000100</CNPJ>
            <xNome>LOGISYNC WMS</xNome>
          </dest>
          <det nItem="1">
            <prod>
              <cProd>PROD-XML-TEST</cProd>
              <cEAN>7891234567890</cEAN>
              <xProd>Produto Teste Importacao</xProd>
              <NCM>85285220</NCM>
              <CEST>0100100</CEST>
              <CFOP>5102</CFOP>
              <uCom>UN</uCom>
              <qCom>25.0000</qCom>
              <vUnCom>15.5000</vUnCom>
              <vProd>387.50</vProd>
            </prod>
            <imposto>
              <ICMS>
                <ICMS10>
                  <orig>0</orig>
                  <CST>10</CST>
                  <modBC>3</modBC>
                  <pRedBC>5.00</pRedBC>
                  <vBC>368.12</vBC>
                  <pICMS>18.00</pICMS>
                  <vICMS>66.26</vICMS>
                  <modBCST>4</modBCST>
                  <pMVAST>40.00</pMVAST>
                  <vBCST>515.37</vBCST>
                  <pICMSST>12.00</pICMSST>
                  <vICMSST>61.84</vICMSST>
                </ICMS10>
              </ICMS>
              <IPI>
                <cEnq>999</cEnq>
                <IPITrib>
                  <CST>50</CST>
                  <vBC>387.50</vBC>
                  <pIPI>5.00</pIPI>
                  <vIPI>19.38</vIPI>
                </IPITrib>
              </IPI>
              <PIS>
                <PISAliq>
                  <CST>01</CST>
                  <vBC>387.50</vBC>
                  <pPIS>1.65</pPIS>
                  <vPIS>6.39</vPIS>
                </PISAliq>
              </PIS>
              <COFINS>
                <COFINSAliq>
                  <CST>01</CST>
                  <vBC>387.50</vBC>
                  <pCOFINS>7.60</pCOFINS>
                  <vCOFINS>29.45</vCOFINS>
                </COFINSAliq>
              </COFINS>
              <ISSQN>
                <cSitTrib>01</cSitTrib>
                <vBC>387.50</vBC>
                <vAliq>3.00</vAliq>
                <vISSQN>11.63</vISSQN>
              </ISSQN>
              <retencoes>
                <pCSLL>9.00</pCSLL>
                <vCSLL>34.88</vCSLL>
                <pIRPJ>15.00</pIRPJ>
                <vIRPJ>58.13</vIRPJ>
                <pCPP>20.00</pCPP>
                <vCPP>77.50</vCPP>
              </retencoes>
              <reforma2026>
                <pIBS>0.10</pIBS>
                <vIBS>0.39</vIBS>
                <pCBS>0.90</pCBS>
                <vCBS>3.49</vCBS>
                <pIS>1.50</pIS>
                <vIS>5.81</vIS>
              </reforma2026>
            </imposto>
          </det>
          <total>
            <ICMSTot>
              <vNF>387.50</vNF>
            </ICMSTot>
          </total>
        </infNFe>
      </NFe>
    </nfeProc>';

    $file = UploadedFile::fake()->createWithContent('nfe.xml', $xmlContent);

    // 3. Post XML upload
    $response = $this->post(route('manifestations.uploadXml'), [
        'xml_file' => $file,
    ]);

    $response->assertRedirect();

    // 4. Assert IncomingInvoice and Item were created with parsed values
    $invoice = IncomingInvoice::where('access_key', $accessKey)->first();
    expect($invoice)->not->toBeNull();
    expect($invoice->number)->toBe('1234');
    expect($invoice->supplier_name)->toBe('FORNECEDOR DE TESTE LTDA');

    $item = $invoice->items()->first();
    expect($item)->not->toBeNull();
    expect($item->product_code)->toBe('PROD-XML-TEST');
    expect($item->cest)->toBe('0100100');
    expect($item->ncm)->toBe('85285220');
    expect($item->cfop)->toBe('5102');
    expect($item->unit)->toBe('UN');
    expect($item->quantity)->toBe('25.0000');
    expect($item->unit_price)->toBe('15.5000');

    // Tax rates checks
    expect((float)$item->icms_rate)->toBe(18.00);
    expect((float)$item->icms_red_bc)->toBe(5.00);
    expect((float)$item->icms_st_rate)->toBe(12.00);
    expect((float)$item->icms_st_mva)->toBe(40.00);
    expect((float)$item->ipi_rate)->toBe(5.00);
    expect((float)$item->pis_rate)->toBe(1.65);
    expect((float)$item->cofins_rate)->toBe(7.60);
    expect((float)$item->iss_rate)->toBe(3.00);
    expect((float)$item->csll_rate)->toBe(9.00);
    expect((float)$item->irpj_rate)->toBe(15.00);
    expect((float)$item->cpp_rate)->toBe(20.00);
    expect((float)$item->ibs_rate)->toBe(0.10);
    expect((float)$item->cbs_rate)->toBe(0.90);
    expect((float)$item->is_rate)->toBe(1.50);

    // 5. Perform conference
    $this->post(route('manifestations.confer-save', $invoice), [
        'checked_quantities' => [
            $item->id => 25.0000
        ]
    ]);
    
    $invoice->refresh();
    expect($invoice->conference_status)->toBe('Conferida');

    // 6. Map to a NEW product
    $this->post(route('inventory.bulkStore', $invoice), [
        'items' => [
            $item->id => [
                'product_id' => 'new',
                'new_name' => 'Novo Produto Via XML',
                'new_barcode' => '7891234567890',
                'new_category' => 'Eletronicos',
                'quantity' => 25.0
            ]
        ]
    ]);

    // 7. Verify new product is created with all XML taxes and information
    $product = Product::where('barcode', '7891234567890')->first();
    expect($product)->not->toBeNull();
    expect($product->name)->toBe('Novo Produto Via XML');
    expect($product->category)->toBe('Eletronicos');
    expect($product->cest)->toBe('0100100');
    expect($product->ncm)->toBe('85285220');
    expect($product->cfop_default)->toBe('5102');
    expect((float)$product->unit_price)->toBe(15.50);
    expect((float)$product->purchase_price)->toBe(15.50);
    expect((float)$product->cost_price)->toBe(15.50);

    expect((float)$product->icms_rate)->toBe(18.00);
    expect((float)$product->icms_red_bc)->toBe(5.00);
    expect((float)$product->icms_st_rate)->toBe(12.00);
    expect((float)$product->icms_st_mva)->toBe(40.00);
    expect((float)$product->ipi_rate)->toBe(5.00);
    expect((float)$product->pis_rate)->toBe(1.65);
    expect((float)$product->cofins_rate)->toBe(7.60);
    expect((float)$product->iss_rate)->toBe(3.00);
    expect((float)$product->csll_rate)->toBe(9.00);
    expect((float)$product->irpj_rate)->toBe(15.00);
    expect((float)$product->cpp_rate)->toBe(20.00);
    expect((float)$product->ibs_rate)->toBe(0.10);
    expect((float)$product->cbs_rate)->toBe(0.90);
    expect((float)$product->is_rate)->toBe(1.50);

    // 8. Test mapping to an EXISTING product (and updating all properties)
    // Create an existing product with blank/zero tax rates
    $existingProduct = Product::create([
        'name' => 'Produto Antigo Existente',
        'sku' => 'SKU-OLD-01',
        'barcode' => '', // empty barcode
        'unit_price' => 5.00,
        'purchase_price' => 5.00,
        'cost_price' => 5.00,
        'quantity' => 0,
        'reorder_level' => 10,
        'status' => 'ativo',
        'ncm' => '00000000',
        'cest' => '',
        'icms_rate' => 0.00,
    ]);

    // Setup another invoice with access key 2 and different taxes
    $accessKey2 = '35260612345678000190550010000012341234567899';
    $xmlContent2 = str_replace([$accessKey, '<nNF>1234</nNF>', '<cProd>PROD-XML-TEST</cProd>', '<vUnCom>15.5000</vUnCom>', '<pICMS>18.00</pICMS>', '<CEST>0100100</CEST>', '<NCM>85285220</NCM>', '<cEAN>7891234567890</cEAN>'], 
                               [$accessKey2, '<nNF>5678</nNF>', '<cProd>PROD-XML-TEST-2</cProd>', '<vUnCom>35.0000</vUnCom>', '<pICMS>12.00</pICMS>', '<CEST>9999999</CEST>', '<NCM>99999999</NCM>', '<cEAN>7891234567899</cEAN>'], 
                               $xmlContent);

    $file2 = UploadedFile::fake()->createWithContent('nfe2.xml', $xmlContent2);

    $this->post(route('manifestations.uploadXml'), [
        'xml_file' => $file2,
    ]);

    $invoice2 = IncomingInvoice::where('access_key', $accessKey2)->first();
    $item2 = $invoice2->items()->first();

    // Confer second invoice
    $this->post(route('manifestations.confer-save', $invoice2), [
        'checked_quantities' => [
            $item2->id => 25.0000
        ]
    ]);

    expect($item2->barcode)->toBe('7891234567899');

    // Map to the existing product
    $response2 = $this->post(route('inventory.bulkStore', $invoice2), [
        'items' => [
            $item2->id => [
                'product_id' => (string) $existingProduct->id,
                'quantity' => 25.0
            ]
        ]
    ]);
    
    $response2->assertSessionHasNoErrors();
    $response2->assertRedirect(route('manifestations.show', $invoice2));

    // 9. Verify the existing product is updated with all second XML taxes and prices
    $existingProduct->refresh();
    expect($existingProduct->barcode)->toBe('7891234567899'); // updated barcode from XML
    expect($existingProduct->cest)->toBe('9999999');
    expect($existingProduct->ncm)->toBe('99999999');
    expect((float)$existingProduct->unit_price)->toBe(35.00);
    expect((float)$existingProduct->purchase_price)->toBe(35.00);
    expect((float)$existingProduct->cost_price)->toBe(35.00);
    expect((float)$existingProduct->icms_rate)->toBe(12.00);
});

test('outbound invoice calculates total including IPI and ICMS ST correctly', function () {
    $adminRole = Role::create(['name' => 'admin', 'description' => 'Administrator']);
    $user = User::factory()->create(['role_id' => $adminRole->id]);
    $this->actingAs($user);

    $product = Product::create([
        'name'          => 'Produto Teste Calculo',
        'sku'           => 'SKU-CALC-01',
        'barcode'       => '7891111111111',
        'unit_price'    => 100.00,
        'quantity'      => 10,
        'reorder_level' => 2,
        'status'        => 'ativo',
    ]);

    $invoiceService = app(\App\Services\InvoiceService::class);
    
    // Simulate data sent from form (including IPI and ICMS ST calculations)
    // Subtotal = 2 * 100.00 = 200.00
    // IPI value = 5% of 200.00 = 10.00
    // ICMS value = 12% of 200.00 = 24.00
    // ICMS ST base = (200.00 + 10.00) * 1.40 (40% MVA) = 294.00
    // ICMS ST value = (294.00 * 18%) - 24.00 = 28.92
    $data = [
        'number'             => 'NF-2026-99999',
        'series'             => '001',
        'type'               => 'saida',
        'recipient_name'     => 'Cliente Teste de Calculo',
        'recipient_document' => '111.111.111-11',
        'payment_method'     => 'pix',
        'discount'           => 0,
        'shipping'           => 0,
        'items'              => [
            [
                'product_id'    => $product->id,
                'description'   => $product->name,
                'quantity'      => 2,
                'unit_price'    => 100.00,
                'discount'      => 0,
                
                'icms_cst'      => '00',
                'icms_orig'     => 0,
                'icms_mod_bc'   => 3,
                'icms_red_bc'   => 0,
                'icms_base'     => 200.00,
                'icms_rate'     => 12.00,
                'icms_value'    => 24.00,
                
                'icms_st_cst'   => '10',
                'icms_st_mva'   => 40.00,
                'icms_st_base'  => 294.00,
                'icms_st_rate'  => 18.00,
                'icms_st_value' => 28.92,
                
                'ipi_cst'       => '50',
                'ipi_enq'       => '999',
                'ipi_base'      => 200.00,
                'ipi_rate'      => 5.00,
                'ipi_value'     => 10.00,
            ]
        ]
    ];

    $invoice = $invoiceService->processInvoice($data, null, false);

    expect($invoice->subtotal)->toBe('200.00');
    // Total should be: subtotal (200.00) + ICMS ST (28.92) + IPI (10.00) = 238.92
    expect($invoice->total)->toBe('238.92');
});

test('xml import automatically matches existing product and increments stock', function () {
    $adminRole = Role::create(['name' => 'admin', 'description' => 'Administrator']);
    $user = User::factory()->create(['role_id' => $adminRole->id]);
    $this->actingAs($user);

    // Create an existing product with the barcode from the XML
    $product = Product::create([
        'name' => 'Produto Existente',
        'sku' => 'PROD-XML-TEST',
        'barcode' => '7891234567890',
        'unit_price' => 10.00,
        'purchase_price' => 10.00,
        'cost_price' => 10.00,
        'quantity' => 5,
        'reorder_level' => 2,
        'status' => 'ativo',
    ]);

    $accessKey = '35260612345678000190550010000012341234567890';
    $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>
    <nfeProc versao="4.00" xmlns="http://www.portalfiscal.inf.br/nfe">
      <NFe>
        <infNFe Id="NFe' . $accessKey . '" versao="4.00">
          <ide>
            <nNF>1234</nNF>
            <serie>1</serie>
            <dhEmi>2026-06-18T12:00:00-03:00</dhEmi>
          </ide>
          <emit>
            <CNPJ>12345678000190</CNPJ>
            <xNome>FORNECEDOR DE TESTE LTDA</xNome>
          </emit>
          <dest>
            <CNPJ>00000000000100</CNPJ>
            <xNome>LOGISYNC WMS</xNome>
          </dest>
          <det nItem="1">
            <prod>
              <cProd>PROD-XML-TEST</cProd>
              <cEAN>7891234567890</cEAN>
              <xProd>Produto Teste Importacao</xProd>
              <NCM>85285220</NCM>
              <CEST>0100100</CEST>
              <CFOP>5102</CFOP>
              <uCom>UN</uCom>
              <qCom>25.0000</qCom>
              <vUnCom>15.5000</vUnCom>
              <vProd>387.50</vProd>
            </prod>
            <imposto>
              <ICMS>
                <ICMS10>
                  <orig>0</orig>
                  <CST>10</CST>
                  <modBC>3</modBC>
                  <pRedBC>5.00</pRedBC>
                  <vBC>368.12</vBC>
                  <pICMS>18.00</pICMS>
                  <vICMS>66.26</vICMS>
                  <modBCST>4</modBCST>
                  <pMVAST>40.00</pMVAST>
                  <vBCST>515.37</vBCST>
                  <pICMSST>12.00</pICMSST>
                  <vICMSST>61.84</vICMSST>
                </ICMS10>
              </ICMS>
            </imposto>
          </det>
          <total>
            <ICMSTot>
              <vNF>387.50</vNF>
            </ICMSTot>
          </total>
        </infNFe>
      </NFe>
    </nfeProc>';

    $file = UploadedFile::fake()->createWithContent('nfe.xml', $xmlContent);

    // Post XML upload
    $response = $this->post(route('manifestations.uploadXml'), [
        'xml_file' => $file,
    ]);

    $response->assertRedirect();

    // Verify invoice is imported
    $invoice = IncomingInvoice::where('access_key', $accessKey)->first();
    expect($invoice)->not->toBeNull();
    expect($invoice->entry_status)->toBe('imported');
    expect($invoice->conference_status)->toBe('Conferida');

    // Verify stock is incremented: 5 (initial) + 25 (from XML) = 30
    $product->refresh();
    expect($product->quantity)->toBe(30);

    // Verify Inventory log is created
    $log = \App\Models\Inventory::where('product_id', $product->id)->first();
    expect($log)->not->toBeNull();
    expect($log->quantity)->toBe(25);
    expect($log->type)->toBe('entrada');
    expect($log->status)->toBe('confirmada');
});
