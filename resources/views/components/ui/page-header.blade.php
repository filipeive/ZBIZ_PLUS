{{--
  Componente: x-ui.page-header
  Props:
    - title: string
    - description: string (opcional)
    - icon: string (opcional, classe FontAwesome)
--}}
@props([
    'title',
    'description' => null,
    'subtitle' => null,
    'icon' => null,
])

@php
    $headerDesc = $description ?? $subtitle ?? null;
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm']) }}>
    <div class="flex items-center gap-4">
        @if($icon)
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold border border-emerald-500/20 shadow-sm shrink-0">
                <i class="{{ $icon }}"></i>
            </div>
        @endif
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $title }}</h1>
            @if($headerDesc)
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $headerDesc }}</p>
            @endif
        </div>
    </div>
    @if(isset($actions) && $actions->isNotEmpty())
        <div class="flex items-center gap-2.5 flex-wrap">
            {{ $actions }}
        </div>
    @elseif($slot->isNotEmpty())
        <div class="flex items-center gap-2.5 flex-wrap">
            {{ $slot }}
        </div>
    @endif
</div>
