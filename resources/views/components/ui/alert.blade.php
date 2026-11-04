{{--
  Componente: x-ui.alert (ou flash message)
  Props:
    - variant: 'success' | 'warning' | 'danger' | 'info' (padrão)
    - dismissible: bool (falso por padrão)
    - title: string (opcional)
--}}
@props([
    'variant' => 'info',
    'dismissible' => false,
    'title' => null,
])

@php
    $icons = [
        'success' => 'fa-solid fa-circle-check',
        'warning' => 'fa-solid fa-triangle-exclamation',
        'danger' => 'fa-solid fa-circle-exclamation',
        'info' => 'fa-solid fa-circle-info',
    ];

    $styles = [
        'success' => 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 icon:text-emerald-500',
        'warning' => 'bg-amber-50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 icon:text-amber-500',
        'danger' => 'bg-rose-50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 icon:text-rose-500',
        'info' => 'bg-sky-50 dark:bg-sky-950/20 border-sky-200 dark:border-sky-800 text-sky-800 dark:text-sky-200 icon:text-sky-500',
    ];

    $icon = $icons[$variant] ?? $icons['info'];
    $style = $styles[$variant] ?? $styles['info'];
@endphp

<div
    @if($dismissible) x-data="{ open: true }" x-show="open" @endif
    role="alert"
    {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 rounded-2xl border text-xs leading-relaxed $style"]) }}
>
    <i class="{{ $icon }} text-sm mt-0.5 shrink-0"></i>
    <div class="flex-1">
        @if($title)
            <h5 class="font-bold text-xs mb-0.5">{{ $title }}</h5>
        @endif
        <div class="space-y-1">
            {{ $slot }}
        </div>
    </div>
    @if($dismissible)
        <button type="button" @click="open = false" aria-label="Fechar alerta" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
            <i class="fa-solid fa-xmark"></i>
        </button>
    @endif
</div>
