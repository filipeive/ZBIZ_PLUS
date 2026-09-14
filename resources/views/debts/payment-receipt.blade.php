@if(request('format') === 'thermal')
    @include('debts.payment-receipt-thermal')
@else
    @include('debts.payment-receipt-a4')
@endif
