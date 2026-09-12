<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - ZBIZ+</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Background Glow Effect -->
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_60%_60%_at_50%_40%,rgba(16,185,129,0.12),rgba(255,255,255,0))] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        
        <!-- Header Brand -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-bolt text-slate-950 text-lg font-black"></i>
                </div>
                <span class="text-3xl font-black font-heading text-white">ZBIZ<span class="text-emerald-400">+</span></span>
            </a>
            <p class="text-xs text-slate-400 mt-2">Acesse a sua conta empresarial</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl">
            
            <?php if(session('success')): ?>
                <div class="mb-5 p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-5 p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">E-mail Corporativo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                               placeholder="seu.email@empresa.co.mz"
                               class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-300">Senha de Acesso</label>
                        <?php if(Route::has('password.request')): ?>
                            <a href="<?php echo e(route('password.request')); ?>" class="text-[11px] text-emerald-400 hover:underline">Esqueceu a senha?</a>
                        <?php endif; ?>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center text-xs text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-500 focus:ring-emerald-500">
                        <span class="ml-2">Lembrar neste computador</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition transform active:scale-95">
                    Entrar no ZBIZ+
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-800/80 text-center">
                <p class="text-xs text-slate-400">
                    Ainda não tem conta empresarial?
                    <a href="<?php echo e(route('register')); ?>" class="text-emerald-400 font-bold hover:underline ml-1">Criar Grátis (30 Dias)</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/auth/login.blade.php ENDPATH**/ ?>