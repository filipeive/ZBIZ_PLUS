{{--
  Componente: x-ui.empty-state
  Props:
    - icon: string ('fa-solid fa-inbox' padrão)
    - title: string
    - description: string (opcional)
--}}
@props([
    'icon' => 'fa-solid fa-inbox',
    'title' => 'Nenhum registo encontrado',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4 rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30']) }}>
    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center text-lg mx-auto mb-3">
        <i class="{{ $icon }}"></i>
    </div>
    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $title }}</h3>
    @if($description)
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">{{ $description }}</p>
    @endif
    @if($slot->isNotEmpty())
        <div class="mt-4 flex justify-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
