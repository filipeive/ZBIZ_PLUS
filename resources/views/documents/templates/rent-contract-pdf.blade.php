<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Contrato de Arrendamento Comercial</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 12px; line-height: 1.55; }
        .container { padding: 24px 28px; }
        h1 { font-size: 20px; text-align: center; margin: 0 0 6px; }
        h2 { font-size: 13px; margin: 18px 0 8px; text-transform: uppercase; }
        p { margin: 0 0 10px; text-align: justify; }
        .parties, .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .parties td { vertical-align: top; width: 50%; padding: 8px 10px 8px 0; }
        .table th, .table td { border: 1px solid #d6d6d6; padding: 8px; }
        .table th { background: #f2f4f7; text-align: left; }
        .muted { color: #666; }
        .signature-grid { width: 100%; margin-top: 40px; }
        .signature-grid td { width: 50%; text-align: center; vertical-align: top; padding: 0 10px; }
        .signature-line { margin-top: 65px; border-top: 1px solid #111; padding-top: 6px; font-size: 11px; }
        .small { font-size: 11px; }
        .section { margin-top: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>CONTRATO DE ARRENDAMENTO COMERCIAL</h1>
        <p class="muted" style="text-align:center;">com Acordo de Reabilitação do Estabelecimento</p>

        <table class="parties">
            <tr>
                <td>
                    <h2>Arrendador</h2>
                    <p><strong>Nome:</strong> {{ $contract['landlord_name'] }}</p>
                    <p><strong>Estado Civil:</strong> {{ $contract['landlord_marital_status'] }}</p>
                    <p><strong>Documento:</strong> {{ $contract['landlord_document'] }}</p>
                    <p><strong>Morada:</strong> {{ $contract['landlord_address'] }}</p>
                </td>
                <td>
                    <h2>Arrendatário</h2>
                    <p><strong>Nome:</strong> {{ $contract['tenant_name'] }}</p>
                    <p><strong>Estado Civil:</strong> {{ $contract['tenant_marital_status'] }}</p>
                    <p><strong>Documento:</strong> {{ $contract['tenant_document'] }}</p>
                    <p><strong>Morada:</strong> {{ $contract['tenant_address'] }}</p>
                </td>
            </tr>
        </table>

        <div class="section">
            <p><strong>Cláusula 1. Objecto.</strong> O presente contrato tem por objecto o arrendamento do estabelecimento comercial localizado em <strong>{{ $contract['property_location'] }}</strong>, destinado ao exercício da actividade de <strong>{{ $contract['business_activity'] }}</strong>.</p>
            <p><strong>Cláusula 2. Prazo.</strong> O arrendamento tem prazo <strong>{{ $contract['contract_term'] }}</strong>, com início em <strong>{{ \Carbon\Carbon::parse($contract['contract_start_date'])->format('d/m/Y') }}</strong>, podendo ser renovado por acordo entre as partes.</p>
            <p><strong>Cláusula 3. Renda.</strong> A renda mensal acordada é de <strong>MT {{ number_format((float) $contract['monthly_rent'], 2, ',', '.') }}</strong>, pagável até ao dia <strong>{{ $contract['payment_day'] }}</strong> de cada mês, através de <strong>{{ $contract['payment_methods'] }}</strong>.</p>
            <p><strong>Cláusula 4. Aviso prévio.</strong> Em caso de rescisão ou necessidade de saída, deverá existir aviso prévio mínimo de <strong>{{ $contract['prior_notice_days'] }}</strong> dias.</p>
        </div>

        <div class="section">
            <h2>Reabilitação do Estabelecimento</h2>
            <p>As partes reconhecem que o espaço necessitou de reabilitação prévia ao arrendamento. O investimento será recuperado por dedução mensal conforme descrito abaixo.</p>

            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 70%;">Área de Trabalho</th>
                        <th style="width: 30%;">Valor (MT)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract['rehab_items'] as $item)
                        <tr>
                            <td>{{ $item['label'] }}</td>
                            <td>MT {{ number_format((float) $item['amount'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td><strong>Total do Investimento</strong></td>
                        <td><strong>MT {{ number_format((float) $contract['rehab_total_investment'], 2, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <p><strong>Mecanismo de dedução:</strong> durante o período de abatimento, o arrendatário paga <strong>MT {{ number_format((float) $contract['rent_paid_amount'], 2, ',', '.') }}</strong> por mês, enquanto <strong>MT {{ number_format((float) $contract['rehab_monthly_deduction'], 2, ',', '.') }}</strong> são deduzidos ao saldo da reabilitação, por cerca de <strong>{{ $contract['rehab_estimated_months'] }}</strong> meses.</p>
        </div>

        @if(!empty($contract['special_clauses']))
            <div class="section">
                <h2>Cláusulas Complementares</h2>
                <p>{{ $contract['special_clauses'] }}</p>
            </div>
        @endif

        <div class="section">
            <p>Feito em duas vias de igual teor e valor, ficando um exemplar com cada uma das partes.</p>
            <p><strong>Local:</strong> {{ $contract['issue_location'] }} &nbsp;&nbsp;&nbsp; <strong>Data:</strong> ____ / ____ / ______</p>
        </div>

        <table class="signature-grid">
            <tr>
                <td>
                    <div class="signature-line">O ARRENDADOR</div>
                    <div class="small">{{ $contract['landlord_name'] }}</div>
                </td>
                <td>
                    <div class="signature-line">O ARRENDATÁRIO</div>
                    <div class="small">{{ $contract['tenant_name'] }}</div>
                </td>
            </tr>
        </table>

        <table class="signature-grid">
            <tr>
                <td>
                    <div class="signature-line">TESTEMUNHA 1</div>
                </td>
                <td>
                    <div class="signature-line">TESTEMUNHA 2</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
