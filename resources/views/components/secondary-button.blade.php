<x-ui.button variant="secondary" :type="$attributes->get('type', 'button')" {{ $attributes }}>
    {{ $slot }}
</x-ui.button>
