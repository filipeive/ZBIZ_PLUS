<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Renda - {{ $expense->expense_date->format('m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #2d3436;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 30px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #f1f2f6;
            margin-bottom: 30px;
            padding-bottom: 15px;
        }
        .logo-text {
            font-size: 28px;
            font-weight: bold;
            color: #2d3436;
        }
        .receipt-no {
            text-align: right;
        }
        .receipt-no h2 {
            margin: 0;
            color: #00b894;
            font-size: 18px;
            text-transform: uppercase;
        }
        .content-box {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 40px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 10px 0;
            border-bottom: 1px dashed #dfe6e9;
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .label {
            width: 150px;
            font-size: 10px;
            text-transform: uppercase;
            color: #636e72;
            font-weight: bold;
        }
        .value {
            font-size: 15px;
            font-weight: bold;
        }
        .amount-text {
            font-size: 22px;
            color: #d63031;
        }
        .footer-table {
            width: 100%;
            margin-top: 60px;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            border-top: 2px solid #2d3436;
            margin-top: 50px;
            padding-top: 8px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td>
                    <div class="logo-text">{{ $contract['tenant_name'] ?? 'REPROSYS' }}</div>
                    <div style="color: #636e72;">{{ $contract['business_activity'] ?? 'Serviços de Reprografia' }}</div>
                </td>
                <td class="receipt-no">
                    <h2>Recibo de Renda</h2>
                    <div style="color: #b2bec3; font-weight: bold;">Ref: {{ $expense->expense_date->format('m/Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="content-box">
            <table class="info-table">
                <tr>
                    <td class="label">Recebemos de</td>
                    <td class="value">Senhor {{ $contract['landlord_name'] ?? 'Proprietário / Gerente do Imóvel' }}</td>
                </tr>
                <tr>
                    <td class="label">Mês Referência</td>
                    <td class="value">{{ $expense->expense_date->format('m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Localização</td>
                    <td class="value">{{ $contract['property_location'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label">A Importância de</td>
                    <td class="value amount-text">MT {{ number_format($expense->amount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Referente a</td>
                    <td class="value">{{ $expense->description }}</td>
                </tr>
                <tr>
                    <td class="label">Data de Emissão</td>
                    <td class="value">{{ $expense->expense_date->format('d \d\e F \d\e Y') }}</td>
                </tr>
                @if($expense->notes)
                <tr>
                    <td class="label">Observações</td>
                    <td class="value" style="font-size: 13px; font-weight: normal; color: #636e72;">{{ $expense->notes }}</td>
                </tr>
                @endif
            </table>
        </div>

        <table class="footer-table">
            <tr>
                <td class="signature-box">
                    <div class="signature-line">Assinatura do Locador</div>
                    <div style="font-size: 10px; color: #636e72;">{{ $contract['landlord_name'] ?? 'Proprietário / Gerente do Imóvel' }}</div>
                </td>
                <td width="10%">&nbsp;</td>
                <td class="signature-box">
                    <div class="signature-line">Assinatura do Locatário</div>
                    <div style="font-size: 10px; color: #636e72;">{{ $contract['tenant_name'] ?? 'Responsável Reprosys' }}</div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 80px; text-align: center; font-size: 9px; color: #b2bec3; text-transform: uppercase; letter-spacing: 1px;">
            Este documento é um comprovante de quitação de aluguer.
        </div>
    </div>
</body>
</html>
