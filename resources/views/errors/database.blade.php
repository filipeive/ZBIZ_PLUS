<!DOCTYPE html>
<html lang="pt" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Dados Indisponível | ZBIZ+ Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between p-4 sm:p-6 selection:bg-amber-500 selection:text-white">
    
    <!-- Top Bar -->
    <header class="max-w-4xl w-full mx-auto flex items-center justify-between py-2 border-b border-slate-800/80">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-400 p-0.5 shadow-lg shadow-amber-500/20">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <span class="font-black text-amber-400 text-lg tracking-wider">Z+</span>
                </div>
            </div>
            <div>
                <span class="font-extrabold text-white text-base tracking-tight">ZBIZ<span class="text-amber-400">+</span></span>
                <span class="text-[10px] block font-semibold text-slate-400 uppercase tracking-wider">Centro de Diagnóstico & Recuperação</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
            </span>
            <span class="text-xs font-bold text-amber-400/90 font-mono">DB_OFFLINE</span>
        </div>
    </header>

    <!-- Main Card -->
    <main class="max-w-2xl w-full mx-auto my-auto py-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl backdrop-blur-xl relative overflow-hidden space-y-6">
            
            <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/5 rounded-full blur-3xl pointer-events-none -mr-20 -mt-20"></div>

            <!-- Icon & Header -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                <div class="w-16 h-16 shrink-0 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-3xl shadow-lg shadow-amber-500/10">
                    <i class="fa-solid fa-database-slash animate-pulse"></i>
                </div>
                <div class="space-y-1.5">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[11px] font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                        Serviço de Dados Inacessível
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black font-heading text-white tracking-tight">
                        Base de Dados a Inicializar
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                        O ZBIZ+ não conseguiu conectar com o motor de base de dados local (<span class="text-slate-200 font-semibold">{{ strtoupper(config('database.default', 'mysql')) }}</span>). Isto é comum quando o computador acabou de ser reiniciado e o serviço ainda está a arrancar.
                    </p>
                </div>
            </div>

            <!-- Connection Diagnostic Panel -->
            <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-4 text-xs font-mono space-y-2">
                <div class="flex items-center justify-between text-slate-400 text-[11px] pb-1 border-b border-slate-800">
                    <span class="uppercase tracking-wider font-bold">Diagnóstico Técnico da Ligação</span>
                    <span class="text-amber-400">Tentativa Falhada</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-slate-300">
                    <div>
                        <span class="text-slate-500">Host / Porta:</span> 
                        <span class="font-bold text-white">{{ config('database.connections.' . config('database.default') . '.host', '127.0.0.1') }}:{{ config('database.connections.' . config('database.default') . '.port', '3306') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Base de Dados:</span> 
                        <span class="font-bold text-white">{{ config('database.connections.' . config('database.default') . '.database', 'zbizplus_db') }}</span>
                    </div>
                    @if(config('database.connections.' . config('database.default') . '.unix_socket'))
                    <div class="col-span-2">
                        <span class="text-slate-500">Socket:</span> 
                        <span class="text-amber-400">{{ config('database.connections.' . config('database.default') . '.unix_socket') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recovery Guide -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                    <i class="fa-solid fa-wrench text-amber-400"></i> Como Resolver em 1 Minuto:
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="bg-slate-800/40 border border-slate-800 rounded-xl p-3.5 space-y-1.5">
                        <span class="font-bold text-white flex items-center gap-1.5">
                            <i class="fa-brands fa-windows text-sky-400"></i> No Windows (XAMPP / Serviço)
                        </span>
                        <p class="text-[11px] text-slate-400 leading-normal">
                            Abra o <strong>XAMPP</strong> e clique em <strong>Start no MySQL</strong>, ou abra o CMD como Administrador e execute:
                        </p>
                        <code class="block bg-slate-950 px-2 py-1 rounded text-[10px] text-emerald-400 font-mono select-all">
                            net start mysql
                        </code>
                    </div>

                    <div class="bg-slate-800/40 border border-slate-800 rounded-xl p-3.5 space-y-1.5">
                        <span class="font-bold text-white flex items-center gap-1.5">
                            <i class="fa-brands fa-linux text-amber-400"></i> No Linux / Servidor
                        </span>
                        <p class="text-[11px] text-slate-400 leading-normal">
                            Inicie o serviço de base de dados no terminal com:
                        </p>
                        <code class="block bg-slate-950 px-2 py-1 rounded text-[10px] text-emerald-400 font-mono select-all">
                            sudo systemctl start mariadb
                        </code>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex flex-col sm:flex-row gap-3">
                <button id="btnRetry" onclick="retryConnection()" class="flex-1 py-3.5 px-5 rounded-2xl bg-amber-500 hover:bg-amber-400 active:scale-95 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20 transition flex items-center justify-center gap-2">
                    <i id="retryIcon" class="fa-solid fa-rotate text-sm"></i>
                    <span id="retryText">Tentar Novamente (<span id="countdown">10</span>s)</span>
                </button>

                @php
                    $phoneSupport = '258862134230';
                    $msgSupport = "Olá Suporte Técnico Fdsmultiservices, o meu terminal ZBIZ+ (" . request()->getHost() . ") apresentou aviso de Base de Dados Offline. Solicito apoio.";
                @endphp
                <a href="https://wa.me/{{ $phoneSupport }}?text={{ rawurlencode($msgSupport) }}" target="_blank" class="py-3.5 px-5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs border border-slate-700 transition flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-sm"></i>
                    <span>Contactar Suporte</span>
                </a>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-4xl w-full mx-auto text-center py-3 border-t border-slate-800/80 text-[11px] text-slate-500 space-y-1">
        <p>Desenvolvido por <strong>Fdsmultiservices</strong> • Suporte Técnico Moçambique: (+258) 86 213 4230</p>
        <p class="text-[10px] text-slate-600">ZBIZ+ Enterprise POS & ERP Suite • Modo de Resiliência Ativo</p>
    </footer>

    <script>
        let seconds = 10;
        const countdownEl = document.getElementById('countdown');
        const retryBtn = document.getElementById('btnRetry');
        const retryIcon = document.getElementById('retryIcon');
        const retryText = document.getElementById('retryText');

        const timer = setInterval(() => {
            seconds--;
            if (countdownEl) countdownEl.innerText = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                retryConnection();
            }
        }, 1000);

        function retryConnection() {
            clearInterval(timer);
            if (retryIcon) retryIcon.classList.add('fa-spin');
            if (retryText) retryText.innerText = 'A verificar ligação...';
            window.location.reload();
        }
    </script>
</body>
</html>

