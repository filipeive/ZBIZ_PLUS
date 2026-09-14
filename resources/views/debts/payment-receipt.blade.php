<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pagamento #{{ $payment->id }}</title>
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
        <button class="btn-print" onclick="window.print()">🖨️ IMPRIMIR RECIBO (80mm)</button>
        <button class="btn-print" style="background: #475569;" onclick="window.close()">✖ FECHAR JANELA</button>
    </div>

    <!-- Cabeçalho -->
    <div class="text-center">
        @if(!empty($debt->tenant?->logo_url))
            <div style="margin-bottom: 6px;">
                <img src="{{ $debt->tenant->logo_url }}" alt="Logo" style="max-height: 48px; max-width: 150px; object-fit: contain;">
            </div>
        @endif
        <h2 style="margin: 0; font-size: 15px; font-weight: bold;">{{ $debt->tenant?->name ?? config('app.name', 'ZBIZ+') }}</h2>
        <div style="font-size: 11px;">{{ $debt->branch?->name ?? 'Balcão de Atendimento' }}</div>
        @if($debt->tenant?->nuit)
            <div style="font-size: 11px;">NUIT: {{ $debt->tenant->nuit }}</div>
        @endif
        @if($debt->branch?->phone)
            <div style="font-size: 11px;">Tel: {{ $debt->branch->phone }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Título do Documento -->
    <div class="text-center">
        <div style="font-weight: bold; font-size: 13px;">RECIBO DE AMORTIZAÇÃO DE DÍVIDA</div>
        <div style="font-size: 10px;">(COMPROVANTE DE QUITAÇÃO PARCIAL OU TOTAL)</div>
    </div>

    <div class="divider"></div>

    <div><strong>Recibo Nº:</strong> #RC-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div><strong>Ref. Dívida:</strong> #DÍVIDA-{{ str_pad($debt->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div><strong>Data / Hora:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}</div>
    <div><strong>Operador:</strong> {{ $payment->user?->name ?? 'Caixa' }}</div>
    <div><strong>Cliente:</strong> {{ $debt->customer_name ?? $debt->customer?->name }}</div>
    @if($debt->customer?->nuit)
        <div><strong>NUIT Cliente:</strong> {{ $debt->customer->nuit }}</div>
    @endif
    @if($debt->customer_phone ?? $debt->customer?->phone)
        <div><strong>Telefone:</strong> {{ $debt->customer_phone ?? $debt->customer?->phone }}</div>
    @endif

    <div class="divider"></div>

    <!-- Detalhes da Liquidação -->
    <table class="table">
        <tr>
            <td>Valor Original da Dívida:</td>
            <td class="text-right">{{ number_format($debt->original_amount ?? $debt->total_amount, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>Forma de Pagamento:</td>
            <td class="text-right" style="font-weight: bold;">{{ strtoupper($payment->payment_method ?? 'Numerário') }}</td>
        </tr>
        @if($payment->notes)
        <tr>
            <td colspan="2" style="font-size: 10px; color: #333;">Obs: {{ $payment->notes }}</td>
        </tr>
        @endif
        <tr style="border-top: 1px dashed #000; font-weight: bold; font-size: 13px;">
            <td>VALOR PAGO:</td>
            <td class="text-right">{{ number_format($payment->amount, 2, ',', '.') }} MT</td>
        </tr>
        <tr style="border-top: 1px dashed #000;">
            <td>Saldo Restante a Pagar:</td>
            <td class="text-right" style="font-weight: bold;">{{ number_format($debt->remaining_amount, 2, ',', '.') }} MT</td>
        </tr>
    </table>

    <div class="divider-double"></div>

    <!-- Status -->
    <div class="text-center" style="font-weight: bold;">
        @if($debt->remaining_amount <= 0.01)
            [ ★ DÍVIDA LIQUIDADA NA TOTALIDADE ★ ]
        @else
            [ DÍVIDA ATIVA - RESTAM {{ number_format($debt->remaining_amount, 2, ',', '.') }} MT ]
        @endif
    </div>

    <div class="divider"></div>

    <!-- Assinaturas -->
    <div style="margin-top: 25px; text-align: center;">
        <div>______________________________________</div>
        <div style="font-size: 10px; font-weight: bold;">{{ $payment->user?->name ?? 'Operador de Caixa' }}</div>
        <div style="font-size: 9px; color: #444;">Assinatura do Operador</div>
    </div>

    <div style="margin-top: 25px; text-align: center;">
        <div>______________________________________</div>
        <div style="font-size: 10px; font-weight: bold;">{{ $debt->customer_name ?? $debt->customer?->name }}</div>
        <div style="font-size: 9px; color: #444;">Assinatura do Cliente</div>
    </div>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 9px; margin-top: 5px;">
        Obrigado pela sua preferência!<br>
        Processado por ZBIZ+ ERP • {{ now()->format('d/m/Y H:i:s') }}
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 400);
        }
    </script>
</body>
</html>

