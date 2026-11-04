@props(['value'])
<x-ui.label :value="$value ?? null" {{ $attributes }}>
    {{ $slot }}
</x-ui.label>
