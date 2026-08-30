<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class ReportExport implements WithMultipleSheets
{
    protected $sales;
    protected $expenses;
    protected $products;
    protected $dateFrom;
    protected $dateTo;
    protected $reportType;
    protected $metrics;

    public function __construct($sales, $expenses, $products, $dateFrom, $dateTo, $reportType, $metrics)
    {
        $this->sales = $sales;
        $this->expenses = $expenses;
        $this->products = $products;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->reportType = $reportType;
        $this->metrics = $metrics;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Aba de Resumo Executivo
        $sheets[] = new ReportSummarySheet(
            $this->metrics, 
            $this->dateFrom, 
            $this->dateTo
        );

        // Abas condicionais baseadas no tipo de relatório
        if ($this->reportType === 'sales' || $this->reportType === 'all') {
            $sheets[] = new SalesSheet($this->sales);
        }

        if ($this->reportType === 'expenses' || $this->reportType === 'all') {
            $sheets[] = new ExpensesSheet($this->expenses);
        }

        if ($this->reportType === 'products' || $this->reportType === 'all') {
            $sheets[] = new ProductsSheet($this->products);
        }

        return $sheets;
    }
}

class ReportSummarySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $metrics;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($metrics, $dateFrom, $dateTo)
    {
        $this->metrics = $metrics;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        return collect([
            // Informações do Período
            ['RELATÓRIO GERENCIAL', ''],
            ['Período:', \Carbon\Carbon::parse($this->dateFrom)->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse($this->dateTo)->format('d/m/Y')],
            ['Data de Geração:', now()->format('d/m/Y H:i:s')],
            ['', ''],

            // Métricas Principais
            ['RESUMO EXECUTIVO', ''],
            ['Total de Vendas (Qtd)', $this->metrics['totalSales']],
            ['Receita Bruta (MT)', number_format($this->metrics['totalRevenue'], 2, ',', '.')],
            ['Custo dos Produtos Vendidos (MT)', number_format($this->metrics['costOfGoodsSold'], 2, ',', '.')],
            ['Lucro Bruto (MT)', number_format($this->metrics['grossProfit'], 2, ',', '.')],
            ['Despesas Operacionais (MT)', number_format($this->metrics['totalExpenses'], 2, ',', '.')],
            ['Lucro Líquido (MT)', number_format($this->metrics['netProfit'], 2, ',', '.')],
            ['', ''],

            // Indicadores de Performance
            ['INDICADORES DE PERFORMANCE', ''],
            ['Margem Bruta (%)', number_format($this->metrics['grossMargin'], 2, ',', '.') . '%'],
            ['Margem Líquida (%)', number_format($this->metrics['netMargin'], 2, ',', '.') . '%'],
            ['Ticket Médio (MT)', number_format($this->metrics['averageTicket'], 2, ',', '.')],
            ['Crescimento da Receita (%)', number_format($this->metrics['revenueGrowth'] ?? 0, 2, ',', '.') . '%'],
            ['', ''],

            // Análise de Eficiência
            ['ANÁLISE DE EFICIÊNCIA', ''],
            ['ROI - Return on Investment (%)', $this->metrics['costOfGoodsSold'] > 0 ? number_format(($this->metrics['netProfit'] / $this->metrics['costOfGoodsSold']) * 100, 2, ',', '.') . '%' : '0%'],
            ['Giro de Estoque (estimado)', $this->metrics['totalRevenue'] > 0 ? number_format($this->metrics['costOfGoodsSold'] / ($this->metrics['totalRevenue'] / 12), 1, ',', '.') : '0'],
            ['Eficiência Operacional (%)', $this->metrics['totalRevenue'] > 0 ? number_format((1 - ($this->metrics['totalExpenses'] / $this->metrics['totalRevenue'])) * 100, 1, ',', '.') . '%' : '0%'],
            ['', ''],

            // Status da Performance
            ['STATUS DA PERFORMANCE', ''],
            ['Margem Bruta', $this->getPerformanceStatus($this->metrics['grossMargin'], 30, 15)],
            ['Margem Líquida', $this->getPerformanceStatus($this->metrics['netMargin'], 15, 5)],
            ['Crescimento', $this->metrics['revenueGrowth'] >= 0 ? 'POSITIVO' : 'NEGATIVO'],
            ['Resultado Geral', $this->metrics['netProfit'] >= 0 ? 'LUCRO' : 'PREJUÍZO']
        ]);
    }

    private function getPerformanceStatus($value, $excellent, $good)
    {
        if ($value >= $excellent) return 'EXCELENTE';
        if ($value >= $good) return 'BOM';
        return 'ATENÇÃO NECESSÁRIA';
    }

    public function headings(): array
    {
        return ['Indicador', 'Valor'];
    }

    public function title(): string
    {
        return 'Resumo Executivo';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            5 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E3F2FD']]],
            13 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3E5F5']]],
            18 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E8F5E8']]],
            23 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFF3E0']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 25,
        ];
    }
}

class SalesSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return $this->sales->map(function ($sale) {
            return [
                'ID' => $sale->id,
                'Data' => \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y'),
                'Cliente' => $sale->customer_name ?? 'N/A',
                'Telefone' => $sale->customer_phone ?? 'N/A',
                'Qtd Itens' => $sale->items->count(),
                'Valor Total (MT)' => number_format($sale->total_amount, 2, ',', '.'),
                'Custo (MT)' => number_format($sale->cost ?? 0, 2, ',', '.'),
                'Lucro (MT)' => number_format($sale->profit ?? 0, 2, ',', '.'),
                'Margem (%)' => number_format($sale->margin ?? 0, 1, ',', '.') . '%',
                'Método Pagamento' => ucfirst($sale->payment_method),
                'Vendedor' => $sale->user->name ?? 'N/A'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID', 'Data', 'Cliente', 'Telefone', 'Qtd Itens', 
            'Valor Total (MT)', 'Custo (MT)', 'Lucro (MT)', 'Margem (%)', 
            'Método Pagamento', 'Vendedor'
        ];
    }

    public function title(): string
    {
        return 'Vendas Detalhadas';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E3F2FD']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // ID
            'B' => 12, // Data
            'C' => 20, // Cliente
            'D' => 15, // Telefone
            'E' => 10, // Qtd Itens
            'F' => 15, // Valor Total
            'G' => 12, // Custo
            'H' => 12, // Lucro
            'I' => 10, // Margem
            'J' => 15, // Pagamento
            'K' => 15, // Vendedor
        ];
    }
}

class ExpensesSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses->map(function ($expense) {
            return [
                'ID' => $expense->id,
                'Data' => \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y'),
                'Categoria' => $expense->category->name ?? 'Sem Categoria',
                'Descrição' => $expense->description,
                'Valor (MT)' => number_format($expense->amount, 2, ',', '.'),
                'Recibo' => $expense->receipt_number ?? 'N/A',
                'Usuário' => $expense->user->name ?? 'N/A',
                'Observações' => $expense->notes ?? ''
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID', 'Data', 'Categoria', 'Descrição', 'Valor (MT)', 
            'Recibo', 'Usuário', 'Observações'
        ];
    }

    public function title(): string
    {
        return 'Despesas';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFEBEE']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // ID
            'B' => 12, // Data
            'C' => 15, // Categoria
            'D' => 30, // Descrição
            'E' => 15, // Valor
            'F' => 12, // Recibo
            'G' => 15, // Usuário
            'H' => 25, // Observações
        ];
    }
}

class ProductsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products->map(function ($product) {
            $stockStatus = 'N/A';
            if ($product->type === 'product') {
                if ($product->stock_quantity <= 0) {
                    $stockStatus = 'ESGOTADO';
                } elseif ($product->stock_quantity <= $product->min_stock_level) {
                    $stockStatus = 'BAIXO';
                } else {
                    $stockStatus = 'OK';
                }
            }

            return [
                'Nome' => $product->name,
                'Categoria' => $product->category->name ?? 'N/A',
                'Tipo' => $product->type === 'product' ? 'Produto' : 'Serviço',
                'Estoque Atual' => $product->type === 'product' ? $product->stock_quantity : '-',
                'Estoque Mínimo' => $product->type === 'product' ? $product->min_stock_level : '-',
                'Status Estoque' => $stockStatus,
                'Preço Custo (MT)' => number_format($product->purchase_price ?? 0, 2, ',', '.'),
                'Preço Venda (MT)' => number_format($product->selling_price, 2, ',', '.'),
                'Markup (%)' => number_format($product->markup ?? 0, 1, ',', '.') . '%',
                'Qtd Vendida' => $product->quantity_sold ?? 0,
                'Receita Gerada (MT)' => number_format($product->revenue_generated ?? 0, 2, ',', '.'),
                'Performance' => $this->getProductPerformance($product)
            ];
        });
    }

    private function getProductPerformance($product)
    {
        $quantitySold = $product->quantity_sold ?? 0;
        $markup = $product->markup ?? 0;
        
        if ($quantitySold === 0) return 'SEM VENDAS';
        if ($quantitySold >= 50 && $markup >= 30) return 'EXCELENTE';
        if ($quantitySold >= 20 && $markup >= 15) return 'BOM';
        if ($quantitySold >= 10) return 'REGULAR';
        return 'BAIXO';
    }

    public function headings(): array
    {
        return [
            'Nome', 'Categoria', 'Tipo', 'Estoque Atual', 'Estoque Mínimo', 
            'Status Estoque', 'Preço Custo (MT)', 'Preço Venda (MT)', 
            'Markup (%)', 'Qtd Vendida', 'Receita Gerada (MT)', 'Performance'
        ];
    }

    public function title(): string
    {
        return 'Produtos e Estoque';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E8F5E8']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // Nome
            'B' => 15, // Categoria
            'C' => 10, // Tipo
            'D' => 12, // Estoque Atual
            'E' => 12, // Estoque Mínimo
            'F' => 15, // Status Estoque
            'G' => 15, // Preço Custo
            'H' => 15, // Preço Venda
            'I' => 10, // Markup
            'J' => 12, // Qtd Vendida
            'K' => 18, // Receita Gerada
            'L' => 15, // Performance
        ];
    }
}