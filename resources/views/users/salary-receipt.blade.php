<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Salário - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #10b981;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #111;
        }
        .receipt-title {
            text-align: right;
            font-size: 18px;
            color: #10b981;
            text-transform: uppercase;
        }
        .details-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .details-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .label {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            font-weight: bold;
        }
        .value {
            font-size: 14px;
            font-weight: bold;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .salary-table th {
            text-align: left;
            border-bottom: 2px solid #333;
            padding: 10px 5px;
            text-transform: uppercase;
            font-size: 11px;
        }
        .salary-table td {
            padding: 15px 5px;
            border-bottom: 1px solid #eee;
        }
        .amount {
            text-align: right;
        }
        .total-row td {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 16px;
            border-bottom: none;
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
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
        .notes-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td>
                    <div class="company-name">REPROSYS</div>
                    <div style="color: #666;">Gestão de Reprografia e Serviços</div>
                    <div style="color: #666;">Maputo, Moçambique</div>
                </td>
                <td class="receipt-title">
                    Recibo de Salário<br>
                    <small style="color: #999; font-size: 12px;">#PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</small>
                </td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td width="50%">
                    <div class="label">Funcionário</div>
                    <div class="value">{{ $user->name }}</div>
                </td>
                <td width="50%">
                    <div class="label">Mês de Referência</div>
                    <div class="value">{{ $payment->reference_month ? $payment->reference_month->format('m/Y') : $payment->payment_date->format('m/Y') }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">Cargo</div>
                    <div class="value">{{ $user->job_title ?: 'Colaborador' }}</div>
                </td>
                <td>
                    <div class="label">Data de Pagamento</div>
                    <div class="value">{{ $payment->payment_date->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>

        <table class="salary-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th class="amount">Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Salário Base</td>
                    <td class="amount">MT {{ number_format($payment->base_amount, 2, ',', '.') }}</td>
                </tr>
                @if($payment->variable_amount != 0)
                <tr>
                    <td>Remuneração Variável / Ajuste</td>
                    <td class="amount">MT {{ number_format($payment->variable_amount, 2, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total Líquido Recebido</td>
                    <td class="amount" style="color: #10b981;">MT {{ number_format($payment->amount, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        @if($payment->notes)
        <div class="notes-box">
            <div class="label">Observações</div>
            <div style="margin-top: 5px;">{{ $payment->notes }}</div>
        </div>
        @endif

        <table class="footer-table">
            <tr>
                <td class="signature-box">
                    <div class="signature-line">Pela Empresa</div>
                    <div style="font-size: 10px; color: #999;">{{ $payment->payer->name ?? 'Reprosys' }}</div>
                </td>
                <td width="10%">&nbsp;</td>
                <td class="signature-box">
                    <div class="signature-line">Assinatura do Funcionário</div>
                    <div style="font-size: 10px; color: #999;">{{ $user->name }}</div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 100px; text-align: center; color: #ccc; font-size: 9px; text-transform: uppercase;">
            Este documento serve como prova de pagamento para todos os efeitos legais.
        </div>
    </div>
</body>
</html>
