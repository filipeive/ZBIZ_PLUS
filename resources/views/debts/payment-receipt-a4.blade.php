<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pagamento #RC-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }} - {{ $debt->tenant?->name ?? 'ZBIZ+' }}</title>
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
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            color: #10b981;
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
        .financial-table {
            margin-top: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
        }
        .financial-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 9px 12px;
            letter-spacing: 0.5px;
        }
        .financial-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .financial-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .financial-table tr.highlight-payment {
            background-color: #ecfdf5;
            font-weight: bold;
        }
        .financial-table tr.highlight-payment td {
            color: #065f46;
            font-size: 12px;
            border-top: 2px solid #10b981;
            border-bottom: 2px solid #10b981;
        }
        .financial-table tr.remaining-row td {
            font-weight: bold;
            font-size: 11.5px;
        }
        .declaration-box {
            margin-top: 18px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 10.5px;
            line-height: 1.45;
        }
        .declaration-paid {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
        }
        .declaration-partial {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
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
        
        /* Barra de Ações (Apenas na Visualização de Ecrã) */
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
        .btn-primary {
            background-color: #0f172a;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1e293b;
        }
        .btn-emerald {
            background-color: #059669;
            color: #ffffff;
        }
        .btn-emerald:hover {
            background-color: #047857;
        }
        .btn-outline {
            background-color: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-outline:hover {
            background-color: #f8fafc;
        }
        .btn-close {
            background-color: #64748b;
            color: #ffffff;
        }
        .btn-close:hover {
            background-color: #475569;
        }

        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .page-container {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                width: 100%;
                max-width: 100%;
                min-height: auto;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    @php
        $tenant = $debt->tenant;
        $docSettings = $tenant ? $tenant->getDocumentSettings() : [];
        $isFullyPaid = ($debt->remaining_amount <= 0.01);
        $receiptNumber = 'RC-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
        $debtRef = 'DIV-' . str_pad($debt->id, 6, '0', STR_PAD_LEFT);

        // Logo Path
        $logoUrl = null;
        if (!empty($tenant?->logo_url)) {
            $logoUrl = $tenant->logo_url;
        } elseif (!empty($docSettings['logo_url'])) {
            $logoUrl = asset($docSettings['logo_url']);
        }
    @endphp

    <!-- Barra de Ações Superior (Não impressa) -->
    <div class="no-print">
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Imprimir Recibo (A4)
            </button>
            <a href="{{ route('debts.payments.receipt', ['payment' => $payment->id, 'download' => 'pdf']) }}" class="btn btn-emerald">
                ⬇️ Descarregar PDF (A4)
            </a>
            <a href="{{ route('debts.payments.receipt', ['payment' => $payment->id, 'format' => 'thermal']) }}" class="btn btn-outline">
                🧾 Versão Térmica (80mm)
            </a>
        </div>
        <div>
            <button onclick="window.close()" class="btn btn-close">
                ✖ Fechar
            </button>
        </div>
    </div>

    <!-- Folha A4 Oficial -->
    <div class="page-container">
        
        <!-- Cabeçalho Oficial Timbrado -->
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
                        <div class="doc-title">{{ $isFullyPaid ? 'RECIBO DE QUITAÇÃO DE DÍVIDA' : 'RECIBO DE AMORTIZAÇÃO' }}</div>
                        <div class="doc-number">#{{ $receiptNumber }}</div>
                        <div style="font-size: 9.5px; margin-top: 4px; color: #cbd5e1;">
                            Original &bull; Válido como Comprovativo de Pagamento
                        </div>
                    </div>
                    <div style="margin-top: 10px; font-size: 10.5px; text-align: right; color: #475569;">
                        <div><strong>Data de Pagamento:</strong> {{ $payment->created_at ? $payment->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</div>
                        <div><strong>Referência da Dívida:</strong> #{{ $debtRef }}</div>
                        @if($debt->sale_id)
                            <div><strong>Venda / Fatura Origem:</strong> #VD-{{ str_pad($debt->sale_id, 6, '0', STR_PAD_LEFT) }}</div>
                        @endif
                        <div><strong>Operador de Caixa:</strong> {{ $payment->user?->name ?? auth()->user()?->name ?? 'Caixa' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Quadro de Dados: Cliente e Conta Corrente -->
        <table class="info-box-table">
            <tr>
                <td style="width: 58%; padding-right: 10px;">
                    <div class="info-box">
                        <div class="info-box-title">Exmo.(s) Sr.(s) / Dados do Devedor / Cliente</div>
                        <div class="info-box-content">
                            <strong>Nome / Entidade:</strong> {{ $debt->customer_name ?? $debt->customer?->name ?? 'Cliente Registado' }}<br>
                            <strong>NUIT:</strong> {{ $debt->customer?->nuit ?? $debt->customer_document ?? 'Consumidor Final' }}<br>
                            <strong>Telefone:</strong> {{ $debt->customer_phone ?? $debt->customer?->phone ?? 'N/D' }}<br>
                            <strong>Endereço:</strong> {{ $debt->customer?->address ?? 'Moçambique' }}
                        </div>
                    </div>
                </td>
                <td style="width: 42%;">
                    <div class="info-box">
                        <div class="info-box-title">Estado da Obrigação Creditícia</div>
                        <div class="info-box-content">
                            <strong>Estado Atual:</strong> 
                            @if($isFullyPaid)
                                <span style="color: #059669; font-weight: bold;">TOTALMENTE LIQUIDADO</span>
                            @else
                                <span style="color: #d97706; font-weight: bold;">ATIVO / PENDENTE</span>
                            @endif
                            <br>
                            <strong>Data da Concessão:</strong> {{ $debt->debt_date ? \Carbon\Carbon::parse($debt->debt_date)->format('d/m/Y') : ($debt->created_at ? \Carbon\Carbon::parse($debt->created_at)->format('d/m/Y') : now()->format('d/m/Y')) }}<br>
                            <strong>Data de Vencimento:</strong> {{ $debt->due_date ? \Carbon\Carbon::parse($debt->due_date)->format('d/m/Y') : 'Não estipulada' }}<br>
                            <strong>Moeda:</strong> Metical Moçambicano (MZN / MT)
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Tabela Demonstrativa da Liquidação Financeira -->
        <table class="financial-table">
            <thead>
                <tr>
                    <th style="width: 60%; text-align: left;">Descrição / Conceito da Operação</th>
                    <th style="width: 40%; text-align: right;">Montante (MT)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Valor Original da Dívida Constituída</strong><br>
                        <span style="font-size: 10px; color: #64748b;">
                            Referente a produtos/serviços faturados a crédito na conta corrente
                        </span>
                    </td>
                    <td class="text-right font-mono" style="font-size: 11.5px;">
                        {{ number_format($debt->original_amount ?? $debt->total_amount, 2, ',', '.') }} MT
                    </td>
                </tr>
                @php
                    $previousAmortizations = max(0, ($debt->original_amount ?? $debt->total_amount) - $debt->remaining_amount - $payment->amount);
                @endphp
                @if($previousAmortizations > 0.01)
                <tr>
                    <td>
                        <strong>Total Amortizado em Prestações Anteriores</strong><br>
                        <span style="font-size: 10px; color: #64748b;">
                            Soma acumulada de pagamentos já liquidados até à data
                        </span>
                    </td>
                    <td class="text-right font-mono" style="color: #475569;">
                        - {{ number_format($previousAmortizations, 2, ',', '.') }} MT
                    </td>
                </tr>
                @endif
                <tr class="highlight-payment">
                    <td>
                        <strong style="font-size: 12.5px;">VALOR PAGO NESTA OPERAÇÃO (RECEBIMENTO EFETIVO)</strong><br>
                        <span style="font-size: 10.5px; font-weight: normal; color: #047857;">
                            Meio de Pagamento: <strong>{{ strtoupper($payment->payment_method_text ?? $payment->payment_method ?? 'Numerário') }}</strong>
                            @if($payment->notes) &bull; Obs: {{ $payment->notes }} @endif
                        </span>
                    </td>
                    <td class="text-right font-mono" style="font-size: 14px;">
                        {{ number_format($payment->amount, 2, ',', '.') }} MT
                    </td>
                </tr>
                <tr class="remaining-row">
                    <td>
                        <strong>Saldo Remanescente a Liquidar</strong><br>
                        <span style="font-size: 10px; color: #64748b;">
                            Valor que permanece sob responsabilidade do cliente
                        </span>
                    </td>
                    <td class="text-right font-mono" style="font-size: 12.5px; color: {{ $isFullyPaid ? '#059669' : '#e11d48' }};">
                        {{ number_format($debt->remaining_amount, 2, ',', '.') }} MT
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Termo Formal de Declaração de Quitação / Amortização -->
        @if($isFullyPaid)
            <div class="declaration-box declaration-paid">
                <strong>DECLARAÇÃO FORMAL DE QUITAÇÃO TOTAL:</strong><br>
                Certificamos para os devidos efeitos que a quantia de <strong>{{ number_format($payment->amount, 2, ',', '.') }} MT</strong> recebida nesta data liquidou na totalidade o saldo em aberto da dívida <strong>#{{ $debtRef }}</strong>. O cliente <strong>{{ $debt->customer_name ?? $debt->customer?->name }}</strong> encontra-se integralmente desonerado da respetiva obrigação creditícia, nada mais lhe sendo imputável referente ao processo faturado.
            </div>
        @else
            <div class="declaration-box declaration-partial">
                <strong>DECLARAÇÃO DE AMORTIZAÇÃO PARCIAL:</strong><br>
                Certificamos a receção efetiva do montante de <strong>{{ number_format($payment->amount, 2, ',', '.') }} MT</strong> entregue a título de amortização pontual da dívida <strong>#{{ $debtRef }}</strong>. Fica averbado na respetiva conta corrente o saldo devedor remanescente de <strong>{{ number_format($debt->remaining_amount, 2, ',', '.') }} MT</strong>, sujeito aos prazos e condições acordados.
            </div>
        @endif

        @if($debt->notes)
            <div style="margin-top: 12px; font-size: 10px; color: #64748b; background: #f8fafc; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                <strong>Observações da Conta:</strong> {{ $debt->notes }}
            </div>
        @endif

        <!-- Assinaturas Formais e Carimbo -->
        <table class="signatures-table">
            <tr>
                <td style="width: 50%;">
                    <div class="sig-line"></div>
                    <strong>Pela Entidade Credora / Operador</strong><br>
                    <span>{{ $payment->user?->name ?? auth()->user()?->name ?? 'Operador de Caixa Autorizado' }}</span><br>
                    <span style="font-size: 9px; color: #94a3b8;">(Assinatura & Carimbo)</span>
                </td>
                <td style="width: 50%;">
                    <div class="sig-line"></div>
                    <strong>Pelo Cliente / Devedor</strong><br>
                    <span>{{ $debt->customer_name ?? $debt->customer?->name }}</span><br>
                    <span style="font-size: 9px; color: #94a3b8;">(Assinatura de Conformidade)</span>
                </td>
            </tr>
        </table>

        <!-- Nota de Rodapé -->
        <div class="footer-note">
            Documento emitido informaticamente através da plataforma <strong>ZBIZ+ Enterprise Cloud & POS Suite</strong> &bull; {{ now()->format('d/m/Y H:i:s') }}<br>
            Desenvolvido por Fdsmultiservices &bull; Linha de Suporte: (+258) 86 213 4230 &bull; fdsmultiservices@gmail.com
        </div>

    </div>

</body>
</html>
