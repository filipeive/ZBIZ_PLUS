<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Apuramento de IVA - {{ $startDate->format('m/Y') }} - {{ $tenant?->name ?? 'ZBIZ' }}</title>
    <style>
        @page {
            margin: 10mm 12mm 12mm 12mm;
            size: a4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.35;
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
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'DejaVu Sans Mono', monospace, Courier; }
        
        .header-box {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .company-info {
            font-size: 9px;
            color: #475569;
            margin-top: 2px;
        }
        .doc-badge {
            background-color: #0f172a;
            color: #ffffff;
            padding: 8px 12px;
            border-radius: 4px;
            text-align: right;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #38bdf8;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-subtitle {
            font-size: 9px;
            color: #cbd5e1;
            margin-top: 2px;
        }

        .section-title {
            background-color: #f1f5f9;
            border-left: 4px solid #0f172a;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 10px 0 6px 0;
        }

        .table-data {
            margin-bottom: 8px;
        }
        .table-data th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #0f172a;
        }
        .table-data td {
            padding: 4.5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
        }
        .table-data tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .kpi-table td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
        }

        .result-box {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .result-box-payable {
            background-color: #fef2f2;
            border: 1.5px solid #ef4444;
            color: #991b1b;
        }
        .result-box-credit {
            background-color: #ecfdf5;
            border: 1.5px solid #10b981;
            color: #065f46;
        }

        .signature-table {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signature-box {
            border-top: 1px solid #475569;
            padding-top: 5px;
            margin: 0 15px;
            text-align: center;
            font-size: 9px;
            color: #334155;
        }

        .footer-note {
            font-size: 8px;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <!-- CABEÇALHO FISCAL -->
    <div class="header-box">
        <table class="header-table">
            <tr>
                <td style="width: 58%;">
                    @php
                        $logoFile = $tenant?->logo_path;
                    @endphp
                    @if($logoFile && file_exists($logoFile))
                        <div style="margin-bottom: 6px;">
                            <img src="{{ $logoFile }}" alt="Logo" style="max-height: 45px; max-width: 160px; object-fit: contain;">
                        </div>
                    @endif
                    <div class="company-name">{{ $tenant?->name ?? 'ZBIZ PLUS EMPRESA' }}</div>
                    <div class="company-info">
                        <strong>NUIT:</strong> <span class="font-mono">{{ $tenant?->nuit ?? '999999999' }}</span><br>
                        <strong>Endereço:</strong> {{ $tenant?->address ?? 'Moçambique' }}<br>
                        <strong>Email / Tel:</strong> {{ $tenant?->email ?? '-' }} | {{ $tenant?->phone ?? '-' }}<br>
                        <strong>Enquadramento:</strong> Regime Geral do IVA (CIVA - 16%)
                    </div>
                </td>
                <td style="width: 42%;">
                    <div class="doc-badge">
                        <div class="doc-title">DECLARAÇÃO DE IVA</div>
                        <div class="doc-subtitle">APURAMENTO PERIÓDICO • MODELO A</div>
                        <div style="font-size: 11px; font-weight: bold; margin-top: 5px;">
                            MÊS: <span class="font-mono">{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}</span>
                        </div>
                        <div style="font-size: 8.5px; color: #94a3b8; margin-top: 2px;">
                            Período: {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- QUADRO I & II: DEMONSTRAÇÃO DO APURAMENTO FISCAL (CAMPOS DA AT) -->
    <div class="section-title">I. Quadro Resumo de Apuramento do IVA (Modelo A - Autoridade Tributária)</div>
    <table class="kpi-table" style="margin-bottom: 10px;">
        <tr style="background-color: #f8fafc;">
            <th style="width: 50%; text-align: left; padding: 6px 8px; font-size: 9px; text-transform: uppercase;">Descrição da Rubrica Fiscal</th>
            <th style="width: 15%; text-align: center; padding: 6px 8px; font-size: 9px; text-transform: uppercase;">Campo AT</th>
            <th style="width: 15%; text-align: center; padding: 6px 8px; font-size: 9px; text-transform: uppercase;">Taxa</th>
            <th style="width: 20%; text-align: right; padding: 6px 8px; font-size: 9px; text-transform: uppercase;">Montante (MT)</th>
        </tr>
        <tr>
            <td>Volume Global de Negócios / Faturação Total</td>
            <td class="text-center font-mono">C-00</td>
            <td class="text-center">-</td>
            <td class="text-right font-mono font-bold">{{ number_format($totalGrossRevenue, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>Transmissões de Bens e Serviços Isentos (Artigo 9º do CIVA)</td>
            <td class="text-center font-mono">C-01</td>
            <td class="text-center font-mono">0%</td>
            <td class="text-right font-mono">{{ number_format($exemptSalesTotal, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>Base de Incidência Tributável à Taxa Normal</td>
            <td class="text-center font-mono">C-03</td>
            <td class="text-center font-mono">16%</td>
            <td class="text-right font-mono">{{ number_format($taxableBase, 2, ',', '.') }} MT</td>
        </tr>
        <tr style="background-color: #f0fdf4;">
            <td><strong>TOTAL DO IVA LIQUIDADO (Cobrado nas Vendas)</strong></td>
            <td class="text-center font-mono font-bold">C-10</td>
            <td class="text-center font-mono font-bold">16%</td>
            <td class="text-right font-mono font-bold" style="color: #15803d;">{{ number_format($ivaLiquidado, 2, ',', '.') }} MT</td>
        </tr>
        <tr>
            <td>Total de Aquisições / Despesas Documentadas do Período</td>
            <td class="text-center font-mono">C-15</td>
            <td class="text-center">-</td>
            <td class="text-right font-mono">{{ number_format($deductibleExpenseTotal, 2, ',', '.') }} MT</td>
        </tr>
        <tr style="background-color: #faf5ff;">
            <td><strong>TOTAL DO IVA DEDUTÍVEL (Suportado em Aquisições)</strong></td>
            <td class="text-center font-mono font-bold">C-20</td>
            <td class="text-center font-mono font-bold">16%</td>
            <td class="text-right font-mono font-bold" style="color: #7e22ce;">{{ number_format($ivaDedutivel, 2, ',', '.') }} MT</td>
        </tr>
    </table>

    <!-- QUADRO III: RESULTADO FISCAL -->
    @if($impostoAPagar > 0)
    <div class="result-box result-box-payable">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%;">
                    <div style="font-size: 11px; font-weight: bold; text-transform: uppercase;">
                        SALDO DEVEDOR: IMPOSTO A PAGAR AO ESTADO (CAMPO 30 / MODELO A)
                    </div>
                    <div style="font-size: 8.5px; margin-top: 3px;">
                        Montante exigível a pagar à Fazenda Pública / Autoridade Tributária de Moçambique até ao dia 15/20 do mês seguinte através da respetiva Guia DAR-B.
                    </div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="font-size: 16px; font-weight: bold;" class="font-mono">
                        {{ number_format($impostoAPagar, 2, ',', '.') }} MT
                    </div>
                </td>
            </tr>
        </table>
    </div>
    @elseif($creditoAReportar > 0)
    <div class="result-box result-box-credit">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%;">
                    <div style="font-size: 11px; font-weight: bold; text-transform: uppercase;">
                        SALDO CREDOR: CRÉDITO FISCAL A REPORTAR (CAMPO 31 / MODELO A)
                    </div>
                    <div style="font-size: 8.5px; margin-top: 3px;">
                        Excesso de imposto suportado dedutível a reportar para dedução em períodos fiscais subsequentes nos termos do Código do IVA.
                    </div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="font-size: 16px; font-weight: bold;" class="font-mono">
                        {{ number_format($creditoAReportar, 2, ',', '.') }} MT
                    </div>
                </td>
            </tr>
        </table>
    </div>
    @else
    <div class="result-box" style="background-color: #f1f5f9; border: 1px solid #94a3b8;">
        <strong>SALDO NULO:</strong> Não há valor a pagar nem crédito a reportar para o período fiscal.
    </div>
    @endif

    <!-- QUADRO IV: RELAÇÃO DE DOCUMENTOS FISCAIS EMITIDOS (AMOSTRA / RESUMO) -->
    <div class="section-title">II. Relação de Faturas e Vendas Emitidas no Período (IVA Liquidado)</div>
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 13%;">Data</th>
                <th style="width: 15%;">Documento</th>
                <th style="width: 28%;">Cliente / Entidade</th>
                <th style="width: 12%;">NUIT</th>
                <th style="width: 10%; text-align: center;">Regime</th>
                <th style="width: 11%; text-align: right;">Base</th>
                <th style="width: 11%; text-align: right;">IVA (MT)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales->take(25) as $sale)
                @php
                    $isExempt = ($sale->tax_regime === 'exempt' || (float)$sale->tax_rate == 0 || (float)$sale->tax_amount == 0);
                    $taxAmount = (float)$sale->tax_amount;
                    $calcBase = $isExempt ? (float)$sale->total_amount : ($sale->prices_include_tax ? max(0, (float)$sale->total_amount - $taxAmount) : max(0, ((float)$sale->subtotal - (float)$sale->discount_amount)));
                @endphp
                <tr>
                    <td class="font-mono">{{ $sale->created_at->format('d/m/Y') }}</td>
                    <td class="font-mono font-bold">{{ $sale->invoice_number ?? ('VD#' . $sale->id) }}</td>
                    <td>{{ Str::limit($sale->customer_name ?? 'Consumidor Final', 24) }}</td>
                    <td class="font-mono">{{ $sale->customer_nuit ?? '-' }}</td>
                    <td class="text-center">{{ $isExempt ? 'Isento' : '16%' }}</td>
                    <td class="text-right font-mono">{{ number_format($calcBase, 2, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold">{{ number_format($taxAmount, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 10px; color: #64748b;">Sem faturas registadas no período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($sales->count() > 25)
    <div style="font-size: 8px; color: #64748b; margin-top: -4px; margin-bottom: 10px;">
        * Apresentadas 25 de {{ $sales->count() }} faturas emitidas. Os totais refletem o apuramento integral de todos os documentos.
    </div>
    @endif

    <!-- DECLARAÇÃO E ASSINATURAS -->
    <table class="signature-table" style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="signature-box">
                    <strong>TÉCNICO DE CONTAS / CONTABILISTA CERTIFICADO</strong><br>
                    <span style="font-size: 8px; color: #64748b;">Assinatura & N.º da Ordem dos Contabilistas</span>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="signature-box">
                    <strong>GERÊNCIA / TITULAR DO SUJEITO PASSIVO</strong><br>
                    <span style="font-size: 8px; color: #64748b;">Assinatura & Carimbo Oficial</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Documento extraído através do ZBIZ+ ERP em {{ now()->format('d/m/Y H:i:s') }} • Conforme o Código do Imposto sobre o Valor Acrescentado (CIVA) da República de Moçambique.
    </div>

</body>
</html>

