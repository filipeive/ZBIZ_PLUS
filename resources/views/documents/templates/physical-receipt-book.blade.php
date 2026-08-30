<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Livro de Recibos - Template Físico</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 10mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
        }
        .page {
            page-break-after: always;
            width: 100%;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 14px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header .subtitle {
            font-size: 9px;
            color: #555;
            margin-top: 3px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 8px;
        }
        .info-row strong {
            color: #333;
        }
        .table-header {
            background-color: #f0f0f0;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
        }
        .receipt-row {
            border-bottom: 1px dotted #ccc;
            height: 22px;
        }
        .receipt-row td {
            padding: 2px 4px;
            vertical-align: middle;
        }
        .receipt-row .num {
            width: 25px;
            text-align: center;
            font-weight: bold;
        }
        .receipt-row .date {
            width: 60px;
            text-align: center;
            font-size: 9px;
        }
        .receipt-row .payer {
            width: 70px;
        }
        .receipt-row .address {
            width: 80px;
        }
        .receipt-row .description {
            width: 90px;
        }
        .receipt-row .amount {
            width: 55px;
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
        }
        .receipt-row .sign {
            width: 60px;
            text-align: center;
            font-size: 8px;
        }
        .amount-ext {
            font-size: 9px;
            border-bottom: 1px solid #000;
            min-height: 14px;
            padding: 0 3px;
        }
        .sign-box {
            border-bottom: 1px solid #000;
            height: 20px;
            margin-top: 2px;
        }
        .legend {
            font-size: 7px;
            color: #666;
            margin-top: 3px;
            font-style: italic;
        }
        .page-number {
            text-align: center;
            font-size: 9px;
            color: #999;
            margin-top: 5px;
            border-top: 1px solid #ccc;
            padding-top: 3px;
        }
        .footer-note {
            font-size: 7px;
            color: #888;
            text-align: center;
            margin-top: 5px;
            font-style: italic;
        }
        .fillable-box {
            border: 1px solid #999;
            background-color: #fff;
            display: inline-block;
            min-width: 30px;
            text-align: center;
        }
        .instructions {
            font-size: 8px;
            color: #555;
            border: 1px solid #ccc;
            padding: 5px;
            margin: 5px 0;
            background-color: #fafafa;
        }
    </style>
</head>
<body>

    <div class="page">
        <div class="header">
            <h1>LIVRO DE RECIBOS</h1>
            <div class="subtitle">Arrendamento Comercial - Reprosys</div>
        </div>

        <div class="info-row">
            <span><strong>Arrendador:</strong> Filipe Domingos dos Santos</span>
            <span><strong>Arrendatário:</strong> Minora Nhatambo Paquete</span>
        </div>
        <div class="info-row">
            <span><strong>Morada:</strong> 1 de Maio B, Cidade de Quelimane</span>
            <span><strong>Local do Imóvel:</strong> Avenida Eduardo Mondlane</span>
        </div>
        <div class="info-row">
            <span><strong>Atividade:</strong> Reprografia, Serigrafia & Escritório</span>
            <span><strong>Renda Mensal:</strong> MT 5.000,00</span>
        </div>
        
        <div class="instructions">
            <strong>Instruções:</strong> Preencher cada linha com os dados do respectivo mês. 
            O valor por extenso deve ser escrito manualmente. Manter este livro para fins fiscais e de arquivo.
        </div>

        <table width="100%">
            <thead class="table-header">
                <tr>
                    <th class="num">Nº</th>
                    <th class="date">Data</th>
                    <th class="payer">Pagador</th>
                    <th class="address">Morada</th>
                    <th class="description">Descrição</th>
                    <th class="amount">Valor (MT)</th>
                    <th class="sign">Assinatura</th>
                </tr>
            </thead>
            <tbody>
                <!-- Linha 1: Janeiro -->
                <tr class="receipt-row">
                    <td class="num">1</td>
                    <td class="date">01/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Janeiro 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 2: Fevereiro -->
                <tr class="receipt-row">
                    <td class="num">2</td>
                    <td class="date">02/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Fevereiro 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 3: Março -->
                <tr class="receipt-row">
                    <td class="num">3</td>
                    <td class="date">03/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Março 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 4: Abril -->
                <tr class="receipt-row">
                    <td class="num">4</td>
                    <td class="date">04/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Abril 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 5: Maio -->
                <tr class="receipt-row">
                    <td class="num">5</td>
                    <td class="date">05/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Maio 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 6: Junho -->
                <tr class="receipt-row">
                    <td class="num">6</td>
                    <td class="date">06/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Junho 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 7: Julho -->
                <tr class="receipt-row">
                    <td class="num">7</td>
                    <td class="date">07/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Julho 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 8: Agosto -->
                <tr class="receipt-row">
                    <td class="num">8</td>
                    <td class="date">08/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Agosto 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 9: Setembro -->
                <tr class="receipt-row">
                    <td class="num">9</td>
                    <td class="date">09/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Setembro 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 10: Outubro -->
                <tr class="receipt-row">
                    <td class="num">10</td>
                    <td class="date">10/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Outubro 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 11: Novembro -->
                <tr class="receipt-row">
                    <td class="num">11</td>
                    <td class="date">11/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Novembro 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
                
                <!-- Linha 12: Dezembro -->
                <tr class="receipt-row">
                    <td class="num">12</td>
                    <td class="date">12/2026</td>
                    <td class="payer">Minora N.</td>
                    <td class="address">17 Setembro</td>
                    <td class="description">Renda mês Dezembro 2026</td>
                    <td class="amount">5.000,00</td>
                    <td class="sign">
                        <div class="sign-box"></div>
                    </td>
                </tr>
                <tr><td colspan="7" class="amount-ext">POR EXTENSO: CINCO MIL METICAIS</td></tr>
            </tbody>
        </table>

        <div class="page-number">
            Página 1 de 1
        </div>

        <div class="footer-note">
            Este documento serve como comprovativo de quitação de aluguer.
            <br>
            Conservar para fins fiscais e legais.
        </div>
    </div>
</body>
</html>
