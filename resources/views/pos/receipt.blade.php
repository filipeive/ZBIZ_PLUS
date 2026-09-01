<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo #{{ $sale->id }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.2;
            width: 78mm;
            margin: 0 auto;
            padding: 5px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 2px 0; }
        @media print {
            .no-print { display: none; }
        }
        .btn-print {
            padding: 8px 16px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
            width: 100%;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">IMPRIMIR RECIBO (80mm)</button>
    </div>

    <div class="text-center">
        <h2 style="margin: 0; font-size: 16px;">{{ $sale->tenant?->name ?? config('app.name', 'ZBIZ+') }}</h2>
        <div>{{ $sale->branch?->name ?? 'Loja Principal' }}</div>
        @if($sale->tenant?->nuit)
            <div>NUIT: {{ $sale->tenant->nuit }}</div>
        @endif
        @if($sale->branch?->phone)
            <div>Tel: {{ $sale->branch->phone }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <div><strong>Doc:</strong> Venda a Dinheiro / Recibo</div>
    <div><strong>Venda Nº:</strong> #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div><strong>Data:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</div>
    <div><strong>Operador:</strong> {{ $sale->user?->name ?? 'Caixa' }}</div>
    <div><strong>Cliente:</strong> {{ $sale->customer_display_name }}</div>
    @if($sale->customer?->nuit)
        <div><strong>NUIT Cliente:</strong> {{ $sale->customer->nuit }}</div>
    @endif

    <div class="divider"></div>

    <table class="table">
        <thead>
            <tr>
                <th style="text-align: left;">Item</th>
                <th class="text-center">Qtd</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td colspan="3" style="font-weight: bold;">{{ $item->product_name }}</td>
                </tr>
                <tr>
                    <td>{{ number_format($item->unit_price, 2, ',', '.') }} MT</td>
                    <td class="text-center">x{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 2, ',', '.') }} MT</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">{{ number_format($sale->subtotal, 2, ',', '.') }} MT</td>
        </tr>
        @if($sale->discount_amount > 0)
        <tr>
            <td>Desconto:</td>
            <td class="text-right">-{{ number_format($sale->discount_amount, 2, ',', '.') }} MT</td>
        </tr>
        @endif
        <tr style="font-size: 14px; font-weight: bold;">
            <td>TOTAL:</td>
            <td class="text-right">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>Pagamento ({{ ucfirst($sale->payment_method) }}):</td>
            <td class="text-right">{{ number_format($sale->amount_paid, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>Troco:</td>
            <td class="text-right">{{ number_format($sale->change_amount, 2, ',', '.') }} MT</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 10px;">
        <p>Obrigado pela preferência!</p>
        <p>Software processado por <strong>ZBIZ+</strong></p>
    </div>

    <script>
        window.onload = function() {
            if (window.location.search.includes('autoprint=1')) {
                window.print();
            }
        };
    </script>
</body>
</html>
