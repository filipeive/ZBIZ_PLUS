{{--
  Componente: x-ui.money
  Props:
    - amount: numeric (valor a formatar no padrão moçambicano)
    - currency: string (padrão 'MT')
    - decimals: int (padrão 2)
--}}
@props([
    'amount' => 0,
    'currency' => 'MT',
    'decimals' => 2,
])

@php
    $formatted = number_format((float) $amount, $decimals, ',', '.');
@endphp

<span {{ $attributes->merge(['class' => 'tabular-nums font-semibold']) }}>
    {{ $formatted }}
    @if($currency)
        <span class="text-[11px] font-normal text-slate-400 ms-0.5">{{ $currency }}</span>
    @endif
</span>
