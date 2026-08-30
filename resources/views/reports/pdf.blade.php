<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px;}
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left;}
        th { background: #f5f5f5; }
        h2 { margin-top: 0; }
    </style>
</head>
<body>
    <h2>Relatório de {{ ucfirst($reportType) }}</h2>
    <p>Período: {{ $dateFrom }} a {{ $dateTo }}</p>
    <p>Total de Vendas: {{ $totalSales }}</p>
    <p>Total Receita: {{ number_format($totalRevenue, 2, ',', '.') }}</p>
    <p>Total Despesas: {{ number_format($totalExpenses, 2, ',', '.') }}</p>

    @if($sales->count())
        <h3>Vendas</h3>
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
        <h3>Despesas</h3>
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
        <h3>Produtos</h3>
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
</body>
</html>