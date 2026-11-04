{{--
  Componente: x-brand.logo
  Props:
    - variant: 'full' (ícone + texto, padrão) | 'icon' (apenas símbolo)
    - theme: 'light' | 'dark' | 'auto' (padrão)
    - size: 'sm' (h-7) | 'md' (h-9, padrão) | 'lg' (h-12)
--}}
@props([
    'variant' => 'full',
    'theme' => 'auto',
    'size' => 'md',
])

@php
    $heights = [
        'sm' => 'h-7',
        'md' => 'h-9',
        'lg' => 'h-12',
    ];
    $hClass = $heights[$size] ?? $heights['md'];
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center gap-2.5 select-none $hClass"]) }}>
    {{-- Ícone / Símbolo da Marca --}}
    <div class="relative aspect-square h-full rounded-xl bg-slate-900 border border-slate-800 p-1.5 flex items-center justify-center shrink-0 shadow-sm overflow-hidden">
        <svg viewBox="0 0 48 48" fill="none" class="w-full h-full">
            <path d="M12 14H36L18 34H36" stroke="#10b981" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M34 18V26M30 22H38" stroke="#34d399" stroke-width="3" stroke-linecap="round"/>
            <circle cx="13" cy="34" r="2.5" fill="#10b981"/>
        </svg>
    </div>

    @if($variant === 'full')
        <div class="flex flex-col leading-none">
            <span class="text-lg font-black tracking-tight text-slate-900 dark:text-white">
                ZBIZ<span class="text-emerald-500">+</span>
            </span>
            <span class="text-[9px] font-bold tracking-widest text-slate-400 dark:text-slate-500 uppercase mt-0.5">
                Gestão &amp; POS
            </span>
        </div>
    @endif
</div>
