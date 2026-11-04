{{--
  Componente: x-ui.field-error
  Props:
    - messages: string|array (mensagens de erro do validador)
--}}
@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-xs text-rose-500 font-medium space-y-1 mt-1.5']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1.5">
                <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
