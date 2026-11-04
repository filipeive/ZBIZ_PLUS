{{--
  Componente: x-ui.button
  Props:
    - variant: 'primary' (padrão) | 'secondary' | 'danger' | 'ghost'
    - size: 'sm' | 'md' (padrão) | 'lg'
    - type: 'button' (padrão) | 'submit' | 'reset'
    - loading: bool (falso por defeito)
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'loading' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold tracking-tight transition-all duration-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'md' => 'px-4 py-2.5 text-xs gap-2',
        'lg' => 'px-5 py-3 text-sm gap-2.5',
    ];

    $variants = [
        'primary' => 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white shadow-sm shadow-emerald-600/30 focus:ring-emerald-500',
        'secondary' => 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 focus:ring-slate-400',
        'danger' => 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white shadow-sm shadow-rose-600/30 focus:ring-rose-500',
        'ghost' => 'bg-transparent hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white focus:ring-slate-400',
    ];

    $classes = $baseClasses . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($loading)
        <i class="fa-solid fa-circle-notch fa-spin"></i>
    @endif
    {{ $slot }}
</button>
