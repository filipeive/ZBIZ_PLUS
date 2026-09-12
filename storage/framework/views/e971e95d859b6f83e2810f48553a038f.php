<?php $__env->startSection('title', 'Editar Pedido #' . $order->id); ?>
<?php $__env->startSection('page-title', 'Editar Pedido #' . $order->id); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                Editar Encomenda #<?php echo e(str_pad($order->id, 5, '0', STR_PAD_LEFT)); ?>

            </h2>
            <p class="text-xs text-slate-400">Atualize os itens da ordem de serviço, cliente e condições financeiras.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-eye"></i> Ver Pedido
            </a>
            <a href="<?php echo e(route('orders.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <form id="order-form" action="<?php echo e(route('orders.update', $order->id)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <input type="hidden" name="estimated_amount" id="estimated_amount" value="<?php echo e($order->estimated_amount); ?>">
        <input type="hidden" name="items" id="items-json" value="">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">

                <!-- Itens da Encomenda -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-4 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xs">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">Itens da Encomenda</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-4">
                        <div class="md:col-span-9">
                            <select class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500" id="product-select">
                                <option value="">Selecione produto ou serviço a incluir...</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($product->id); ?>" data-name="<?php echo e($product->name); ?>" data-price="<?php echo e($product->selling_price); ?>">
                                        <?php echo e($product->name); ?> (MT <?php echo e(number_format($product->selling_price, 2, ',', '.')); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <button type="button" class="w-full py-2.5 rounded-xl <?php echo e($theme['btn']); ?> text-xs flex items-center justify-center gap-2" onclick="addItemToCart()">
                                <i class="fa-solid fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-800">
                        <table class="w-full text-left text-xs" id="cart-table">
                            <thead>
                                <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                    <th class="p-3">Item / Artigo</th>
                                    <th class="p-3 w-28 text-center">Quantidade</th>
                                    <th class="p-3 text-right w-32">Preço Unit.</th>
                                    <th class="p-3 text-right w-32">Subtotal</th>
                                    <th class="p-3 text-center w-16">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items" class="divide-y divide-slate-800/60 bg-slate-900/60">
                                <!-- Preenchido via JS -->
                            </tbody>
                        </table>
                    </div>
                    <?php $__errorArgs = ['items'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-rose-400 text-xs mt-2"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Detalhes & Descrição -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading border-b border-slate-800 pb-3">Especificações do Pedido</h3>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Descrição / Ordem de Serviço *</label>
                        <textarea name="description" rows="3" required class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none"><?php echo e(old('description', $order->description)); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Notas Internas</label>
                        <textarea name="notes" rows="2" class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none"><?php echo e(old('notes', $order->notes)); ?></textarea>
                    </div>
                </div>

            </div>

            <!-- Coluna Lateral: Cliente e Valores -->
            <div class="space-y-6">
                
                <!-- Dados do Cliente -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading border-b border-slate-800 pb-3">Identificação do Cliente</h3>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Nome do Cliente *</label>
                        <input type="text" name="customer_name" value="<?php echo e(old('customer_name', $order->customer_name)); ?>" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Telefone de Contacto</label>
                        <input type="text" name="customer_phone" value="<?php echo e(old('customer_phone', $order->customer_phone)); ?>" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Prazo de Entrega</label>
                        <input type="date" name="delivery_date" value="<?php echo e(old('delivery_date', $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '')); ?>" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Prioridade</label>
                        <select name="priority" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="low" <?php echo e(old('priority', $order->priority) === 'low' ? 'selected' : ''); ?>>Baixa</option>
                            <option value="medium" <?php echo e(old('priority', $order->priority) === 'medium' ? 'selected' : ''); ?>>Média</option>
                            <option value="high" <?php echo e(old('priority', $order->priority) === 'high' ? 'selected' : ''); ?>>Alta</option>
                            <option value="urgent" <?php echo e(old('priority', $order->priority) === 'urgent' ? 'selected' : ''); ?>>Urgente</option>
                        </select>
                    </div>
                </div>

                <!-- Totais e Pagamento -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading border-b border-slate-800 pb-3">Resumo Financeiro</h3>
                    
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400">Total Estimado:</span>
                        <span class="text-lg font-black font-mono text-white" id="display-total">MT 0,00</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Adiantamento Pago (MT)</label>
                        <input type="number" step="0.01" min="0" name="advance_payment" id="advance_payment" value="<?php echo e(old('advance_payment', $order->advance_payment)); ?>" oninput="updateTotals()" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-emerald-400 font-mono focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <div class="flex justify-between items-center text-xs pt-2 border-t border-slate-800">
                        <span class="text-slate-400">Saldo Pendente:</span>
                        <span class="text-base font-black font-mono text-amber-400" id="display-balance">MT 0,00</span>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-2xl <?php echo e($theme['btn']); ?> text-sm hover:scale-105 active:scale-95 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> Salvar Alterações
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    let cart = <?php echo json_encode($order->items->map(function($it) {
        return [
            'product_id' => $it->product_id, 'name' => $it->item_name ?? ($it->product?->name ?? 'Artigo'), 'quantity' => (float) $it->quantity) ?>;

    function renderCart() {
        const tbody = document.getElementById('cart-items');
        tbody.innerHTML = '';

        if (cart.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-slate-500">Nenhum item adicionado à encomenda.</td></tr>`;
            updateTotals();
            return;
        }

        cart.forEach((item, index) => {
            const subtotal = item.quantity * item.price;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="p-3 font-bold text-white">${item.name}</td>
                <td class="p-3 text-center">
                    <input type="number" min="1" step="1" value="${item.quantity}" onchange="updateQty(${index}, this.value)" class="w-20 px-2 py-1 bg-slate-950 border border-slate-800 rounded-lg text-center text-white text-xs">
                </td>
                <td class="p-3 text-right font-mono text-slate-300">${item.price.toFixed(2)} MT</td>
                <td class="p-3 text-right font-mono font-bold text-white">${subtotal.toFixed(2)} MT</td>
                <td class="p-3 text-center">
                    <button type="button" onclick="removeItem(${index})" class="w-6 h-6 rounded-md bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        updateTotals();
    }

    function addItemToCart() {
        const select = document.getElementById('product-select');
        const selectedOpt = select.options[select.selectedIndex];
        if (!select.value) return;

        const id = parseInt(select.value);
        const name = selectedOpt.getAttribute('data-name');
        const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0;

        const existing = cart.find(item => item.product_id === id);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({ product_id: id, name: name, quantity: 1, price: price });
        }

        select.value = '';
        renderCart();
    }

    function updateQty(index, val) {
        const qty = parseFloat(val);
        if (qty > 0) {
            cart[index].quantity = qty;
        }
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateTotals() {
        let total = 0;
        cart.forEach(item => {
            total += (item.quantity * item.price);
        });

        const advance = parseFloat(document.getElementById('advance_payment').value) || 0;
        const balance = Math.max(0, total - advance);

        document.getElementById('display-total').innerText = 'MT ' + total.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('display-balance').innerText = 'MT ' + balance.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('estimated_amount').value = total;

        const formattedItems = cart.map(item => ({
            product_id: item.product_id,
            item_name: item.name,
            quantity: item.quantity,
            unit_price: item.price
        }));

        document.getElementById('items-json').value = JSON.stringify(formattedItems);
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderCart();
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/orders/edit.blade.php ENDPATH**/ ?>