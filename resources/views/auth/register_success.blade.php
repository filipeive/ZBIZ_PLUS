<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-Registo Concluído - ZBIZ+</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
        .aura-bg {
            background-color: #1e3a4c;
            background-image:
                radial-gradient(at 10% 20%, #115e59 0px, transparent 50%),
                radial-gradient(at 90% 15%, #0369a1 0px, transparent 50%),
                radial-gradient(at 80% 80%, #38bdf8 0px, transparent 50%),
                radial-gradient(at 20% 85%, #475569 0px, transparent 50%),
                radial-gradient(at 50% 50%, #0f766e 0px, transparent 50%);
            background-size: cover;
            background-attachment: fixed;
        }
        .success-card .text-white,
        .success-card .text-slate-200,
        .success-card .text-slate-300 {
            color: #1e293b !important;
        }
        .success-card .text-slate-400,
        .success-card .text-slate-500 {
            color: #64748b !important;
        }
        .success-card .bg-slate-950\/80,
        .success-card .bg-slate-900 {
            background-color: #f8fafc !important;
        }
        .success-card .border-slate-800,
        .success-card .border-slate-800\/80,
        .success-card .border-slate-800\/60 {
            border-color: #e2e8f0 !important;
        }
        .success-card .text-emerald-400,
        .success-card .text-emerald-300 {
            color: #0284c7 !important;
        }
        .success-card .bg-emerald-600 {
            background-color: #005c68 !important;
            color: #fff !important;
        }
        .success-card .bg-slate-800 {
            background-color: #e2e8f0 !important;
            color: #334155 !important;
        }
    </style>
</head>
<body class="aura-bg min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden antialiased selection:bg-sky-500 selection:text-white">

    <div class="w-full max-w-xl relative z-10 my-8">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-sky-600 text-white flex items-center justify-center shadow-lg shadow-sky-500/25 overflow-hidden">
                    <img src="{{ asset('favicon.png') }}" alt="Z+" class="w-full h-full object-contain p-1">
                </div>
                <span class="text-3xl font-black font-heading text-white">ZBIZ+</span>
            </a>
        </div>

        <!-- Success Card -->
        <div class="success-card bg-white/95 border border-white/60 rounded-[2.5rem] p-8 shadow-2xl shadow-slate-950/30 backdrop-blur-xl">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-amber-500/10 border-2 border-amber-500/30 text-amber-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    <span>Aguardando Aprovação da Fdsmultiservices</span>
                </div>
                <h1 class="text-2xl font-black font-heading text-white">Pré-Registo Submetido!</h1>
                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                    A sua solicitação foi recebida pelo administrador da plataforma. Iremos definir o seu período de teste e enviar um SMS com o link oficial de acesso.
                </p>
            </div>

            <!-- SMS Delivery Notice -->
            <div class="mb-6 p-4 bg-emerald-950/30 border border-emerald-500/30 rounded-2xl flex items-start gap-3.5">
                <div class="text-emerald-400 text-lg mt-0.5"><i class="fa-solid fa-mobile-screen-button"></i></div>
                <div class="text-xs">
                    <div class="font-bold text-emerald-300">Como funciona o próximo passo?</div>
                    <p class="text-slate-300 mt-1 leading-relaxed">
                        Assim que a nossa equipa aprovar o seu pedido, receberá uma mensagem <strong>SMS</strong> no telemóvel <strong class="text-white font-mono">{{ session('reg_phone') }}</strong> com o link de ativação e a confirmação dos dias de teste do plano <strong class="text-emerald-400">{{ session('reg_plan_name', 'ZBIZ Starter') }}</strong>.
                    </p>
                </div>
            </div>

            <!-- Registration Summary Box -->
            <div class="bg-slate-950/80 border border-slate-800/80 rounded-2xl p-5 mb-6 space-y-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 border-b border-slate-800 pb-2 flex justify-between items-center">
                    <span>Resumo da Solicitação</span>
                    <span class="text-emerald-400 font-normal">Plano: {{ session('reg_plan_name', 'ZBIZ Starter') }}</span>
                </div>

                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Empresa:</span>
                    <span class="font-bold text-white">{{ session('reg_company_name') }}</span>
                </div>

                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Responsável:</span>
                    <span class="font-bold text-slate-200">{{ session('reg_admin_name') }}</span>
                </div>

                <div class="flex justify-between items-center text-xs pt-1">
                    <span class="text-slate-400">Telemóvel (SMS):</span>
                    <span class="font-mono font-bold text-emerald-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-800">{{ session('reg_phone') }}</span>
                </div>

                <div class="flex justify-between items-center text-xs pt-1">
                    <span class="text-slate-400">E-mail Registado:</span>
                    <span class="font-mono text-slate-300 bg-slate-900 px-2 py-0.5 rounded border border-slate-800">{{ session('reg_email') }}</span>
                </div>

                <div class="flex justify-between items-center text-xs pt-1">
                    <span class="text-slate-400">Estado:</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                        Pendente de Validação
                    </span>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="space-y-3">
                <a href="https://wa.me/258862134230?text={{ urlencode('Olá Fdsmultiservices, acabei de fazer o pré-registo no ZBIZ+ para a empresa ' . session('reg_company_name') . ' e gostaria de acelerar a aprovação do meu acesso.') }}" 
                   target="_blank"
                   class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 transition transform active:scale-95">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                    <span>Contactar Administrador no WhatsApp</span>
                </a>
                
                <a href="/" 
                   class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold rounded-xl text-xs flex items-center justify-center gap-2 transition">
                    <i class="fa-solid fa-house"></i>
                    <span>Voltar à Página Inicial</span>
                </a>
            </div>

            <!-- Support info -->
            <div class="mt-6 pt-4 border-t border-slate-800/60 text-center text-xs text-slate-400">
                <span>Dúvidas ou Suporte Imediato? WhatsApp: </span>
                <a href="https://wa.me/258862134230" target="_blank" class="text-emerald-400 font-bold hover:underline">
                    (+258) 86 213 4230
                </a>
                <span class="text-slate-600 mx-1">•</span>
                <span>Email: fdsmultiservices@gmail.com</span>
            </div>

        </div>

        <!-- Footer Watermark -->
        <div class="mt-8 text-center text-[11px] text-white/70 space-y-1">
            <p>Desenvolvido por <strong class="text-white">Fdsmultiservices</strong></p>
            <div class="flex items-center justify-center gap-3 text-white/70">
                <a href="https://wa.me/258862134230" target="_blank" class="hover:text-emerald-400 transition inline-flex items-center gap-1">
                    <i class="fa-brands fa-whatsapp text-emerald-400"></i> (+258) 86 213 4230
                </a>
                <span>•</span>
                <a href="mailto:fdsmultiservices@gmail.com" class="hover:text-emerald-400 transition inline-flex items-center gap-1">
                    <i class="fa-solid fa-envelope text-orange-400"></i> fdsmultiservices@gmail.com
                </a>
            </div>
        </div>

    </div>

</body>
</html>
