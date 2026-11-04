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
    {{-- Símbolo Canónico da Marca --}}
    <div class="relative aspect-square h-full shrink-0 flex items-center justify-center">
        <svg viewBox="0 0 48 48" fill="none" class="w-full h-full drop-shadow-sm">
            <rect width="48" height="48" rx="12" fill="#0f172a"/>
            <path d="M12 18H27L12 33H33" stroke="#10b981" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M36 10.5V19.5M31.5 15H40.5" stroke="#34d399" stroke-width="3.5" stroke-linecap="round"/>
        </svg>
    </div>

    @if($variant === 'full')
        <div class="flex items-center leading-none">
            <span class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
                ZBIZ<span class="text-emerald-500">+</span>
            </span>
        </div>
    @endif
</div>
