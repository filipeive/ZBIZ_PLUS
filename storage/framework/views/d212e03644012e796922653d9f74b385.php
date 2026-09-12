<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante de Venda #<?php echo e($sale->id); ?></title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.2;
            color: #000;
            background: #fff;
            width: 80mm;
            max-width: 302px; /* 80mm = 302px */
            margin: 0 auto;
            padding: 2mm;
        }

        /* Para impressoras de 55mm */
        @media (max-width: 210px) {
            body {
                width: 55mm;
                max-width: 210px;
                font-size: 10px;
            }
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 3mm;
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 1mm;
            text-transform: uppercase;
        }

        .header p {
            font-size: 11px;
            margin: 0.5mm 0;
        }

        /* Informações do cliente */
        .customer-info {
            margin-bottom: 3mm;
            font-size: 10px;
        }

        .customer-info p {
            margin: 0.5mm 0;
            word-wrap: break-word;
        }

        /* Separador */
        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }

        /* Tabela de itens */
        .items-table {
            width: 100%;
            margin-bottom: 3mm;
            font-size: 10px;
        }

        .items-header {
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
            margin-bottom: 2mm;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .item-row {
            display: block;
            margin-bottom: 2mm;
            padding-bottom: 1mm;
            border-bottom: 1px dotted #ccc;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: bold;
            font-size: 11px;
            word-wrap: break-word;
            margin-bottom: 1mm;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .item-qty-price {
            text-align: right;
        }

        /* Totais */
        .totals {
            border-top: 1px solid #000;
            padding-top: 2mm;
            margin-top: 2mm;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 1mm 0;
            font-size: 11px;
        }

        .total-row.grand-total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 1mm;
            margin-top: 2mm;
        }

        /* Desconto */
        .discount-section {
            background: #f5f5f5;
            padding: 2mm;
            margin: 2mm 0;
            border: 1px dashed #000;
        }

        .discount-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin: 0.5mm 0;
        }

        /* Observações */
        .notes {
            margin-top: 3mm;
            font-size: 10px;
            word-wrap: break-word;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 5mm;
            border-top: 1px dashed #000;
            padding-top: 2mm;
            font-size: 10px;
        }

        /* Botões (não aparecem na impressão) */
        .no-print {
            text-align: center;
            margin-top: 5mm;
        }

        .btn {
            padding: 5px 15px;
            margin: 0 5px;
            border: 1px solid #ccc;
            background: #f8f8f8;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border-color: #6c757d;
        }

        /* Estilos de impressão */
        @media print {
            body {
                width: auto;
                max-width: none;
                margin: 0;
                padding: 0;
                font-size: 12px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .header h2 {
                font-size: 16px;
            }
            
            .item-name {
                font-size: 12px;
            }
            
            .total-row.grand-total {
                font-size: 14px;
            }
            
            /* Quebra de página controlada */
            .page-break {
                page-break-before: always;
            }
            
            /* Remove cores de fundo na impressão */
            .discount-section {
                background: none !important;
                border: 1px solid #000;
            }
        }

        /* Para impressoras muito pequenas */
        @media (max-width: 180px) {
            body {
                font-size: 9px;
            }
            
            .header h2 {
                font-size: 12px;
            }
            
            .item-name {
                font-size: 10px;
            }
        }

        /* Utilitários */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 2mm; }
        .mt-2 { margin-top: 2mm; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2><?php echo e(config('app.name', 'LOJA')); ?></h2>
        <p><strong>COMPROVANTE DE VENDA</strong></p>
        <p><strong>#<?php echo e($sale->id); ?></strong></p>
        <p><?php echo e($sale->sale_date->format('d/m/Y H:i')); ?></p>
    </div>

    <!-- Informações do Cliente -->
    <div class="customer-info">
        <p><strong>CLIENTE:</strong> <?php echo e($sale->customer_name ?? 'CONSUMIDOR FINAL'); ?></p>
        <?php if($sale->customer_phone): ?>
            <p><strong>TELEFONE:</strong> <?php echo e($sale->customer_phone); ?></p>
        <?php endif; ?>
        <p><strong>PAGAMENTO:</strong> <?php echo e(strtoupper($sale->payment_method)); ?></p>
        <p><strong>VENDEDOR:</strong> <?php echo e($sale->user->name ?? 'SISTEMA'); ?></p>
    </div>

    <hr class="divider">

    <!-- Itens -->
    <div class="items-table">
        <div class="items-header">
            <span>ITEM</span>
            <span>QTD x VALOR</span>
        </div>

        <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="item-row">
                <div class="item-name"><?php echo e($item->product->name ?? 'PRODUTO REMOVIDO'); ?></div>
                <div class="item-details">
                    <span>
                        <?php if($item->discount_amount > 0): ?>
                            <small style="text-decoration: line-through;">
                                <?php echo e(number_format($item->original_unit_price ?? $item->unit_price, 2, ',', '.')); ?>

                            </small>
                            <?php echo e(number_format($item->unit_price, 2, ',', '.')); ?> MT
                        <?php else: ?>
                            <?php echo e(number_format($item->unit_price, 2, ',', '.')); ?> MT
                        <?php endif; ?>
                    </span>
                    <span class="item-qty-price">
                        <?php echo e($item->quantity); ?>x = <?php echo e(number_format($item->total_price, 2, ',', '.')); ?> MT
                    </span>
                </div>
                <?php if($item->discount_amount > 0): ?>
                    <div class="item-details">
                        <span style="font-size: 9px; color: #666;">
                            DESCONTO: <?php echo e(number_format($item->discount_percentage ?? 0, 1)); ?>%
                        </span>
                        <span style="font-size: 9px; color: #666;">
                            -<?php echo e(number_format($item->discount_amount, 2, ',', '.')); ?> MT
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Totais -->
    <div class="totals">
        <?php if($sale->discount_amount > 0): ?>
            <div class="discount-section">
                <div class="total-row">
                    <span>SUBTOTAL:</span>
                    <span><?php echo e(number_format($sale->subtotal, 2, ',', '.')); ?> MT</span>
                </div>
                <div class="total-row" style="color: #d63384;">
                    <span>DESCONTO (<?php echo e(number_format($sale->getTotalDiscountPercentage(), 1)); ?>%):</span>
                    <span>-<?php echo e(number_format($sale->discount_amount, 2, ',', '.')); ?> MT</span>
                </div>
                <?php if($sale->discount_reason): ?>
                    <div style="font-size: 9px; margin-top: 1mm;">
                        <strong>MOTIVO:</strong> <?php echo e($sale->discount_reason); ?>

                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="total-row grand-total">
            <span>TOTAL A PAGAR:</span>
            <span><?php echo e(number_format($sale->total_amount, 2, ',', '.')); ?> MT</span>
        </div>
    </div>

    <?php if($sale->notes): ?>
        <hr class="divider">
        <div class="notes">
            <p><strong>OBSERVAÇÕES:</strong></p>
            <p><?php echo e($sale->notes); ?></p>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p>OBRIGADO PELA PREFERÊNCIA!</p>
        <p style="font-size: 9px; margin-top: 2mm;">
            <?php echo e(now()->format('d/m/Y H:i')); ?>

        </p>
        <?php if($sale->discount_amount > 0): ?>
            <p style="font-size: 9px; margin-top: 1mm;">
                VOCÊ ECONOMIZOU: <?php echo e(number_format($sale->discount_amount, 2, ',', '.')); ?> MT
            </p>
        <?php endif; ?>
    </div>

    <!-- Botões de controle (ocultos na impressão) -->
    <?php if(!request()->has('print')): ?>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-primary">IMPRIMIR</button>
            <button onclick="window.close()" class="btn btn-secondary">FECHAR</button>
        </div>
    <?php endif; ?>

    <script>
        // Auto-print quando carrega
        window.onload = function() {
            // Pequeno delay para garantir que a página carregou completamente
            setTimeout(function() {
                window.print();
            }, 500);
        }

        // Fechar após impressão ou cancelamento
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 1000);
        }

        // Fallback: fechar automaticamente após 10 segundos se nada acontecer
        setTimeout(function() {
            if (!window.closed) {
                window.close();
            }
        }, 10000);

        // Detectar ESC para fechar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>
</html><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/sales/print.blade.php ENDPATH**/ ?>