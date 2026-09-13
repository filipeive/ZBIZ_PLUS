<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Certificado de Licença - {{ $tenant->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-size: 12px;
            line-height: 1.5;
        }
        .cert-container {
            border: 5px double #059669;
            padding: 25px;
            position: relative;
            background: #ffffff;
            min-height: 960px;
            box-sizing: border-box;
        }
        .cert-inner {
            border: 1px solid #cbd5e1;
            padding: 25px;
            height: 100%;
            box-sizing: border-box;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -1px;
        }
        .logo-plus {
            color: #059669;
        }
        .logo-sub {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #64748b;
            font-weight: bold;
            margin-top: 3px;
        }
        .ref-box {
            text-align: right;
        }
        .badge {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .ref-code {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            color: #64748b;
            margin-top: 5px;
        }
        .title-section {
            text-align: center;
            margin: 25px 0;
        }
        .cert-title {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0 0 8px 0;
            letter-spacing: 0.5px;
        }
        .cert-desc {
            font-size: 11px;
            color: #475569;
            max-width: 520px;
            margin: 0 auto;
        }
        .key-box {
            background-color: #f8fafc;
            border: 2px dashed #059669;
            padding: 18px;
            text-align: center;
            margin: 25px 0;
            border-radius: 8px;
        }
        .key-label {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #047857;
            margin-bottom: 5px;
        }
        .key-code {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 2px;
        }
        .key-hint {
            font-size: 9px;
            color: #64748b;
            margin-top: 6px;
        }
        .info-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
        .card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }
        .card-title {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #047857;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .item-row {
            margin-bottom: 5px;
            font-size: 11px;
        }
        .item-label {
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
        }
        .item-value {
            font-weight: bold;
            color: #1e293b;
        }
        .security-box {
            margin-top: 20px;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 9px;
            color: #64748b;
        }
        .hash-code {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 8px;
            color: #334155;
            word-break: break-all;
        }
        .signatures-table {
            width: 100%;
            margin-top: 45px;
            border-collapse: collapse;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        .sign-line {
            border-top: 1px solid #94a3b8;
            margin-top: 40px;
            margin-bottom: 6px;
        }
        .sign-name {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .sign-role {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }
        .footer-note {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<div class="cert-container">
    <div class="cert-inner">

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="logo-text">ZBIZ<span class="logo-plus">+</span></div>
                    <div class="logo-sub">Enterprise Cloud & POS Suite · Moçambique</div>
                </td>
                <td class="ref-box">
                    <div class="badge">Certificado Oficial</div>
                    <div class="ref-code">Ref: ZB-LIC-{{ str_pad($license->id, 6, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>

        <!-- Certificate Title -->
        <div class="title-section">
            <h1 class="cert-title">Certificado de Ativação de Licença</h1>
            <p class="cert-desc">
                Certifica-se que a empresa abaixo identificada está legalmente autorizada a operar a plataforma de gestão empresarial <strong>ZBIZ+</strong> conforme o plano e modalidades contratadas.
            </p>
        </div>

        <!-- Serial Key Display Box -->
        <div class="key-box">
            <div class="key-label">Chave Serial de Software (License Key)</div>
            <div class="key-code">{{ $license->key_code }}</div>
            <div class="key-hint">Insira este código na tela de ativação para desbloqueio ou renovação imediata da instalação.</div>
        </div>

        <!-- Details Grid -->
        <table class="info-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Entidade Licenciada</div>
                        <div class="item-row">
                            <span class="item-label">Empresa / Razão Social:</span><br>
                            <span class="item-value" style="font-size: 13px;">{{ $tenant->name }}</span>
                        </div>
                        <div class="item-row">
                            <span class="item-label">NUIT:</span><br>
                            <span class="item-value">{{ $tenant->nuit ?? 'Não registado' }}</span>
                        </div>
                        <div class="item-row">
                            <span class="item-label">Ramo de Atividade:</span><br>
                            <span class="item-value">{{ $tenant->business_type_label ?? ucfirst($tenant->business_type) }}</span>
                        </div>
                        <div class="item-row">
                            <span class="item-label">Contacto / E-mail:</span><br>
                            <span class="item-value">{{ $tenant->phone ?? '-' }} · {{ $tenant->email ?? '-' }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">Especificações da Licença</div>
                        <div class="item-row">
                            <span class="item-label">Plano Contratado:</span><br>
                            <span class="item-value" style="font-size: 13px; color: #047857;">{{ $license->plan?->name ?? 'Plano Empresarial' }}</span>
                        </div>
                        <div class="item-row">
                            <span class="item-label">Modalidade de Instalação:</span><br>
                            <span class="item-value" style="text-transform: uppercase;">{{ $license->mode ?? $tenant->installation_mode ?? 'Cloud' }}</span>
                        </div>
                        <div class="item-row">
                            <span class="item-label">Período de Validade:</span><br>
                            <span class="item-value">{{ $license->starts_at?->format('d/m/Y') }} até {{ $license->expires_at?->format('d/m/Y') }}</span>
                        </div>
                        <div class="item-row">
                            <span class="item-label">Estado da Licença:</span><br>
                            <span class="item-value" style="text-transform: uppercase; color: #047857;">{{ $license->status }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Security & Authenticity Hash -->
        <div class="security-box">
            <div style="font-weight: bold; margin-bottom: 2px; color: #0f172a;">Assinatura Digital de Autenticidade (SHA-256 HMAC):</div>
            <div class="hash-code">{{ $license->key_hash }}</div>
            <div style="margin-top: 4px;">
                Emitido por: <strong>{{ $license->issuer?->name ?? 'Administração Fdsmultiservices' }}</strong> em {{ $license->created_at?->format('d/m/Y \à\s H:i') }}.
            </div>
        </div>

        <!-- Signatures -->
        <table class="signatures-table">
            <tr>
                <td>
                    <div class="sign-line"></div>
                    <div class="sign-name">Fdsmultiservices</div>
                    <div class="sign-role">Emissor / Dono da Plataforma</div>
                </td>
                <td>
                    <div class="sign-line"></div>
                    <div class="sign-name">{{ $tenant->name }}</div>
                    <div class="sign-role">Representante da Empresa Licenciada</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer-note">
            Este certificado é emitido digitalmente pela plataforma ZBIZ+ (desenvolvida por Fdsmultiservices · Suporte: +258 86 213 4230). A ativação da chave pode ser verificada em tempo real no endereço oficial do sistema.
        </div>

    </div>
</div>

</body>
</html>
