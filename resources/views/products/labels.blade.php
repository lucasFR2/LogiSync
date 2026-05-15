<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Etiquetas LogiSync</title>
    <style>
        @page { margin: 10px; }
        body { 
            font-family: 'Helvetica', sans-serif; 
            margin: 0; 
            padding: 0; 
        }
        .container {
            width: 100%;
        }
        .label-box {
            float: left;
            width: 180px;
            height: 110px;
            padding: 10px;
            margin: 5px;
            border: 1px solid #ddd;
            text-align: center;
            border-radius: 8px;
        }
        .product-name {
            font-size: 11px;
            font-weight: bold;
            height: 28px;
            overflow: hidden;
            margin-bottom: 5px;
            color: #1a1a1a;
        }
        .barcode-img {
            width: 100%;
            height: 35px;
            background: #eee;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            letter-spacing: 2px;
        }
        .barcode-text {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
            font-family: monospace;
        }
        .price {
            font-size: 14px;
            font-weight: 800;
            color: #0F172A;
        }
        .footer {
            font-size: 7px;
            color: #999;
            margin-top: 5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        @foreach($products as $product)
            <div class="label-box">
                <div class="product-name">{{ $product->name }}</div>
                
                {{-- Mock Barcode for now as we don't have a generator library --}}
                <div style="font-size: 8px; border-bottom: 2px solid #000; height: 20px; margin-bottom: 5px;">
                    |||||||||||||||||||||||||||||||||||||||||||
                </div>
                
                <div class="barcode-text">{{ $product->barcode ?? $product->sku }}</div>
                
                <div class="price">R$ {{ number_format($product->unit_price, 2, ',', '.') }}</div>
                
                <div class="footer">LogiSync WMS - Premium System</div>
            </div>
        @endforeach
    </div>
</body>
</html>
