<table>
    <thead>
        <tr>
            <th colspan="5">Relatório de {{ ucfirst($reportType) }}</th>
        </tr>
        <tr>
            <th>Período</th>
            <th>Total Vendas</th>
            <th>Total Receita</th>
            <th>Total Despesas</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $dateFrom }} a {{ $dateTo }}</td>
            <td>{{ $totalSales }}</td>
            <td>{{ number_format($totalRevenue, 2, ',', '.') }}</td>
            <td>{{ number_format($totalExpenses, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

@if($sales->count())
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Cliente</th>
                <th>Valor</th>
                <th>Produtos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->sale_date }}</td>
                    <td>{{ $sale->user->name ?? '-' }}</td>
                    <td>{{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                    <td>
                        @foreach($sale->items as $item)
                            {{ $item->product->name }} ({{ $item->quantity }})<br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if($expenses->count())
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Usuário</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ number_format($expense->amount, 2, ',', '.') }}</td>
                    <td>{{ $expense->user->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if($products->count())
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Preço Venda</th>
                <th>Estoque</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ number_format($product->selling_price, 2, ',', '.') }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                    <td>{{ $product->is_active ? 'Ativo' : 'Inativo' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif