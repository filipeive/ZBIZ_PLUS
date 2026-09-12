<!DOCTYPE html>
<html lang="pt" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sessão Expirada | ZBIZ PLUS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <div class="w-20 h-20 rounded-3xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-3xl mx-auto">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div>
            <span class="text-xs font-black font-mono uppercase tracking-widest text-blue-400">Erro 419</span>
            <h1 class="text-2xl font-black font-heading text-white mt-1">Sessão Expirada</h1>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                O token de segurança da sessão expirou por inatividade. Atualize a página ou efetue login novamente.
            </p>
        </div>
        <div class="pt-2 flex flex-col gap-2">
            <a href="{{ route('login') }}" class="w-full py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md hover:scale-105 active:scale-95 transition flex items-center justify-center">
                <i class="fa-solid fa-right-to-bracket mr-1"></i> Ir para o Login
            </a>
            <button onclick="window.location.reload()" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Recarregar Página
            </button>
        </div>
    </div>
</body>
</html>
