@extends('layouts.app')

@section('title', 'Rentabilidade por Cliente')
@section('page-title', 'Rentabilidade por Cliente')
@section('title-icon', 'fa-users')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Rentabilidade por Cliente</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>
                Análise de Clientes por Rentabilidade
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Telefone</th>
                            <th class="text-center">Vendas</th>
                            <th class="text-end">Receita Total</th>
                            <th class="text-end">Lucro Total</th>
                            <th class="text-center">Margem</th>
                            <th class="text-end">Ticket Médio</th>
                            <th>Última Compra</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerAnalysis as $customer)
                            <tr>
                                <td><strong>{{ $customer['customer_name'] }}</strong></td>
                                <td>{{ $customer['phone'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $customer['sales_count'] }}</td>
                                <td class="text-end text-success fw-bold">
                                    {{ number_format($customer['total_revenue'], 2, ',', '.') }} MT
                                </td>
                                <td class="text-end {{ $customer['total_profit'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ number_format($customer['total_profit'], 2, ',', '.') }} MT
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $customer['profit_margin'] >= 25 ? 'success' : ($customer['profit_margin'] >= 15 ? 'warning' : 'danger') }}">
                                        {{ number_format($customer['profit_margin'], 1) }}%
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($customer['average_ticket'], 2, ',', '.') }} MT</td>
                                <td>{{ \Carbon\Carbon::parse($customer['last_purchase'])->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    Nenhum cliente com vendas no período
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection