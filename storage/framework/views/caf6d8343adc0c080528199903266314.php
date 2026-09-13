<?php $__env->startSection('title', 'Detalhes da Venda #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT)); ?>
<?php $__env->startSection('page-title', 'Histórico & Detalhes da Venda #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT)); ?>

<?php
    $theme = tenant_theme();
    $paymentMethodLabels = [
        'cash'     => ['label' => 'Dinheiro', 'icon' => 'fa-money-bill-wave', 'color' => 'emerald'],
        'mpesa'    => ['label' => 'M-Pesa', 'icon' => 'fa-mobile-screen', 'color' => 'rose'],
        'emola'    => ['label' => 'e-Mola', 'icon' => 'fa-mobile-retro', 'color' => 'amber'],
        'card'     => ['label' => 'Cartão POS / Débito', 'icon' => 'fa-credit-card', 'color' => 'blue'],
        'transfer' => ['label' => 'Transferência Bancária', 'icon' => 'fa-building-columns', 'color' => 'indigo'],
        'credit'   => ['label' => 'Fiado / Dívida', 'icon' => 'fa-file-invoice-dollar', 'color' => 'amber'],
        'split'    => ['label' => 'Pagamento Misto', 'icon' => 'fa-coins', 'color' => 'purple'],
    ];
    $pm = $paymentMethodLabels[$sale->payment_method] ?? ['label' => ucfirst($sale->payment_method ?? 'Dinheiro'), 'icon' => 'fa-money-bill-wave', 'color' => 'slate'];
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-full mx-auto space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('sales.index')); ?>" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Histórico
            </a>
            <?php if($sale->branch): ?>
                <span class="px-3 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700 text-slate-300 font-bold text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-store text-emerald-400"></i> <?php echo e($sale->branch->name); ?>

                </span>
            <?php endif; ?>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <?php if($sale->debt): ?>
                <a href="<?php echo e(route('debts.show', $sale->debt->id)); ?>" class="px-4 py-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 hover:bg-amber-500/20 font-bold text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Ver Fiado / Dívida
                </a>
            <?php endif; ?>

            <a href="<?php echo e(route('pos.receipt', $sale->id)); ?>" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i> Recibo Térmico (80mm)
            </a>

            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl <?php echo e($theme['btn']); ?> text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir A4 / PDF
            </button>
        </div>
    </div>

    <!-- Main Sale Dossier Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        
        <!-- Header Banner: Invoice Meta & Grand Total -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-800">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo e($theme['badge']); ?> inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-400"></i> Venda Finalizada
                    </span>
                    <?php if($sale->discount_amount > 0): ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/10 border border-amber-500/30 text-amber-300">
                            <i class="fa-solid fa-tags"></i> Desconto Aplicado
                        </span>
                    <?php endif; ?>
                </div>
                <h2 class="text-3xl font-black font-heading text-white tracking-tight">
                    Fatura / Venda #<?php echo e(str_pad($sale->id, 5, '0', STR_PAD_LEFT)); ?>

                </h2>
                <div class="text-xs text-slate-400 flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span><i class="fa-regular fa-calendar me-1"></i> <?php echo e($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') : ($sale->created_at ? $sale->created_at->format('d/m/Y') : '-')); ?></span>
                    <span><i class="fa-regular fa-clock me-1"></i> <?php echo e($sale->created_at ? $sale->created_at->format('H:i:s') : '-'); ?></span>
                    <?php if($sale->branch): ?>
                        <span><i class="fa-solid fa-location-dot me-1 text-slate-500"></i> <?php echo e($sale->branch->name); ?> (<?php echo e($sale->branch->code ?? 'Matriz'); ?>)</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-left md:text-right min-w-[240px]">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Líquido da Operação</div>
                <div class="text-3xl font-black font-heading text-emerald-400 font-mono mt-0.5">
                    <?php echo e(number_format($sale->total_amount, 2, ',', '.')); ?> <span class="text-sm font-sans text-slate-400">MT</span>
                </div>
                <?php if($sale->discount_amount > 0): ?>
                    <div class="text-[11px] text-slate-400 mt-1">
                        Subtotal: <span class="font-mono text-slate-300"><?php echo e(number_format($sale->subtotal, 2, ',', '.')); ?> MT</span> | 
                        Desconto: <span class="font-mono text-amber-400">-<?php echo e(number_format($sale->discount_amount, 2, ',', '.')); ?> MT</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 3-Column Entities Context Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Cliente & Faturamento -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-2">
                <div class="text-slate-500 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-user-tag text-indigo-400"></i> Cliente / Entidade
                </div>
                <div class="font-bold text-white text-sm truncate">
                    <?php echo e($sale->customer_name ?? $sale->customer?->name ?? 'Consumidor Final'); ?>

                </div>
                <div class="text-xs text-slate-400 space-y-0.5">
                    <?php if($sale->customer_phone || $sale->customer?->phone): ?>
                        <div><i class="fa-solid fa-phone text-[10px] me-1.5 text-slate-500"></i><?php echo e($sale->customer_phone ?? $sale->customer?->phone); ?></div>
                    <?php endif; ?>
                    <?php if($sale->customer?->nuit): ?>
                        <div><i class="fa-solid fa-id-card text-[10px] me-1.5 text-slate-500"></i>NUIT: <span class="font-mono text-slate-300"><?php echo e($sale->customer->nuit); ?></span></div>
                    <?php endif; ?>
                    <?php if($sale->customer?->address): ?>
                        <div class="truncate"><i class="fa-solid fa-map-pin text-[10px] me-1.5 text-slate-500"></i><?php echo e($sale->customer->address); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pagamento & Caixa -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-2">
                <div class="text-slate-500 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid <?php echo e($pm['icon']); ?> text-emerald-400"></i> Pagamento & Liquidação
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-<?php echo e($pm['color']); ?>-500/10 border border-<?php echo e($pm['color']); ?>-500/30 text-<?php echo e($pm['color']); ?>-400 inline-flex items-center gap-1.5">
                        <i class="fa-solid <?php echo e($pm['icon']); ?>"></i> <?php echo e($pm['label']); ?>

                    </span>
                </div>
                <div class="text-xs text-slate-400 space-y-0.5 font-mono">
                    <?php if($sale->payment_method === 'credit'): ?>
                        <div>Entrada / Pago: <span class="text-emerald-400 font-bold"><?php echo e(number_format($sale->amount_paid, 2, ',', '.')); ?> MT</span></div>
                        <div>Saldo Fiado: <span class="text-amber-400 font-bold"><?php echo e(number_format(max(0, $sale->total_amount - $sale->amount_paid), 2, ',', '.')); ?> MT</span></div>
                    <?php else: ?>
                        <div>Recebido: <span class="text-white font-bold"><?php echo e(number_format($sale->amount_paid ?: $sale->total_amount, 2, ',', '.')); ?> MT</span></div>
                        <div>Troco Devolvido: <span class="text-slate-300"><?php echo e(number_format($sale->change_amount ?? 0, 2, ',', '.')); ?> MT</span></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Operador & Filial -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-2">
                <div class="text-slate-500 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-cash-register text-amber-400"></i> Operador & Filial
                </div>
                <div class="font-bold text-white text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-user text-slate-400"></i> <?php echo e($sale->user?->name ?? 'Operador Caixa'); ?>

                </div>
                <div class="text-xs text-slate-400 space-y-0.5">
                    <div>Filial: <span class="text-slate-300 font-medium"><?php echo e($sale->branch?->name ?? 'Loja Principal'); ?></span></div>
                    <?php if($sale->branch?->phone): ?>
                        <div>Contacto Filial: <span class="text-slate-400"><?php echo e($sale->branch->phone); ?></span></div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Line Items Table -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-indigo-400"></i> Artigos Faturados (<?php echo e($sale->items->count()); ?>)
                </h3>
            </div>
            
            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="p-3.5">#</th>
                            <th class="p-3.5">Produto / Descrição</th>
                            <th class="p-3.5 text-center">Lote / Validade</th>
                            <th class="p-3.5 text-center">Quantidade</th>
                            <th class="p-3.5 text-right">Preço Unitário</th>
                            <th class="p-3.5 text-right">Desconto</th>
                            <th class="p-3.5 text-right">Subtotal Líquido</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                        <?php $__empty_1 = true; $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="p-3.5 text-slate-500 font-mono"><?php echo e($index + 1); ?></td>
                                <td class="p-3.5">
                                    <div class="font-bold text-white text-sm">
                                        <?php echo e($item->product_name ?? $item->product?->name ?? 'Produto #'.$item->product_id); ?>

                                    </div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                        <?php if($item->product?->sku): ?>
                                            <span>SKU: <span class="font-mono text-slate-300"><?php echo e($item->product->sku); ?></span></span>
                                        <?php endif; ?>
                                        <?php if($item->product?->barcode): ?>
                                            <span>Barras: <span class="font-mono text-slate-300"><?php echo e($item->product->barcode); ?></span></span>
                                        <?php endif; ?>
                                        <?php if($item->product?->category): ?>
                                            <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[10px]"><?php echo e($item->product->category->name); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-3.5 text-center">
                                    <?php if($item->batch_number || $item->product?->batches?->first()): ?>
                                        <?php
                                            $batch = $item->product?->batches?->first();
                                            $batchNum = $item->batch_number ?? $batch?->batch_number;
                                            $expDate = $item->expiry_date ?? $batch?->expiry_date;
                                        ?>
                                        <span class="px-2 py-0.5 rounded-md bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 font-mono text-[10px]">
                                            Lote: <?php echo e($batchNum ?? 'N/A'); ?>

                                        </span>
                                        <?php if($expDate): ?>
                                            <div class="text-[10px] text-slate-400 mt-0.5">Val: <?php echo e(\Carbon\Carbon::parse($expDate)->format('d/m/Y')); ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-slate-600 text-[11px]">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3.5 text-center font-mono font-bold text-white text-sm">
                                    <?php echo e($item->quantity); ?> <span class="text-[10px] font-sans text-slate-400"><?php echo e($item->product?->unit ?? 'un'); ?></span>
                                </td>
                                <td class="p-3.5 text-right font-mono text-slate-300">
                                    <?php echo e(number_format($item->unit_price, 2, ',', '.')); ?> MT
                                </td>
                                <td class="p-3.5 text-right font-mono text-amber-400">
                                    <?php if(($item->discount_amount ?? 0) > 0): ?>
                                        -<?php echo e(number_format($item->discount_amount, 2, ',', '.')); ?> MT
                                    <?php else: ?>
                                        <span class="text-slate-600">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3.5 text-right font-black text-emerald-400 font-mono text-sm">
                                    <?php echo e(number_format($item->total_price, 2, ',', '.')); ?> MT
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-500">Nenhum item registrado para esta venda.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-slate-950 border-t border-slate-800 text-xs">
                        <tr>
                            <td colspan="5" class="p-3 text-right font-bold text-slate-400 uppercase text-[10px]">Subtotal Bruto:</td>
                            <td colspan="2" class="p-3 text-right font-mono font-bold text-slate-300"><?php echo e(number_format($sale->subtotal, 2, ',', '.')); ?> MT</td>
                        </tr>
                        <?php if($sale->discount_amount > 0): ?>
                            <tr>
                                <td colspan="5" class="p-3 text-right font-bold text-amber-400 uppercase text-[10px]">Desconto Total:</td>
                                <td colspan="2" class="p-3 text-right font-mono font-bold text-amber-400">-<?php echo e(number_format($sale->discount_amount, 2, ',', '.')); ?> MT</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="border-t border-slate-800/80 bg-slate-950/90 font-bold">
                            <td colspan="5" class="p-3.5 text-right font-black text-white uppercase text-xs">Total Final Faturado:</td>
                            <td colspan="2" class="p-3.5 text-right font-mono font-black text-emerald-400 text-base">
                                <?php echo e(number_format($sale->total_amount, 2, ',', '.')); ?> MT
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Observações / Notas -->
        <?php if($sale->notes): ?>
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-1">
                <div class="text-slate-500 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-note-sticky text-amber-400"></i> Observações da Operação
                </div>
                <p class="text-xs text-slate-300 leading-relaxed"><?php echo e($sale->notes); ?></p>
            </div>
        <?php endif; ?>

        <!-- Auditoria Operacional & Movimentações de Stock -->
        <?php if(isset($stockMovements) && $stockMovements->count() > 0): ?>
            <div class="space-y-3 pt-4 border-t border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-dolly text-emerald-400"></i> Rastreabilidade & Baixas de Stock (<?php echo e($stockMovements->count()); ?>)
                </h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                <th class="p-3">ID Mov.</th>
                                <th class="p-3">Artigo</th>
                                <th class="p-3 text-center">Tipo</th>
                                <th class="p-3 text-center">Qtd. Baixada</th>
                                <th class="p-3">Armazém / Filial</th>
                                <th class="p-3">Data / Hora</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                            <?php $__currentLoopData = $stockMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="p-3 font-mono text-slate-500">#<?php echo e($sm->id); ?></td>
                                    <td class="p-3 font-bold text-white"><?php echo e($sm->product?->name); ?></td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 border border-rose-500/30 text-rose-400">
                                            Saída (Venda)
                                        </span>
                                    </td>
                                    <td class="p-3 text-center font-mono font-bold text-rose-400">-<?php echo e($sm->quantity); ?></td>
                                    <td class="p-3 text-slate-300"><?php echo e($sm->branch?->name ?? $sale->branch?->name ?? 'Loja Principal'); ?></td>
                                    <td class="p-3 text-slate-400 font-mono text-[11px]"><?php echo e($sm->created_at ? $sm->created_at->format('d/m/Y H:i') : '-'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/sales/show.blade.php ENDPATH**/ ?>