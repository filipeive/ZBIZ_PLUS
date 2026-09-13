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
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden">

    <!-- Ambient Glow Background -->
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_70%_70%_at_50%_30%,rgba(16,185,129,0.15),rgba(255,255,255,0))] pointer-events-none"></div>

    <div class="w-full max-w-xl relative z-10 my-8">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-emerald-500/30 flex items-center justify-center shadow-lg shadow-emerald-500/20 overflow-hidden">
                    <img src="{{ asset('favicon.png') }}" alt="Z+" class="w-full h-full object-cover">
                </div>
                <span class="text-3xl font-black font-heading text-white">ZBIZ<span class="text-emerald-400">+</span></span>
            </a>
        </div>

        <!-- Success Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-emerald-500/10 border-2 border-emerald-500/30 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h1 class="text-2xl font-black font-heading text-white">Pré-Registo Concluído!</h1>
                <p class="text-xs text-slate-400 mt-1">A sua empresa foi configurada e está pronta para operar.</p>
            </div>

            <!-- SMS Delivery Alert -->
            @if(session('reg_sms_sent'))
                <div class="mb-6 p-4 bg-emerald-950/40 border border-emerald-500/40 rounded-2xl flex items-start gap-3.5">
                    <div class="text-emerald-400 text-lg mt-0.5"><i class="fa-solid fa-mobile-screen-button"></i></div>
                    <div class="text-xs">
                        <div class="font-bold text-emerald-300">Credenciais Enviadas por SMS</div>
                        <p class="text-slate-300 mt-0.5">Enviámos uma mensagem SMS oficial com os seus dados de acesso para o telemóvel <strong class="text-white">{{ session('reg_phone') }}</strong>.</p>
                    </div>
                </div>
            @else
                <div class="mb-6 p-4 bg-amber-950/30 border border-amber-500/30 rounded-2xl flex items-start gap-3.5">
                    <div class="text-amber-400 text-lg mt-0.5"><i class="fa-solid fa-circle-info"></i></div>
                    <div class="text-xs">
                        <div class="font-bold text-amber-300">Aviso de Envio de SMS</div>
                        <p class="text-slate-300 mt-0.5">O telemóvel registado foi <strong class="text-white">{{ session('reg_phone') }}</strong>. Guarde as credenciais abaixo para acesso.</p>
                    </div>
                </div>
            @endif

            <!-- Credentials Box -->
            <div class="bg-slate-950/80 border border-slate-800/80 rounded-2xl p-5 mb-6 space-y-3.5" x-data="{ showPass: false, copied: false }">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 border-b border-slate-800 pb-2 flex justify-between items-center">
                    <span>Resumo das Credenciais</span>
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
                    <span class="text-slate-400">E-mail (Login):</span>
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-bold text-emerald-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-800">{{ session('reg_email') }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center text-xs pt-1">
                    <span class="text-slate-400">Senha:</span>
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-bold text-slate-200 bg-slate-900 px-2 py-0.5 rounded border border-slate-800" x-text="showPass ? '{{ session('reg_password') }}' : '••••••••'"></span>
                        <button type="button" @click="showPass = !showPass" class="text-slate-400 hover:text-white text-xs px-1.5 py-0.5 rounded transition">
                            <i :class="showPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Copy credentials button -->
                <div class="pt-3">
                    <button type="button" 
                            @click="navigator.clipboard.writeText('ZBIZ+ Login:\nEmail: {{ session('reg_email') }}\nSenha: {{ session('reg_password') }}\nLink: {{ url('/login') }}'); copied = true; setTimeout(() => copied = false, 3000)"
                            class="w-full py-2 bg-slate-900 hover:bg-slate-800 border border-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-semibold flex items-center justify-center gap-2 transition">
                        <i :class="copied ? 'fa-solid fa-check text-emerald-400' : 'fa-solid fa-copy'"></i>
                        <span x-text="copied ? 'Credenciais Copiadas!' : 'Copiar Credenciais'"></span>
                    </button>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="space-y-3">
                <a href="{{ route('dashboard.index') }}" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 transition transform active:scale-95">
                    <i class="fa-solid fa-gauge-high"></i> Entrar no Painel de Controlo
                </a>
                <a href="{{ route('pos.index') }}" class="w-full py-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition">
                    <i class="fa-solid fa-cash-register"></i> Aceder à Frente de Caixa (POS)
                </a>
            </div>

            <!-- Support footer -->
            <div class="mt-6 pt-5 border-t border-slate-800/60 text-center text-xs text-slate-400">
                <span>Dúvidas ou Suporte Imediato? Contacte-nos via WhatsApp: </span>
                <a href="https://wa.me/258862134230" target="_blank" class="text-emerald-400 font-bold hover:underline">
                    (+258) 86 213 4230
                </a>
            </div>

        </div>

        <!-- Footer Watermark -->
        <div class="mt-8 text-center text-[11px] text-slate-500 space-y-1">
            <p>Desenvolvido por <strong class="text-slate-400">Fdsmultiservices</strong></p>
            <div class="flex items-center justify-center gap-3 text-slate-500">
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
