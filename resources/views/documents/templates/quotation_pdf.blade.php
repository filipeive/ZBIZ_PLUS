<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cotação Comercial - {{ $quotation->quotation_number }}</title>
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
            background-color: #0284c7;
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
            color: #ffffff;
        }
        .doc-number {
            font-size: 13px;
            font-weight: bold;
            margin-top: 3px;
            color: #e0f2fe;
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
            background-color: #0284c7;
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
    @endphp

    <!-- Top Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                @if(!empty($docSettings['logo_url']) && file_exists(public_path($docSettings['logo_url'])))
                    <img src="{{ public_path($docSettings['logo_url']) }}" alt="Logo" class="logo"><br>
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
                    <div class="doc-title">Cotação / Proposta</div>
                    <div class="doc-number">{{ $quotation->quotation_number }}</div>
                    <div style="font-size: 9.5px; margin-top: 4px; color: #e0f2fe;">
                        Proposta Comercial Sem Efeito de Fatura
                    </div>
                </div>
                <div style="margin-top: 10px; font-size: 10px; text-align: right; color: #475569;">
                    <div><strong>Data da Cotação:</strong> {{ $quotation->date ? $quotation->date->format('d/m/Y') : now()->format('d/m/Y') }}</div>
                    @if($quotation->valid_until)
                        <div><strong>Válida Até:</strong> {{ $quotation->valid_until->format('d/m/Y') }}</div>
                    @endif
                    <div><strong>Estado:</strong> 
                        <span style="font-weight: bold; text-transform: uppercase;">{{ $quotation->status }}</span>
                    </div>
                    <div><strong>Elaborado por:</strong> {{ $quotation->user?->name ?? 'Comercial' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Info Box: Cliente & Detalhes -->
    <table class="info-box-table">
        <tr>
            <td style="width: 58%; padding-right: 10px;">
                <div class="info-box">
                    <div class="info-box-title">Destinatário / Proposta Apresentada a</div>
                    <div class="info-box-content">
                        <strong>Nome / Empresa:</strong> {{ $quotation->customer_name }}<br>
                        <strong>NUIT:</strong> {{ $quotation->customer_nuit ?? 'Consumidor Final' }}<br>
                        <strong>Telefone:</strong> {{ $quotation->customer_phone ?? 'N/D' }}<br>
                        <strong>Endereço:</strong> {{ $quotation->customer_address ?? 'Moçambique' }}
                    </div>
                </div>
            </td>
            <td style="width: 42%;">
                <div class="info-box">
                    <div class="info-box-title">Condições Comerciais</div>
                    <div class="info-box-content">
                        <strong>Regime Fiscal:</strong> 
                        @if($quotation->tax_regime === 'exempt')
                            Isento (Artigo 9º do CIVA)
                        @else
                            Regime Normal (Taxa 16%)
                        @endif
                        <br>
                        <strong>Validade:</strong> {{ $docSettings['quotation_validity_days'] }} dias corridos<br>
                        <strong>Prazo de Entrega:</strong> Imediato / Sob Consulta<br>
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
                <th style="width: 45%;">Designação do Artigo / Serviço</th>
                <th style="width: 10%; text-align: center;">Qtd</th>
                <th style="width: 13%; text-align: right;">P. Unitário</th>
                <th style="width: 10%; text-align: right;">Desc.</th>
                <th style="width: 7%; text-align: center;">IVA</th>
                <th style="width: 10%; text-align: right;">Total Líq.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotation->items as $idx => $item)
                @php
                    $isExempt = $item->is_tax_exempt || $quotation->tax_regime === 'exempt';
                    $taxPct = $isExempt ? 0 : ($item->tax_rate ?? 16.0);
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->item_name }}</strong>
                        @if($item->description)
                            <div style="font-size: 9px; color: #64748b;">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $item->discount_amount > 0 ? number_format($item->discount_amount, 2, ',', '.') : '-' }}
                    </td>
                    <td class="text-center">{{ $isExempt ? 'Isento' : ($taxPct . '%') }}</td>
                    <td class="text-right font-bold">{{ number_format($item->total_price, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px;">Nenhum item cotado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totais e Coordenadas Bancárias -->
    <table class="totals-table">
        <tr>
            <td style="width: 55%; padding-right: 15px;">
                @if(!empty($docSettings['bank_accounts']) || !empty($docSettings['mobile_wallets']))
                    <div class="banks-box">
                        <strong style="text-transform: uppercase; color: #0f172a; display: block; margin-bottom: 4px; font-size: 10px;">
                            Coordenadas para Liquidação após Adjudicação:
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
                        <td class="text-right">{{ number_format($quotation->subtotal, 2, ',', '.') }} MT</td>
                    </tr>
                    @if($quotation->discount_amount > 0)
                        <tr>
                            <td style="color: #0284c7;">Descontos Concedidos:</td>
                            <td class="text-right" style="color: #0284c7;">-{{ number_format($quotation->discount_amount, 2, ',', '.') }} MT</td>
                        </tr>
                    @endif
                    <tr>
                        <td>IVA Total ({{ $quotation->tax_regime === 'exempt' ? 'Isento' : ($quotation->tax_rate . '%') }}):</td>
                        <td class="text-right">
                            @if($quotation->tax_regime === 'exempt')
                                0,00 MT
                            @else
                                {{ number_format($quotation->tax_amount, 2, ',', '.') }} MT
                            @endif
                        </td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL PROPOSTA:</td>
                        <td class="text-right">{{ number_format($quotation->total_amount, 2, ',', '.') }} MT</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Termos e Condições Legais -->
    <div class="terms-box">
        @if($quotation->tax_regime === 'exempt' || !empty($quotation->tax_exemption_reason))
            <strong>Menção Legal de IVA:</strong> {{ $quotation->tax_exemption_reason ?? $docSettings['tax_exemption_reason'] }}<br>
        @endif
        <strong>Termos e Validade:</strong> {{ $quotation->terms_conditions ?? $docSettings['quotation_terms'] }}
    </div>

    <!-- Assinaturas -->
    <table class="signatures-table">
        <tr>
            <td style="width: 50%;">
                <div class="sig-line"></div>
                <strong>Pelo Proponente / {{ $docSettings['company_name'] }}</strong>
            </td>
            <td style="width: 50%;">
                <div class="sig-line"></div>
                <strong>Aceite / Carimbo do Cliente</strong>
            </td>
        </tr>
    </table>

    <!-- Rodapé -->
    <div class="footer-note">
        {{ $docSettings['footer_notes'] }} &bull; Proposta elaborada em {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>

