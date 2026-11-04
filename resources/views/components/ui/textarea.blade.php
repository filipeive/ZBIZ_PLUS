{{--
  Componente: x-ui.textarea
  Props:
    - rows: int (padrão 3)
    - error: string|bool
--}}
@props([
    'rows' => 3,
    'error' => false,
])

@php
    $baseClasses = 'w-full px-3.5 py-2.5 rounded-xl text-xs transition duration-150 outline-none border focus:ring-2 disabled:opacity-60 disabled:cursor-not-allowed resize-y';

    if ($error) {
        $stateClasses = 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200 focus:border-rose-500 focus:ring-rose-500/20';
    } else {
        $stateClasses = 'bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:ring-emerald-500/20';
    }
@endphp

<textarea rows="{{ $rows }}" {{ $attributes->merge(['class' => "$baseClasses $stateClasses"]) }}>{{ $slot }}</textarea>
