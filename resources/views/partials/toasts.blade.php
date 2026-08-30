@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Processar a mensagem de toast única do controller (padrão novo)
        @if (session('toast_message'))
            FDSMULTSERVICES.Toast.show(
                "{{ session('toast_message') }}",
                "{{ session('toast_type', 'info') }}"
            );
        @endif

        // Processar mensagens antigas (fallback para compatibilidade)
        @if (session('success'))
            FDSMULTSERVICES.Toast.show("{{ session('success') }}", 'success');
        @endif

        @if (session('error'))
            FDSMULTSERVICES.Toast.show("{{ session('error') }}", 'error');
        @endif

        @if (session('warning'))
            FDSMULTSERVICES.Toast.show("{{ session('warning') }}", 'warning');
        @endif

        @if (session('info'))
            FDSMULTSERVICES.Toast.show("{{ session('info') }}", 'info');
        @endif

        // Ocultar alerts tradicionais Bootstrap
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.display = 'none';
        });
    });
</script>
@endpush