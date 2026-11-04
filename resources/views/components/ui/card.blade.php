{{--
  Componente: x-ui.card
  Props:
    - padding: 'none' | 'sm' | 'md' (padrão) | 'lg'
    - hover: bool (efeito sutil de elevação)
--}}
@props([
    'padding' => 'md',
    'hover' => false,
])

@php
    $baseClasses = 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm transition-all duration-200';

    $paddings = [
        'none' => 'p-0',
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
    ];

    $hoverClass = $hover ? 'hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-0.5' : '';
    $classes = $baseClasses . ' ' . ($paddings[$padding] ?? $paddings['md']) . ' ' . $hoverClass;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
