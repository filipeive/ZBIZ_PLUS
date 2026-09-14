<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fecho de Caixa #{{ $shift->id }}</title>
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
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #000;
            font-weight: bold;
            font-size: 11px;
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ IMPRIMIR FECHO DE CAIXA (80mm)</button>
        <button class="btn-print" style="background: #475569;" onclick="window.close()">✖ FECHAR JANELA</button>
    </div>

    <!-- Cabeçalho da Empresa -->
    <div class="text-center">
        <h2 style="margin: 0; font-size: 15px; font-weight: bold;">{{ $shift->tenant?->name ?? config('app.name', 'ZBIZ+') }}</h2>
        <div style="font-size: 11px;">{{ $shift->branch?->name ?? 'Balcão de Atendimento' }}</div>
        @if($shift->tenant?->nuit)
            <div style="font-size: 11px;">NUIT: {{ $shift->tenant->nuit }}</div>
        @endif
        @if($shift->branch?->phone)
            <div style="font-size: 11px;">Tel: {{ $shift->branch->phone }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Título do Documento Fiscal/Operacional -->
    <div class="text-center">
        <div style="font-weight: bold; font-size: 13px;">RELATÓRIO DE FECHO DE CAIXA</div>
        <div style="font-size: 11px;">(FECHO Z / TURNO DE OPERADOR)</div>
    </div>

    <div class="divider"></div>

    <!-- Metadados do Turno -->
    <div><strong>Turno Nº:</strong> #{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</div>
    <div><strong>Operador:</strong> {{ $shift->user?->name ?? 'Caixa' }}</div>
    <div><strong>Abertura:</strong> {{ $shift->opened_at->format('d/m/Y H:i') }}</div>
    <div><strong>Fecho:</strong> {{ $shift->closed_at ? $shift->closed_at->format('d/m/Y H:i') : 'Ainda Aberto' }}</div>
    @if($shift->closed_at)
        <div><strong>Duração:</strong> {{ $shift->opened_at->diffInHours($shift->closed_at) }}h {{ $shift->opened_at->diff($shift->closed_at)->i }}m</div>
    @endif
    <div><strong>Qtd Vendas:</strong> {{ $shift->sales()->count() }} transações</div>

    <div class="divider"></div>

    <!-- Resumo por Método de Pagamento -->
    <div style="font-weight: bold; margin-bottom: 2px;">RESUMO DE VENDAS DO TURNO</div>
    <table class="table">
        <tr>
            <td>(+) Numerário (Dinheiro):</td>
            <td class="text-right">{{ number_format($shift->cash_sales_total, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>(+) Carteira M-Pesa:</td>
            <td class="text-right">{{ number_format($shift->mpesa_sales_total, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>(+) Carteira e-Mola:</td>
            <td class="text-right">{{ number_format($shift->emola_sales_total, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>(+) Cartão / POS:</td>
            <td class="text-right">{{ number_format($shift->card_sales_total, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>(+) Crédito / Fiado:</td>
            <td class="text-right">{{ number_format($shift->credit_sales_total, 2, ',', '.') }} MT</td>
        </tr>
        <tr style="border-top: 1px dashed #000; font-weight: bold;">
            <td>(=) TOTAL FATURADO:</td>
            <td class="text-right">{{ number_format($shift->total_sales_amount, 2, ',', '.') }} MT</td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Auditoria de Numerário na Gaveta (Fecho Cego) -->
    <div style="font-weight: bold; margin-bottom: 2px;">AUDITORIA DE NUMERÁRIO (GAVETA)</div>
    <table class="table">
        <tr>
            <td>Fundo de Maneio Inicial:</td>
            <td class="text-right">{{ number_format($shift->opening_balance, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>(+) Vendas em Dinheiro:</td>
            <td class="text-right">{{ number_format($shift->cash_sales_total, 2, ',', '.') }} MT</td>
        </tr>
        <tr style="font-weight: bold; border-top: 1px dashed #000;">
            <td>(=) Saldo Esperado Sistema:</td>
            <td class="text-right">{{ number_format($shift->closing_balance_system ?? $shift->expected_cash, 2, ',', '.') }} MT</td>
        </tr>
        <tr style="font-weight: bold;">
            <td>Contagem Física Real (Cega):</td>
            <td class="text-right">{{ number_format($shift->closing_balance_actual ?? 0, 2, ',', '.') }} MT</td>
        </tr>
    </table>

    <div class="divider-double"></div>

    <!-- Situação do Fecho -->
    <div class="text-center">
        @php
            $diff = (float)($shift->difference ?? 0);
        @endphp
        @if(abs($diff) < 0.01)
            <div class="status-badge" style="border-color: #000;">✓ SITUAÇÃO: CAIXA CERTO (0,00 MT)</div>
        @elseif($diff < 0)
            <div class="status-badge" style="border-color: #000;">⚠ QUEBRA DE CAIXA: {{ number_format($diff, 2, ',', '.') }} MT</div>
            <div style="font-size: 10px;">(Falta de numerário apurada no turno)</div>
        @else
            <div class="status-badge" style="border-color: #000;">⚠ SOBRA DE CAIXA: +{{ number_format($diff, 2, ',', '.') }} MT</div>
            <div style="font-size: 10px;">(Excesso de numerário apurado no turno)</div>
        @endif
    </div>

    @if($shift->notes)
        <div class="divider"></div>
        <div style="font-size: 10px;">
            <strong>Observações / Justificação:</strong><br>
            {{ $shift->notes }}
        </div>
    @endif

    <div class="divider"></div>

    <!-- Linhas de Assinatura para Arquivo Físico -->
    <div style="margin-top: 25px; text-align: center;">
        <div>______________________________________</div>
        <div style="font-size: 10px; font-weight: bold;">{{ $shift->user?->name ?? 'Operador de Caixa' }}</div>
        <div style="font-size: 9px; color: #444;">Assinatura do Operador</div>
    </div>

    <div style="margin-top: 25px; text-align: center;">
        <div>______________________________________</div>
        <div style="font-size: 10px; font-weight: bold;">Supervisor / Gerente de Loja</div>
        <div style="font-size: 9px; color: #444;">Visto de Auditoria & Conferência</div>
    </div>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 9px; margin-top: 5px;">
        Processado por ZBIZ+ ERP • {{ now()->format('d/m/Y H:i:s') }}
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
