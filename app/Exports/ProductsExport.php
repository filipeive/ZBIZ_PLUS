<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Product::with('category');

        // Aplicar filtros
        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        if (isset($this->filters['status']) && $this->filters['status'] !== '') {
            $query->where('is_active', $this->filters['status']);
        }

        if (!empty($this->filters['stock_status'])) {
            switch ($this->filters['stock_status']) {
                case 'low':
                    $query->where('type', 'product')->whereRaw('stock_quantity <= min_stock_level');
                    break;
                case 'normal':
                    $query->where('type', 'product')->whereRaw('stock_quantity > min_stock_level AND stock_quantity <= min_stock_level * 3');
                    break;
                case 'high':
                    $query->where('type', 'product')->whereRaw('stock_quantity > min_stock_level * 3');
                    break;
            }
        }

        // Filtros de período
        if (!empty($this->filters['period'])) {
            $now = Carbon::now();
            switch ($this->filters['period']) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                    break;
                case 'quarter':
                    $quarter = ceil($now->month / 3);
                    $startMonth = (($quarter - 1) * 3) + 1;
                    $endMonth = $quarter * 3;
                    $query->whereBetween('created_at', [
                        Carbon::create($now->year, $startMonth, 1)->startOfMonth(),
                        Carbon::create($now->year, $endMonth, 1)->endOfMonth()
                    ]);
                    break;
                case 'year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        // Ordenação
        $sortBy = $this->filters['sort_by'] ?? 'name';
        $sortOrder = $this->filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nome do Produto/Serviço',
            'Categoria',
            'Tipo',
            'Descrição',
            'Preço de Venda (MT)',
            'Preço de Compra (MT)',
            'Unidade',
            'Estoque Atual',
            'Estoque Mínimo',
            'Valor Total Estoque (MT)',
            'Status Estoque',
            'Status Produto',
            'Data de Criação',
            'Última Atualização'
        ];
    }

    /**
     * @param $product
     * @return array
     */
    public function map($product): array
    {
        $stockStatus = 'N/A';
        $totalValue = 0;

        if ($product->type === 'product') {
            $totalValue = $product->selling_price * $product->stock_quantity;

            if ($product->stock_quantity <= $product->min_stock_level) {
                $stockStatus = 'Baixo';
            } elseif ($product->stock_quantity <= $product->min_stock_level * 3) {
                $stockStatus = 'Normal';
            } else {
                $stockStatus = 'Alto';
            }
        }

        return [
            $product->id,
            $product->name,
            $product->category ? $product->category->name : 'Sem categoria',
            $product->type === 'product' ? 'Produto' : 'Serviço',
            $product->description ?: '',
            number_format($product->selling_price, 2, ',', '.'),
            $product->purchase_price ? number_format($product->purchase_price, 2, ',', '.') : '',
            $product->unit ?: '',
            $product->type === 'product' ? $product->stock_quantity : 'N/A',
            $product->type === 'product' ? $product->min_stock_level : 'N/A',
            $product->type === 'product' ? number_format($totalValue, 2, ',', '.') : 'N/A',
            $stockStatus,
            $product->is_active ? 'Ativo' : 'Inativo',
            $product->created_at->format('d/m/Y H:i'),
            $product->updated_at->format('d/m/Y H:i')
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Data rows
            'A2:O1000' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Numeric columns alignment
            'F:G' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            'I:K' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            // Center alignment for status columns
            'L:M' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 30,  // Nome
            'C' => 20,  // Categoria
            'D' => 12,  // Tipo
            'E' => 40,  // Descrição
            'F' => 18,  // Preço Venda
            'G' => 18,  // Preço Compra
            'H' => 12,  // Unidade
            'I' => 15,  // Estoque Atual
            'J' => 15,  // Estoque Mínimo
            'K' => 20,  // Valor Total
            'L' => 15,  // Status Estoque
            'M' => 15,  // Status Produto
            'N' => 18,  // Data Criação
            'O' => 18,  // Data Atualização
        ];
    }
}