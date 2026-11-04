{{--
  Componente: x-ui.label
  Props:
    - value: string (opcional se passado como slot)
    - required: bool (mostra asterisco vermelho se verdadeiro)
--}}
@props([
    'value' => null,
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-rose-500 font-bold ms-0.5">*</span>
    @endif
</label>
