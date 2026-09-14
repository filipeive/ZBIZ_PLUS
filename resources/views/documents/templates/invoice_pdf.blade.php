<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $sale->official_invoice_title }} - {{ $sale->invoice_number ?? ('VD-' . $sale->id) }}</title>
    <style>
        @page {
            margin: 12mm 15mm 15mm 15mm;
            size: a4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
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
            padding: 8px 14px;
            text-align: right;
            border-radius: 6px;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            color: #10b981;
        }
        .doc-number {
            font-size: 13px;
            font-weight: bold;
            margin-top: 3px;
        }
        .info-box-table {
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .info-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            background-color: #f8fafc;
        }
        .info-box-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
        }
        .info-box-content {
            font-size: 10.5px;
            color: #1e293b;
            line-height: 1.4;
        }
        .items-table {
            margin-top: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            border-bottom: 1px solid #0f172a;
        }
        .items-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .totals-table {
            margin-top: 15px;
        }
        .totals-table td {
            vertical-align: top;
        }
        .totals-box {
            width: 260px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #f8fafc;
            margin-left: auto;
        }
        .totals-box td {
            padding: 5px 12px;
            font-size: 10.5px;
        }
        .totals-box tr.grand-total {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 12px;
        }
        .totals-box tr.grand-total td {
            color: #ffffff;
            padding: 8px 12px;
        }
        .banks-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            background-color: #f8fafc;
            font-size: 9.5px;
            color: #334155;
            margin-top: 15px;
        }
        .terms-box {
            margin-top: 15px;
            font-size: 9px;
            color: #64748b;
            line-height: 1.35;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
        }
        .signatures-table {
            margin-top: 30px;
        }
        .signatures-table td {
            text-align: center;
            font-size: 10px;
            color: #475569;
        }
        .sig-line {
            border-top: 1px solid #94a3b8;
            width: 70%;
            margin: 0 auto 5px auto;
        }
        .footer-note {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    @php
        $docSettings = $tenant->getDocumentSettings();
        $invoiceTitle = $sale->official_invoice_title;
        $invoiceNumber = $sale->invoice_number ?? ('VD-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT));
    @endphp

    <!-- Top Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                @php
                    $invoiceLogoPath = null;
                    if (!empty($docSettings['logo_url']) && file_exists(public_path($docSettings['logo_url']))) {
                        $invoiceLogoPath = public_path($docSettings['logo_url']);
                    } elseif ($tenant?->logo_path && file_exists($tenant->logo_path)) {
                        $invoiceLogoPath = $tenant->logo_path;
                    } elseif (!empty($tenant?->settings['logo_path']) && file_exists(public_path('storage/' . $tenant->settings['logo_path']))) {
                        $invoiceLogoPath = public_path('storage/' . $tenant->settings['logo_path']);
                    }
                @endphp
                @if($invoiceLogoPath)
                    <img src="{{ $invoiceLogoPath }}" alt="Logo" class="logo"><br>
                @endif
                <div class="company-title">{{ $docSettings['company_name'] }}</div>
                <div class="company-details">
                    @if($docSettings['legal_name'] !== $docSettings['company_name'])
                        <strong>Razão Social:</strong> {{ $docSettings['legal_name'] }}<br>
                    @endif
                    <strong>NUIT:</strong> {{ $docSettings['nuit'] ?? 'N/D' }}<br>
                    <strong>Endereço:</strong> {{ $docSettings['address'] }}, {{ $docSettings['city'] }} - {{ $docSettings['province'] }}<br>
                    <strong>Telefone:</strong> {{ $docSettings['phone'] }} | <strong>E-mail:</strong> {{ $docSettings['email'] }}
                </div>
            </td>
            <td style="width: 45%;">
                <div class="doc-badge">
                    <div class="doc-title">{{ $invoiceTitle }}</div>
                    <div class="doc-number">{{ $invoiceNumber }}</div>
                    <div style="font-size: 9.5px; margin-top: 4px; color: #cbd5e1;">
                        Original &bull; Processado por Computador
                    </div>
                </div>
                <div style="margin-top: 10px; font-size: 10px; text-align: right; color: #475569;">
                    <div><strong>Data de Emissão:</strong> {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : $sale->created_at->format('d/m/Y') }}</div>
                    @if($sale->due_date)
                        <div><strong>Data de Vencimento:</strong> {{ $sale->due_date->format('d/m/Y') }}</div>
                    @endif
                    <div><strong>Condição de Pagamento:</strong> {{ ucfirst($sale->payment_method ?? 'Pronto Pagamento') }}</div>
                    <div><strong>Operador:</strong> {{ $sale->user?->name ?? 'Sistema' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Info Box: Cliente & Detalhes -->
    <table class="info-box-table">
        <tr>
            <td style="width: 58%; padding-right: 10px;">
                <div class="info-box">
                    <div class="info-box-title">Exmo.(s) Sr.(s) / Dados do Cliente</div>
                    <div class="info-box-content">
                        <strong>Nome / Entidade:</strong> {{ $sale->customer_display_name }}<br>
                        <strong>NUIT:</strong> {{ $sale->customer?->nuit ?? $sale->customer_nuit ?? 'Consumidor Final' }}<br>
                        <strong>Telefone:</strong> {{ $sale->customer?->phone ?? $sale->customer_phone ?? 'N/D' }}<br>
                        <strong>Endereço:</strong> {{ $sale->customer?->address ?? $sale->customer_address ?? 'Moçambique' }}
                    </div>
                </div>
            </td>
            <td style="width: 42%;">
                <div class="info-box">
                    <div class="info-box-title">Enquadramento Fiscal</div>
                    <div class="info-box-content">
                        <strong>Regime IVA:</strong> 
                        @if($sale->tax_regime === 'exempt')
                            Isento (Artigo 9º do CIVA)
                        @elseif($sale->tax_regime === 'simplified')
                            Regime Simplificado (ISPC)
                        @else
                            Regime Geral (Taxa 16%)
                        @endif
                        <br>
                        <strong>Estado:</strong> 
                        @if($sale->invoice_type === 'cash_invoice' || $sale->payment_method !== 'credit')
                            <span style="color: #10b981; font-weight: bold;">Liquidado / Pago</span>
                        @else
                            <span style="color: #e11d48; font-weight: bold;">Aguardando Pagamento</span>
                        @endif
                        <br>
                        <strong>Moeda:</strong> Meticais (MZN)
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabela de Itens -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 45%;">Designação do Produto / Serviço</th>
                <th style="width: 10%; text-align: center;">Qtd</th>
                <th style="width: 13%; text-align: right;">P. Unitário</th>
                <th style="width: 10%; text-align: right;">Desc.</th>
                <th style="width: 7%; text-align: center;">IVA</th>
                <th style="width: 10%; text-align: right;">Total Líq.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sale->items as $idx => $item)
                @php
                    $isExempt = $item->is_tax_exempt || $sale->tax_regime === 'exempt';
                    $taxPct = $isExempt ? 0 : ($item->tax_rate ?? 16.0);
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->product?->name ?? $item->product_name ?? 'Item de Venda' }}</strong>
                        @if($item->product?->sku)
                            <span style="font-size: 9px; color: #64748b;">(Ref: {{ $item->product->sku }})</span>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">{{ number_format($item->original_unit_price > 0 ? $item->original_unit_price : $item->unit_price, 2, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $item->discount_amount > 0 ? number_format($item->discount_amount, 2, ',', '.') : '-' }}
                    </td>
                    <td class="text-center">{{ $isExempt ? 'Isento' : ($taxPct . '%') }}</td>
                    <td class="text-right font-bold">{{ number_format($item->total_price, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px;">Nenhum item registado nesta venda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totais e Coordenadas Bancárias -->
    <table class="totals-table">
        <tr>
            <td style="width: 55%; padding-right: 15px;">
                <!-- Coordenadas Bancárias -->
                @if(!empty($docSettings['bank_accounts']) || !empty($docSettings['mobile_wallets']))
                    <div class="banks-box">
                        <strong style="text-transform: uppercase; color: #0f172a; display: block; margin-bottom: 4px; font-size: 10px;">
                            Coordenadas para Pagamento / Transferência:
                        </strong>
                        @foreach($docSettings['bank_accounts'] ?? [] as $bank)
                            <div><strong>{{ $bank['bank_name'] }}:</strong> Conta: {{ $bank['account_number'] }} | NIB: {{ $bank['nib'] }}</div>
                        @endforeach
                        @foreach($docSettings['mobile_wallets'] ?? [] as $wallet)
                            <div><strong>{{ $wallet['wallet_name'] }}:</strong> {{ $wallet['phone_number'] }} (Titular: {{ $wallet['holder_name'] }})</div>
                        @endforeach
                    </div>
                @endif
            </td>
            <td style="width: 45%;">
                <table class="totals-box">
                    <tr>
                        <td>Incidência / Subtotal:</td>
                        <td class="text-right">{{ number_format($sale->subtotal, 2, ',', '.') }} MT</td>
                    </tr>
                    @if($sale->discount_amount > 0)
                        <tr>
                            <td style="color: #10b981;">Descontos Concedidos:</td>
                            <td class="text-right" style="color: #10b981;">-{{ number_format($sale->discount_amount, 2, ',', '.') }} MT</td>
                        </tr>
                    @endif
                    <tr>
                        <td>IVA Total ({{ $sale->tax_regime === 'exempt' ? 'Isento' : ($sale->tax_rate . '%') }}):</td>
                        <td class="text-right">
                            @if($sale->tax_regime === 'exempt')
                                0,00 MT
                            @else
                                {{ number_format($sale->tax_amount > 0 ? $sale->tax_amount : ($sale->total_amount * 0.16 / 1.16), 2, ',', '.') }} MT
                            @endif
                        </td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL A PAGAR:</td>
                        <td class="text-right">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Termos e Condições Legais -->
    <div class="terms-box">
        @if($sale->tax_regime === 'exempt' || !empty($sale->tax_exemption_reason))
            <strong>Menção Legal de IVA:</strong> {{ $sale->tax_exemption_reason ?? $docSettings['tax_exemption_reason'] }}<br>
        @endif
        <strong>Condições Gerais:</strong> {{ $docSettings['invoice_terms'] }}
    </div>

    <!-- Assinaturas -->
    <table class="signatures-table">
        <tr>
            <td style="width: 50%;">
                <div class="sig-line"></div>
                <strong>Pelo Emissor / {{ $docSettings['company_name'] }}</strong>
            </td>
            <td style="width: 50%;">
                <div class="sig-line"></div>
                <strong>Pelo Cliente / Aceite Comercial</strong>
            </td>
        </tr>
    </table>

    <!-- Rodapé -->
    <div class="footer-note">
        {{ $docSettings['footer_notes'] }} &bull; Documento emitido em {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>

