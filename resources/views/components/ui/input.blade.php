{{--
  Componente: x-ui.input
  Props:
    - type: 'text' (padrão), 'email', 'password', 'number', etc.
    - error: string|bool (se tem erro de validação)
--}}
@props([
    'type' => 'text',
    'error' => false,
])

@php
    $baseClasses = 'w-full px-3.5 py-2.5 rounded-xl text-xs transition duration-150 outline-none border focus:ring-2 disabled:bg-slate-100 dark:disabled:bg-slate-900 disabled:opacity-60 disabled:cursor-not-allowed';

    if ($error) {
        $stateClasses = 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200 focus:border-rose-500 focus:ring-rose-500/20 placeholder-rose-300';
    } else {
        $stateClasses = 'bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:ring-emerald-500/20';
    }
@endphp

<input type="{{ $type }}" {{ $attributes->merge(['class' => "$baseClasses $stateClasses"]) }}>
