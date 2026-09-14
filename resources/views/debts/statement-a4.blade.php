<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato da Dívida #DIV-{{ str_pad($debt->id, 6, '0', STR_PAD_LEFT) }} - {{ $debt->tenant?->name ?? 'ZBIZ+' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
        }
        .page-container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 18mm 20mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo {
            max-height: 70px;
            max-width: 200px;
            object-fit: contain;
            margin-bottom: 8px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px 0;
        }
        .company-details {
            font-size: 10px;
            color: #475569;
            line-height: 1.35;
        }
        .doc-badge {
            background-color: #0f172a;
            color: #ffffff;
            padding: 10px 16px;
            text-align: right;
            border-radius: 8px;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            color: #38bdf8;
        }
        .doc-number {
            font-size: 14px;
            font-weight: bold;
            margin-top: 3px;
            color: #ffffff;
        }
        .info-box-table {
            margin-top: 18px;
            margin-bottom: 18px;
        }
        .info-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            background-color: #f8fafc;
            height: 100%;
        }
        .info-box-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 8px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .info-box-content {
            font-size: 11px;
            color: #1e293b;
            line-height: 1.45;
        }
        .payments-table {
            margin-top: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
        }
        .payments-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            letter-spacing: 0.5px;
        }
        .payments-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
        }
        .payments-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .totals-table {
            margin-top: 15px;
        }
        .totals-box {
            width: 300px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            margin-left: auto;
            overflow: hidden;
        }
        .totals-box td {
            padding: 6px 12px;
            font-size: 11px;
        }
        .totals-box tr.grand-total {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
        }
        .totals-box tr.grand-total td {
            color: #ffffff;
            padding: 9px 12px;
            font-size: 12.5px;
        }
        .signatures-table {
            margin-top: 35px;
            page-break-inside: avoid;
        }
        .signatures-table td {
            text-align: center;
            font-size: 10px;
            color: #475569;
            vertical-align: top;
            padding: 0 15px;
        }
        .sig-line {
            border-top: 1px solid #94a3b8;
            width: 80%;
            margin: 0 auto 6px auto;
        }
        .footer-note {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: monospace; }
        
        .no-print {
            max-width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary { background-color: #0f172a; color: #ffffff; }
        .btn-primary:hover { background-color: #1e293b; }
        .btn-close { background-color: #64748b; color: #ffffff; }
        .btn-close:hover { background-color: #475569; }

        @media print {
            body { background-color: #ffffff; padding: 0; }
            .page-container { box-shadow: none; border-radius: 0; padding: 0; width: 100%; max-width: 100%; min-height: auto; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    @php
        $tenant = $debt->tenant;
        $docSettings = $tenant ? $tenant->getDocumentSettings() : [];
        $isFullyPaid = ($debt->remaining_amount <= 0.01);
        $debtRef = 'DIV-' . str_pad($debt->id, 6, '0', STR_PAD_LEFT);

        $logoUrl = null;
        if (!empty($tenant?->logo_url)) {
            $logoUrl = $tenant->logo_url;
        } elseif (!empty($docSettings['logo_url'])) {
            $logoUrl = asset($docSettings['logo_url']);
        }
    @endphp

    <div class="no-print">
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Imprimir Extrato A4
            </button>
        </div>
        <div>
            <button onclick="window.close()" class="btn btn-close">
                ✖ Fechar
            </button>
        </div>
    </div>

    <div class="page-container">
        
        <table class="header-table">
            <tr>
                <td style="width: 58%;">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="logo"><br>
                    @endif
                    <div class="company-title">{{ $tenant?->name ?? config('app.name', 'ZBIZ+') }}</div>
                    <div class="company-details">
                        @if(!empty($tenant?->legal_name) && $tenant->legal_name !== $tenant->name)
                            <strong>Denominação Social:</strong> {{ $tenant->legal_name }}<br>
                        @endif
                        <strong>NUIT:</strong> {{ $tenant?->nuit ?? 'N/D' }}<br>
                        <strong>Endereço:</strong> {{ $tenant?->address ?? 'Moçambique' }}, {{ $tenant?->city ?? '' }}<br>
                        <strong>Balcão / Filial:</strong> {{ $debt->branch?->name ?? 'Sede Central' }}<br>
                        <strong>Telefone:</strong> {{ $tenant?->phone ?? $debt->branch?->phone ?? 'N/D' }} | <strong>E-mail:</strong> {{ $tenant?->email ?? 'N/D' }}
                    </div>
                </td>
                <td style="width: 42%;">
                    <div class="doc-badge">
                        <div class="doc-title">EXTRATO GERAL DA DÍVIDA</div>
                        <div class="doc-number">#{{ $debtRef }}</div>
                        <div style="font-size: 9.5px; margin-top: 4px; color: #cbd5e1;">
                            Conta Corrente &bull; Histórico Completo de Amortizações
                        </div>
                    </div>
                    <div style="margin-top: 10px; font-size: 10.5px; text-align: right; color: #475569;">
                        <div><strong>Data de Emissão:</strong> {{ now()->format('d/m/Y H:i') }}</div>
                        @if($debt->sale_id)
                            <div><strong>Venda / Fatura Origem:</strong> #VD-{{ str_pad($debt->sale_id, 6, '0', STR_PAD_LEFT) }}</div>
                        @endif
                        <div><strong>Data Limite / Vencimento:</strong> {{ $debt->due_date ? $debt->due_date->format('d/m/Y') : 'Não estipulada' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="info-box-table">
            <tr>
                <td style="width: 58%; padding-right: 10px;">
                    <div class="info-box">
                        <div class="info-box-title">Dados do Titular da Conta / Devedor</div>
                        <div class="info-box-content">
                            <strong>Nome:</strong> {{ $debt->customer_name ?? $debt->customer?->name ?? 'Cliente Registado' }}<br>
                            <strong>NUIT:</strong> {{ $debt->customer?->nuit ?? $debt->customer_document ?? 'Consumidor Final' }}<br>
                            <strong>Telefone:</strong> {{ $debt->customer_phone ?? $debt->customer?->phone ?? 'N/D' }}<br>
                            <strong>Endereço:</strong> {{ $debt->customer?->address ?? 'Moçambique' }}
                        </div>
                    </div>
                </td>
                <td style="width: 42%;">
                    <div class="info-box">
                        <div class="info-box-title">Situação Financeira do Fiado</div>
                        <div class="info-box-content">
                            <strong>Estado:</strong> 
                            @if($isFullyPaid)
                                <span style="color: #059669; font-weight: bold;">TOTALMENTE LIQUIDADO</span>
                            @else
                                <span style="color: #d97706; font-weight: bold;">SALDO DEVEDOR ATIVO</span>
                            @endif
                            <br>
                            <strong>Data de Constituição:</strong> {{ $debt->debt_date ? \Carbon\Carbon::parse($debt->debt_date)->format('d/m/Y') : ($debt->created_at ? \Carbon\Carbon::parse($debt->created_at)->format('d/m/Y') : now()->format('d/m/Y')) }}<br>
                            <strong>Total de Amortizações:</strong> {{ count($debt->payments ?? []) }} pagamento(s)
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Histórico Cronológico de Amortizações -->
        <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #0f172a; margin-top: 15px;">
            Histórico Cronológico de Amortizações Registadas
        </div>

        <table class="payments-table">
            <thead>
                <tr>
                    <th style="width: 15%; text-align: left;">Data/Hora</th>
                    <th style="width: 15%; text-align: left;">Nº Recibo</th>
                    <th style="width: 20%; text-align: left;">Forma de Pagamento</th>
                    <th style="width: 25%; text-align: left;">Operador / Caixa</th>
                    <th style="width: 25%; text-align: right;">Valor Amortizado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debt->payments ?? [] as $payment)
                    <tr>
                        <td>{{ $payment->created_at ? $payment->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="font-mono">#RC-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ strtoupper($payment->payment_method_text ?? $payment->payment_method ?? 'Dinheiro') }}</td>
                        <td>{{ $payment->user?->name ?? 'Caixa' }}</td>
                        <td class="text-right font-mono font-bold" style="color: #059669;">{{ number_format($payment->amount, 2, ',', '.') }} MT</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 15px; color: #94a3b8;">
                            Nenhum pagamento registado nesta conta corrente até o momento.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Quadro de Resumo de Totais -->
        <table class="totals-table">
            <tr>
                <td></td>
                <td style="width: 320px;">
                    <table class="totals-box">
                        <tr>
                            <td>Valor Original Constituído:</td>
                            <td class="text-right font-mono">{{ number_format($debt->original_amount ?? $debt->total_amount, 2, ',', '.') }} MT</td>
                        </tr>
                        <tr>
                            <td style="color: #059669; font-weight: bold;">Total Amortizado:</td>
                            <td class="text-right font-mono font-bold" style="color: #059669;">- {{ number_format($debt->paid_amount ?? 0, 2, ',', '.') }} MT</td>
                        </tr>
                        <tr class="grand-total">
                            <td>SALDO RESTANTE:</td>
                            <td class="text-right font-mono">{{ number_format($debt->remaining_amount, 2, ',', '.') }} MT</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="signatures-table">
            <tr>
                <td style="width: 50%;">
                    <div class="sig-line"></div>
                    <strong>Pela Entidade Credora</strong><br>
                    <span>{{ auth()->user()?->name ?? 'Gestão Financeira' }}</span><br>
                    <span style="font-size: 9px; color: #94a3b8;">(Assinatura & Carimbo)</span>
                </td>
                <td style="width: 50%;">
                    <div class="sig-line"></div>
                    <strong>Pelo Devedor / Titular da Conta</strong><br>
                    <span>{{ $debt->customer_name ?? $debt->customer?->name }}</span><br>
                    <span style="font-size: 9px; color: #94a3b8;">(Assinatura de Conformidade)</span>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            Extrato gerado pelo sistema <strong>ZBIZ+ Enterprise Cloud & POS Suite</strong> &bull; Fdsmultiservices &bull; {{ now()->format('d/m/Y H:i:s') }}
        </div>

    </div>

</body>
</html>
