<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Etiquetas LogiSync</title>
    <style>
        @page { 
            size: A4 portrait;
            margin: 15mm 10mm; 
        }
        body { 
            font-family: 'Helvetica', sans-serif; 
            margin: 0; 
            padding: 0; 
            background: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        tr {
            page-break-inside: avoid;
        }
        td {
            width: 33.33%;
            padding: 2mm;
            vertical-align: top;
            box-sizing: border-box;
        }
        .label-box {
            border: 1px solid #cccccc;
            border-radius: 8px;
            padding: 4mm 3mm;
            text-align: center;
            height: 38mm;
            box-sizing: border-box;
            background: #ffffff;
        }
        .product-name {
            font-size: 10px;
            font-weight: bold;
            height: 24px;
            overflow: hidden;
            margin-bottom: 4px;
            color: #111111;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .barcode-lines {
            font-size: 10px;
            letter-spacing: 1.5px;
            font-weight: normal;
            height: 14px;
            overflow: hidden;
            margin-bottom: 2px;
            border-bottom: 1px solid #000000;
        }
        .barcode-text {
            font-size: 9px;
            color: #444444;
            margin-bottom: 4px;
            font-family: monospace;
        }
        .price {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
        }
        .footer {
            font-size: 7px;
            color: #777777;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <table>
        @foreach(collect($products)->chunk(3) as $chunk)
            <tr>
                @foreach($chunk as $product)
                    <td>
                        <div class="label-box">
                            <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                            
                            {{-- Mock Barcode lines --}}
                            <div class="barcode-lines">
                                ||||||||||||||||||||||||||||||||||||||||||||
                            </div>
                            
                            <div class="barcode-text">{{ $product->barcode ?? $product->sku }}</div>
                            
                            <div class="price">R$ {{ number_format($product->unit_price, 2, ',', '.') }}</div>
                            
                            <div class="footer">LogiSync WMS - Premium System</div>
                        </div>
                    </td>
                @endforeach
                {{-- Fill remaining cells --}}
                @for($i = count($chunk); $i < 3; $i++)
                    <td></td>
                @endfor
            </tr>
        @endforeach
    </table>
</body>
</html>
