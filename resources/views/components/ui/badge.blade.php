{{--
  Componente: x-ui.badge (ou status-pill)
  Props:
    - variant: 'success' | 'warning' | 'danger' | 'info' | 'neutral' (padrão)
    - size: 'sm' | 'md' (padrão)
--}}
@props([
    'variant' => 'neutral',
    'size' => 'md',
])

@php
    $baseClasses = 'inline-flex items-center font-bold tracking-tight rounded-full uppercase';

    $sizes = [
        'sm' => 'px-2 py-0.5 text-[10px] gap-1',
        'md' => 'px-2.5 py-1 text-xs gap-1.5',
    ];

    $variants = [
        'success' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20',
        'warning' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20',
        'danger' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20',
        'info' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20',
        'neutral' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700',
    ];

    $classes = $baseClasses . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
