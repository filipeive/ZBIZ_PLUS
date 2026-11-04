<x-ui.button variant="danger" :type="$attributes->get('type', 'submit')" {{ $attributes }}>
    {{ $slot }}
</x-ui.button>
