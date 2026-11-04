{{--
  Componente: x-ui.table
  Props:
    - wrapperClass: string adicional para o container responsivo
--}}
@props([
    'wrapperClass' => '',
])

<div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 {{ $wrapperClass }}">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse text-xs']) }}>
        {{ $slot }}
    </table>
</div>
