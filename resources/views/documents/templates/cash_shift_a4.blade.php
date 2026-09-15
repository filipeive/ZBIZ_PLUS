<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Fecho de Caixa #{{ $shift->id }}</title>
    <style>
        @page { size: A4 portrait; margin: 14mm 16mm 16mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1e293b; font-size: 10px; line-height: 1.4; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .logo { max-width: 170px; max-height: 62px; object-fit: contain; margin-bottom: 6px; }
        .company-name { color: #0f172a; font-size: 19px; font-weight: bold; margin-bottom: 4px; }
        .muted { color: #64748b; }
        .header-right { text-align: right; }
        .badge { display: inline-block; min-width: 210px; padding: 10px 14px; background: #0f172a; color: #fff; text-align: right; border-radius: 5px; }
        .badge-title { color: #34d399; font-size: 15px; font-weight: bold; text-transform: uppercase; }
        .badge-number { font-size: 12px; font-weight: bold; margin-top: 3px; }
        .meta { margin-top: 10px; color: #475569; }
        .section { margin-top: 18px; }
        .section-title { background: #0f172a; color: #fff; font-size: 10px; font-weight: bold; text-transform: uppercase; padding: 7px 9px; }
        .info td { width: 50%; border: 1px solid #cbd5e1; padding: 8px 10px; vertical-align: top; }
        .info strong { color: #334155; }
        .summary { border: 1px solid #cbd5e1; }
        .summary th { background: #f1f5f9; color: #475569; font-size: 9px; text-align: left; text-transform: uppercase; padding: 7px 9px; }
        .summary td { border-top: 1px solid #e2e8f0; padding: 7px 9px; }
        .summary .amount { text-align: right; font-weight: bold; }
        .total td { background: #0f172a; color: #fff; font-weight: bold; }
        .audit td { border: 1px solid #cbd5e1; padding: 8px 10px; }
        .audit .label { width: 65%; }
        .audit .amount { text-align: right; font-weight: bold; }
        .status { margin-top: 18px; padding: 12px; border: 1px solid #94a3b8; text-align: center; font-size: 13px; font-weight: bold; }
        .status-ok { color: #047857; background: #ecfdf5; border-color: #86efac; }
        .status-warning { color: #b45309; background: #fffbeb; border-color: #fcd34d; }
        .notes { margin-top: 18px; padding: 10px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .signatures { margin-top: 52px; }
        .signatures td { width: 50%; text-align: center; color: #475569; }
        .line { border-top: 1px solid #64748b; width: 75%; margin: 0 auto 6px; }
        .footer { margin-top: 34px; padding-top: 8px; border-top: 1px dashed #cbd5e1; color: #94a3b8; text-align: center; font-size: 9px; }
        .no-print { margin-bottom: 16px; text-align: right; }
        .no-print button { background: #0f172a; color: #fff; border: 0; border-radius: 5px; padding: 8px 14px; font-weight: bold; cursor: pointer; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print"><button onclick="window.print()">Imprimir Relatório A4</button></div>

    @php
        $docSettings = $shift->tenant?->getDocumentSettings() ?? [];
        $logoPath = null;
        if (!empty($docSettings['logo_url']) && file_exists(public_path($docSettings['logo_url']))) {
            $logoPath = public_path($docSettings['logo_url']);
        } elseif ($shift->tenant?->logo_path && file_exists($shift->tenant->logo_path)) {
            $logoPath = $shift->tenant->logo_path;
        }
        $difference = (float)($shift->difference ?? 0);
    @endphp

    <table class="header">
        <tr>
            <td style="width: 57%;">
                @if($logoPath)<img src="{{ $logoPath }}" alt="Logo" class="logo"><br>@endif
                <div class="company-name">{{ $docSettings['company_name'] ?? $shift->tenant?->name ?? config('app.name', 'ZBIZ+') }}</div>
                <div class="muted">
                    @if($docSettings['nuit'] ?? $shift->tenant?->nuit)<strong>NUIT:</strong> {{ $docSettings['nuit'] ?? $shift->tenant?->nuit }}<br>@endif
                    {{ $docSettings['address'] ?? $shift->tenant?->address ?? '' }}<br>
                    {{ $docSettings['phone'] ?? $shift->tenant?->phone ?? '' }}
                </div>
            </td>
            <td class="header-right" style="width: 43%;">
                <div class="badge">
                    <div class="badge-title">Fecho de Caixa</div>
                    <div class="badge-number">Turno #{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 9px; margin-top: 4px; color: #cbd5e1;">Relatório operacional</div>
                </div>
                <div class="meta">
                    <strong>Emissão:</strong> {{ now()->format('d/m/Y H:i') }}<br>
                    <strong>Filial:</strong> {{ $shift->branch?->name ?? 'Balcão Principal' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Dados do turno</div>
        <table class="info">
            <tr>
                <td><strong>Operador:</strong> {{ $shift->user?->name ?? 'Caixa' }}<br><strong>Data de abertura:</strong> {{ $shift->opened_at->format('d/m/Y H:i') }}</td>
                <td><strong>Data de fecho:</strong> {{ $shift->closed_at?->format('d/m/Y H:i') ?? 'Ainda aberto' }}<br><strong>Vendas registadas:</strong> {{ $shift->sales()->count() }} transações</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Resumo de vendas por método de pagamento</div>
        <table class="summary">
            <thead><tr><th>Método de pagamento</th><th style="text-align: right;">Total</th></tr></thead>
            <tbody>
                <tr><td>Numerário (Dinheiro)</td><td class="amount">{{ number_format($shift->cash_sales_total, 2, ',', '.') }} MT</td></tr>
                <tr><td>M-Pesa</td><td class="amount">{{ number_format($shift->mpesa_sales_total, 2, ',', '.') }} MT</td></tr>
                <tr><td>e-Mola</td><td class="amount">{{ number_format($shift->emola_sales_total, 2, ',', '.') }} MT</td></tr>
                <tr><td>Cartão / POS</td><td class="amount">{{ number_format($shift->card_sales_total, 2, ',', '.') }} MT</td></tr>
                <tr><td>Crédito / Fiado</td><td class="amount">{{ number_format($shift->credit_sales_total, 2, ',', '.') }} MT</td></tr>
                <tr class="total"><td>TOTAL FATURADO</td><td class="amount">{{ number_format($shift->total_sales_amount, 2, ',', '.') }} MT</td></tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Auditoria de numerário</div>
        <table class="audit">
            <tr><td class="label">Fundo de maneio inicial</td><td class="amount">{{ number_format($shift->opening_balance, 2, ',', '.') }} MT</td></tr>
            <tr><td class="label">Vendas em dinheiro</td><td class="amount">{{ number_format($shift->cash_sales_total, 2, ',', '.') }} MT</td></tr>
            <tr><td class="label"><strong>Saldo esperado pelo sistema</strong></td><td class="amount">{{ number_format($shift->closing_balance_system ?? $shift->expected_cash, 2, ',', '.') }} MT</td></tr>
            <tr><td class="label"><strong>Contagem física realizada</strong></td><td class="amount">{{ number_format($shift->closing_balance_actual ?? 0, 2, ',', '.') }} MT</td></tr>
            <tr><td class="label"><strong>Diferença apurada</strong></td><td class="amount">{{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 2, ',', '.') }} MT</td></tr>
        </table>
    </div>

    @if(abs($difference) < 0.01)
        <div class="status status-ok">CAIXA CERTO: não foi identificada diferença.</div>
    @else
        <div class="status status-warning">{{ $difference < 0 ? 'QUEBRA DE CAIXA' : 'SOBRA DE CAIXA' }}: {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 2, ',', '.') }} MT</div>
    @endif

    @if($shift->notes)
        <div class="notes"><strong>Observações / Justificação:</strong><br>{{ $shift->notes }}</div>
    @endif

    <table class="signatures">
        <tr>
            <td><div class="line"></div><strong>{{ $shift->user?->name ?? 'Operador de Caixa' }}</strong><br>Assinatura do operador</td>
            <td><div class="line"></div><strong>Supervisor / Gerente</strong><br>Visto de auditoria</td>
        </tr>
    </table>

    <div class="footer">Processado por ZBIZ+ ERP · {{ now()->format('d/m/Y H:i:s') }}</div>

    <script>window.onload = function () { setTimeout(function () { window.print(); }, 400); };</script>
</body>
</html>
