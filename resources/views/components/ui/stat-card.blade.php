{{--
  Componente: x-ui.stat-card
  Props:
    - title: string
    - value: string|numeric
    - unit: string (opcional, ex: 'MT', 'itens', '%')
    - subtitle: string (opcional)
    - icon: string (opcional)
    - color: 'emerald' | 'amber' | 'rose' | 'sky' | 'teal' | 'slate' (padrão)
--}}
@props([
    'title' => null,
    'label' => null,
    'value',
    'unit' => null,
    'subtitle' => null,
    'description' => null,
    'icon' => null,
    'color' => 'emerald',
])

@php
    $cardTitle = $title ?? $label ?? '';
    $cardSubtitle = $subtitle ?? $description ?? null;
    $colors = [
        'emerald' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        'teal' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
        'amber' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        'rose' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        'sky' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
        'slate' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
    ];

    $badgeStyle = $colors[$color] ?? $colors['emerald'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between']) }}>
    <div class="flex items-center justify-between gap-2 mb-3">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $cardTitle }}</span>
        @if($icon)
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm border {{ $badgeStyle }}">
                <i class="{{ $icon }}"></i>
            </div>
        @endif
    </div>
    <div>
        <div class="text-2xl font-black tracking-tight text-slate-900 dark:text-white tabular-nums">
            {{ $value }}
            @if($unit)
                <span class="text-xs font-normal text-slate-400 ms-1">{{ $unit }}</span>
            @endif
        </div>
        @if($cardSubtitle)
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">{{ $cardSubtitle }}</p>
        @endif
    </div>
</div>
