<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sale->official_invoice_title }} - {{ $sale->invoice_number ?? ('#' . $sale->id) }}</title>
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
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 2px 0; }
        @media print {
            .no-print { display: none !important; }
        }
        .btn-print {
            padding: 8px 16px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 6px;
            width: 100%;
            font-weight: bold;
            font-size: 13px;
        }
        .btn-close {
            padding: 6px 16px;
            background: #475569;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 10px;
            width: 100%;
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ IMPRIMIR COMPROVANTE (80mm)</button>
        <button class="btn-close" onclick="window.close()">✖ FECHAR JANELA</button>
    </div>

    <!-- Cabeçalho Oficial do Tenant -->
    <div class="text-center">
        @if(!empty($sale->tenant?->logo_url))
            <div style="margin-bottom: 6px;">
                <img src="{{ $sale->tenant->logo_url }}" alt="Logo" style="max-height: 48px; max-width: 150px; object-fit: contain;">
            </div>
        @endif
        <h2 style="margin: 0; font-size: 15px; font-weight: bold;">{{ $sale->tenant?->name ?? config('app.name', 'ZBIZ+') }}</h2>
        <div style="font-size: 11px;">{{ $sale->branch?->name ?? 'Loja Principal' }}</div>
        @if($sale->tenant?->nuit)
            <div style="font-size: 11px;">NUIT: {{ $sale->tenant->nuit }}</div>
        @endif
        @if($sale->branch?->phone)
            <div style="font-size: 11px;">Tel: {{ $sale->branch->phone }}</div>
        @endif
        @if($sale->branch?->address)
            <div style="font-size: 10px; color: #333;">{{ $sale->branch->address }}</div>
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
            <td>Desconto:</td>
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
            @php
                $debtBalance = max(0, $sale->total_amount - $sale->display_amount_paid);
            @endphp
            <tr style="font-weight: bold; color: #b91c1c;">
                <td>Saldo Devedor:</td>
                <td class="text-right">{{ number_format($debtBalance, 2, ',', '.') }} MT</td>
            </tr>
            @if($sale->due_date)
            <tr>
                <td style="font-size: 10px;">Data Vencimento:</td>
                <td class="text-right" style="font-size: 10px;">{{ $sale->due_date->format('d/m/Y') }}</td>
            </tr>
            @endif
        @else
            <tr>
                <td>Troco:</td>
                <td class="text-right">{{ number_format($sale->display_change_amount, 2, ',', '.') }} MT</td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 10px; margin-top: 5px;">
        <div>{{ $sale->tenant?->settings['receipt_footer'] ?? 'Obrigado pela preferência!' }}</div>
        <div style="font-size: 9px; color: #555; margin-top: 4px;">
            Software certificado ZBIZ+ • Fdsmultiservices
        </div>
        <div style="font-size: 8px; color: #777; margin-top: 2px;">
            Original para o Cliente • {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('autoprint')) {
                window.print();
            }
        });
    </script>
</body>
</html>