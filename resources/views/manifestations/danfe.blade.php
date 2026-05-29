<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>DANFE Simulado - {{ $manifestation->number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 20px; background: #525659; }
        .danfe-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.5); }
        .box { border: 1px solid #000; border-radius: 3px; padding: 4px; margin-bottom: 4px; position: relative; }
        .box-title { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #333; margin-bottom: 2px; }
        .box-content { font-size: 11px; font-weight: bold; }
        .flex { display: flex; gap: 4px; }
        .flex-1 { flex: 1; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items th, table.items td { border: 1px solid #000; padding: 4px; font-size: 9px; }
        table.items th { font-weight: bold; text-align: center; }
        .title-main { font-size: 14px; font-weight: bold; text-align: center; margin-bottom: 10px; }
        .barcode { margin-top: 10px; text-align: center; font-family: 'Courier New', Courier, monospace; font-size: 14px; font-weight: bold; letter-spacing: 2px; }
    </style>
</head>
<body>

<div class="danfe-container">
    <div class="flex">
        <div class="box" style="width: 50%;">
            <div class="box-title">Identificação do Emitente</div>
            <div class="box-content" style="font-size: 14px;">{{ $manifestation->supplier_name }}</div>
            <div style="font-size: 10px; margin-top: 5px;">
                CNPJ: {{ $manifestation->supplier_cnpj }}<br>
                Fornecedor Simulado do Sistema LogiSync
            </div>
        </div>
        <div class="box" style="width: 20%; text-align: center;">
            <div class="title-main">DANFE</div>
            <div style="font-size: 9px;">Documento Auxiliar da Nota Fiscal Eletrônica</div>
            <div style="margin-top: 10px; font-size: 12px;">
                0 - Entrada<br>
                1 - Saída
            </div>
            <div style="font-size: 16px; font-weight: bold; border: 1px solid #000; display: inline-block; padding: 2px 5px; margin-top: 5px;">0</div>
        </div>
        <div class="box" style="width: 30%;">
            <div class="box-title">Chave de Acesso</div>
            <div class="barcode">
                {{ substr($manifestation->access_key, 0, 4) }}
                {{ substr($manifestation->access_key, 4, 4) }}
                {{ substr($manifestation->access_key, 8, 4) }}
                {{ substr($manifestation->access_key, 12, 4) }}
                {{ substr($manifestation->access_key, 16, 4) }}
                {{ substr($manifestation->access_key, 20, 4) }}
                {{ substr($manifestation->access_key, 24, 4) }}
                {{ substr($manifestation->access_key, 28, 4) }}
                {{ substr($manifestation->access_key, 32, 4) }}
                {{ substr($manifestation->access_key, 36, 4) }}
                {{ substr($manifestation->access_key, 40, 4) }}
            </div>
            <div class="box-title" style="margin-top: 10px; text-align: center;">Consulta de autenticidade no portal nacional da NF-e</div>
        </div>
    </div>

    <div class="flex">
        <div class="box flex-1">
            <div class="box-title">Natureza da Operação</div>
            <div class="box-content">VENDA DE MERCADORIA SIMULADA</div>
        </div>
        <div class="box" style="width: 200px;">
            <div class="box-title">Protocolo de Autorização de Uso</div>
            <div class="box-content">1{{ rand(10000000000000, 99999999999999) }} - {{ $manifestation->emission_date->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="title-main" style="text-align: left; font-size: 10px; margin-top: 10px;">DESTINATÁRIO/REMETENTE</div>
    <div class="flex">
        <div class="box flex-1">
            <div class="box-title">Nome / Razão Social</div>
            <div class="box-content">LogiSync WMS S.A (Empresa Fictícia)</div>
        </div>
        <div class="box" style="width: 150px;">
            <div class="box-title">CNPJ/CPF</div>
            <div class="box-content">00.000.000/0001-00</div>
        </div>
        <div class="box" style="width: 100px;">
            <div class="box-title">Data da Emissão</div>
            <div class="box-content">{{ $manifestation->emission_date->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="title-main" style="text-align: left; font-size: 10px; margin-top: 10px;">CÁLCULO DO IMPOSTO</div>
    <div class="flex">
        <div class="box flex-1">
            <div class="box-title">Base de Cálculo do ICMS</div>
            <div class="box-content text-right">0,00</div>
        </div>
        <div class="box flex-1">
            <div class="box-title">Valor do ICMS</div>
            <div class="box-content text-right">0,00</div>
        </div>
        <div class="box flex-1">
            <div class="box-title">Valor do Frete</div>
            <div class="box-content text-right">0,00</div>
        </div>
        <div class="box flex-1">
            <div class="box-title">Valor Total dos Produtos</div>
            <div class="box-content text-right">{{ number_format($manifestation->total_amount, 2, ',', '.') }}</div>
        </div>
        <div class="box flex-1">
            <div class="box-title">Valor Total da Nota</div>
            <div class="box-content text-right">{{ number_format($manifestation->total_amount, 2, ',', '.') }}</div>
        </div>
    </div>

    <div class="title-main" style="text-align: left; font-size: 10px; margin-top: 10px;">DADOS DOS PRODUTOS / SERVIÇOS</div>
    <table class="items">
        <thead>
            <tr>
                <th>CÓDIGO</th>
                <th>DESCRIÇÃO DO PRODUTO/SERVIÇO</th>
                <th>NCM/SH</th>
                <th>CFOP</th>
                <th>UNID</th>
                <th>QTD.</th>
                <th>VLR. UNIT.</th>
                <th>VLR. TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($manifestation->items as $idx => $item)
            <tr>
                <td>{{ str_pad($idx+1, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->ncm }}</td>
                <td class="text-center">{{ $item->cfop }}</td>
                <td class="text-center">{{ $item->unit }}</td>
                <td class="text-right">{{ number_format($item->quantity, 4, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 4, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->total_price, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #666;">
        Este é um DANFE simulado pelo sistema LogiSync para fins acadêmicos. Não possui valor fiscal real.
    </div>
</div>

<script>
    window.print();
</script>
</body>
</html>
