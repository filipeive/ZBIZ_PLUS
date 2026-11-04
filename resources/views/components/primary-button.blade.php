<x-ui.button variant="primary" :type="$attributes->get('type', 'submit')" {{ $attributes }}>
    {{ $slot }}
</x-ui.button>
