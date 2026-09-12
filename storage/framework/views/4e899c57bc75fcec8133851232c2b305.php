<!DOCTYPE html>
<html lang="pt" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página Não Encontrada | ZBIZ PLUS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <div class="w-20 h-20 rounded-3xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-3xl mx-auto">
            <i class="fa-solid fa-compass"></i>
        </div>
        <div>
            <span class="text-xs font-black font-mono uppercase tracking-widest text-amber-400">Erro 404</span>
            <h1 class="text-2xl font-black font-heading text-white mt-1">Página Não Encontrada</h1>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                O endereço ou rota solicitada não existe, foi desativada ou movida permanentemente.
            </p>
        </div>
        <div class="pt-2 flex flex-col gap-2">
            <a href="<?php echo e(url('/dashboard')); ?>" class="w-full py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md hover:scale-105 active:scale-95 transition flex items-center justify-center">
                <i class="fa-solid fa-house mr-1"></i> Ir para o Painel Principal
            </a>
            <button onclick="window.history.back()" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Voltar
            </button>
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/errors/404.blade.php ENDPATH**/ ?>