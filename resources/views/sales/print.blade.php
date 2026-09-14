<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sale->official_invoice_title }} #{{ $sale->id }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.25;
            width: 78mm;
            margin: 0 auto;
            padding: 5px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 6px 0; }
        .divider-double { border-top: 2px solid #000; margin: 6px 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 2px 0; }
        @media print {
            .no-print { display: none !important; }
        }
        .btn-print {
            padding: 8px 16px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 8px;
            width: 100%;
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ IMPRIMIR COMPROVANTE (80mm)</button>
        <button class="btn-print" style="background: #475569;" onclick="window.close()">✖ FECHAR JANELA</button>
    </div>

    <!-- Cabeçalho -->
    <div class="text-center">
        <h2 style="margin: 0; font-size: 15px; font-weight: bold;">{{ $sale->tenant?->name ?? config('app.name', 'ZBIZ+') }}</h2>
        <div style="font-size: 11px;">{{ $sale->branch?->name ?? 'Loja Principal' }}</div>
        @if($sale->tenant?->nuit)
            <div style="font-size: 11px;">NUIT: {{ $sale->tenant->nuit }}</div>
        @endif
        @if($sale->branch?->phone)
            <div style="font-size: 11px;">Tel: {{ $sale->branch->phone }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Dados do Documento Fiscal/Comercial -->
    <div><strong>Doc:</strong> {{ $sale->official_invoice_title }}</div>
    <div><strong>Nº:</strong> {{ $sale->invoice_number ?? ('#' . str_pad($sale->id, 6, '0', STR_PAD_LEFT)) }}</div>
    <div><strong>Data:</strong> {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y H:i') : $sale->created_at->format('d/m/Y H:i') }}</div>
    <div><strong>Operador:</strong> {{ $sale->user?->name ?? 'Caixa' }}</div>
    <div><strong>Cliente:</strong> {{ $sale->customer_display_name }}</div>
    @if($sale->customer?->nuit || $sale->customer_nuit)
        <div><strong>NUIT Cliente:</strong> {{ $sale->customer?->nuit ?? $sale->customer_nuit }}</div>
    @endif
    @if($sale->customer?->phone || $sale->customer_phone)
        <div><strong>Telefone:</strong> {{ $sale->customer?->phone ?? $sale->customer_phone }}</div>
    @endif

    <div class="divider"></div>

    <!-- Tabela de Itens -->
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
                    <td colspan="3" style="font-weight: bold;">{{ $item->product_name ?? $item->product?->name ?? 'Artigo' }}</td>
                </tr>
                <tr>
                    <td>{{ number_format($item->unit_price, 2, ',', '.') }} MT</td>
                    <td class="text-center">x{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 2, ',', '.') }} MT</td>
                </tr>
                @if($item->discount_amount > 0)
                <tr>
                    <td colspan="3" style="font-size: 10px; color: #444;">
                        Desc. Item: -{{ number_format($item->discount_amount, 2, ',', '.') }} MT
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Totais e Discriminação de IVA -->
    <table class="table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">{{ number_format($sale->subtotal, 2, ',', '.') }} MT</td>
        </tr>
        @if($sale->discount_amount > 0)
        <tr>
            <td>Desconto Global:</td>
            <td class="text-right">-{{ number_format($sale->discount_amount, 2, ',', '.') }} MT</td>
        </tr>
        @endif

        @if($sale->tax_amount > 0)
        <tr>
            <td>IVA ({{ number_format($sale->tax_rate, 0) }}%):</td>
            <td class="text-right">{{ number_format($sale->tax_amount, 2, ',', '.') }} MT</td>
        </tr>
        @elseif($sale->tax_regime === 'exempt')
        <tr>
            <td style="font-size: 10px;">Regime de IVA:</td>
            <td class="text-right" style="font-size: 10px;">Isento (Artigo 9º do CIVA)</td>
        </tr>
        @endif

        <tr style="font-size: 14px; font-weight: bold; border-top: 1px dashed #000;">
            <td>TOTAL:</td>
            <td class="text-right">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</td>
        </tr>

        <tr>
            <td>Pagamento ({{ $sale->formatted_payment_method }}):</td>
            <td class="text-right">{{ number_format($sale->display_amount_paid, 2, ',', '.') }} MT</td>
        </tr>

        @if($sale->payment_method === 'credit')
            @if($sale->debt && $sale->debt->remaining_amount > 0)
            <tr style="font-weight: bold;">
                <td>Saldo Devedor:</td>
                <td class="text-right">{{ number_format($sale->debt->remaining_amount, 2, ',', '.') }} MT</td>
            </tr>
            @endif
        @else
            <tr>
                <td>Troco:</td>
                <td class="text-right">{{ number_format($sale->display_change_amount, 2, ',', '.') }} MT</td>
            </tr>
        @endif
    </table>

    @if($sale->notes)
        <div class="divider"></div>
        <div style="font-size: 10px;">
            <strong>Observações:</strong> {{ $sale->notes }}
        </div>
    @endif

    <div class="divider"></div>

    <div class="text-center" style="font-size: 10px;">
        <p>Obrigado pela sua preferência!</p>
        <p>Processado por programa certificado ZBIZ+ • {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <script>
        // Auto-print imediato
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 400);
        }
    </script>
</body>
</html>