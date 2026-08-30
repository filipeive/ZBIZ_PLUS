/**
 * Gerenciador de Ajuste de Estoque
 * Arquivo: public/js/products/stock-manager.js
 */
class StockManager {
    constructor() {
        this.modal = null;
        this.form = null;
        this.isLoading = false;
        this.currentProduct = null;

        this.init();
    }

    init() {
        const modalElement = document.getElementById('stockModal');
        const formElement = document.getElementById('stock-form');
        
        if (!modalElement || !formElement) {
            console.warn('StockManager: Modal ou formulário não encontrado');
            return;
        }

        this.modal = new bootstrap.Modal(modalElement);
        this.form = formElement;
        this.setupEvents();
    }

    setupEvents() {
        // Botão salvar ajuste
        const btnSaveStock = document.getElementById('btn-save-stock');
        if (btnSaveStock) {
            btnSaveStock.addEventListener('click', () => {
                this.saveStockAdjustment();
            });
        }

        // Mudança no tipo de ajuste
        const adjustmentType = document.getElementById('adjustment-type');
        if (adjustmentType) {
            adjustmentType.addEventListener('change', (e) => {
                this.updateHints(e.target.value);
                this.updatePreview();
            });
        }

        // Mudança na quantidade
        const adjustmentQuantity = document.getElementById('adjustment-quantity');
        if (adjustmentQuantity) {
            adjustmentQuantity.addEventListener('input', () => {
                this.updatePreview();
            });
        }

        // Contador de caracteres
        const adjustmentReason = document.getElementById('adjustment-reason');
        if (adjustmentReason) {
            adjustmentReason.addEventListener('input', (e) => {
                const counter = document.getElementById('reason-counter');
                if (counter) {
                    const length = e.target.value.length;
                    counter.textContent = `${length}/200 caracteres`;

                    if (length > 180) {
                        counter.classList.add('text-warning');
                    } else {
                        counter.classList.remove('text-warning');
                    }
                }
            });
        }

        // Reset do modal
        const modalElement = document.getElementById('stockModal');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', () => {
                this.resetForm();
            });
        }

        // Configurar botões de ajuste de estoque
        this.setupStockButtons();
    }

    setupStockButtons() {
        document.querySelectorAll('.btn-stock').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const productId = e.currentTarget.dataset.id;
                if (productId) {
                    this.openStockModal(productId);
                }
            });
        });
    }

    async openStockModal(productId) {
        if (!productId) {
            this.showToast('ID do produto não encontrado', 'error');
            return;
        }

        try {
            this.setLoading(true);

            const response = await this.request(`/products/${productId}/edit-data`, 'GET');

            if (response.success) {
                this.currentProduct = response.data;
                this.populateProductInfo(this.currentProduct);
                this.modal.show();
            } else {
                this.showToast('Erro ao carregar dados do produto', 'error');
            }
        } catch (error) {
            console.error('Erro ao carregar produto:', error);
            this.showToast('Erro ao carregar produto', 'error');
        } finally {
            this.setLoading(false);
        }
    }

    populateProductInfo(product) {
        const stockProductId = document.getElementById('stock-product-id');
        const stockProductName = document.getElementById('stock-product-name');
        const stockProductCategory = document.getElementById('stock-product-category');
        const stockCurrent = document.getElementById('stock-current');

        if (stockProductId) stockProductId.value = product.id;
        if (stockProductName) stockProductName.textContent = product.name;
        if (stockProductCategory) {
            stockProductCategory.textContent = product.category?.name || 'Sem categoria';
        }
        if (stockCurrent) {
            stockCurrent.textContent = `${product.stock_quantity || 0} ${product.unit || 'unid'}`;
        }

        this.updatePreview();
    }

    updateHints(adjustmentType) {
        const hintElement = document.getElementById('stock-hint');
        if (!hintElement) return;

        if (adjustmentType === 'increase') {
            hintElement.innerHTML = '<i class="fas fa-arrow-up text-success me-1"></i>Quantidade a ser adicionada ao estoque';
            hintElement.className = 'text-success';
        } else if (adjustmentType === 'decrease') {
            hintElement.innerHTML = '<i class="fas fa-arrow-down text-danger me-1"></i>Quantidade a ser removida do estoque';
            hintElement.className = 'text-danger';
        } else {
            hintElement.innerHTML = '<i class="fas fa-info-circle me-1"></i>Digite a quantidade a ser ajustada';
            hintElement.className = '';
        }
    }

    updatePreview() {
        const previewElement = document.getElementById('stock-preview');
        const adjustmentTypeElement = document.getElementById('adjustment-type');
        const quantityElement = document.getElementById('adjustment-quantity');

        if (!previewElement || !adjustmentTypeElement || !quantityElement) return;

        const adjustmentType = adjustmentTypeElement.value;
        const quantity = parseInt(quantityElement.value) || 0;

        if (!this.currentProduct || !adjustmentType || quantity === 0) {
            previewElement.classList.add('d-none');
            return;
        }

        const currentStock = this.currentProduct.stock_quantity || 0;
        let newStock;
        let operationText;
        let changeText;

        if (adjustmentType === 'increase') {
            newStock = currentStock + quantity;
            operationText = 'Entrada (+):';
            changeText = `+${quantity}`;
            previewElement.className = 'alert stock-preview-positive';
        } else {
            newStock = currentStock - quantity;
            operationText = 'Saída (-):';
            changeText = `-${quantity}`;

            if (newStock < 0) {
                previewElement.className = 'alert stock-preview-negative';
            } else if (newStock <= (this.currentProduct.min_stock_level || 0)) {
                previewElement.className = 'alert stock-preview-warning';
            } else {
                previewElement.className = 'alert stock-preview-positive';
            }
        }

        const previewCurrent = document.getElementById('preview-current');
        const previewOperation = document.getElementById('preview-operation');
        const previewChange = document.getElementById('preview-change');
        const previewResult = document.getElementById('preview-result');

        if (previewCurrent) previewCurrent.textContent = currentStock;
        if (previewOperation) previewOperation.textContent = operationText;
        if (previewChange) previewChange.textContent = changeText;
        if (previewResult) previewResult.textContent = newStock;

        previewElement.classList.remove('d-none');
    }

    async saveStockAdjustment() {
        if (this.isLoading) return;

        this.clearErrors();

        const formData = new FormData(this.form);
        const productIdElement = document.getElementById('stock-product-id');
        const productId = productIdElement ? productIdElement.value : null;

        if (!productId) {
            this.showToast('ID do produto não encontrado', 'error');
            return;
        }

        try {
            this.setLoading(true);

            const response = await this.request(`/products/${productId}/adjust-stock`, 'POST', formData);

            if (response.success) {
                this.showToast(response.message || 'Estoque ajustado com sucesso!', 'success');
                this.modal.hide();

                // Recarregar página após sucesso
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                if (response.errors) {
                    this.displayErrors(response.errors);
                }
                this.showToast(response.message || 'Erro ao ajustar estoque', 'error');
            }
        } catch (error) {
            console.error('Erro ao ajustar estoque:', error);
            this.showToast('Erro ao ajustar estoque', 'error');
        } finally {
            this.setLoading(false);
        }
    }

    resetForm() {
        if (this.form) {
            this.form.reset();
        }
        
        this.currentProduct = null;
        
        const stockPreview = document.getElementById('stock-preview');
        const reasonCounter = document.getElementById('reason-counter');
        
        if (stockPreview) stockPreview.classList.add('d-none');
        if (reasonCounter) reasonCounter.textContent = '0/200 caracteres';
        
        this.clearErrors();
        this.updateHints('');
    }

    setLoading(loading) {
        this.isLoading = loading;
        const saveBtn = document.getElementById('btn-save-stock');
        const modalContent = document.querySelector('#stockModal .modal-content');

        if (saveBtn) {
            if (loading) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
            } else {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Confirmar Ajuste';
            }
        }

        if (modalContent) {
            if (loading) {
                modalContent.classList.add('loading-state');
            } else {
                modalContent.classList.remove('loading-state');
            }
        }
    }

    displayErrors(errors) {
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(`error-${field}`);
            const inputElement = document.getElementById(field.replace('_', '-'));

            if (errorElement && errors[field] && errors[field].length > 0) {
                errorElement.textContent = errors[field][0];
                errorElement.classList.add('show');

                if (inputElement) {
                    inputElement.classList.add('field-error');
                }
            }
        });
    }

    clearErrors() {
        // Limpar mensagens de erro
        document.querySelectorAll('#stock-form .error-message').forEach(el => {
            el.classList.remove('show');
            el.textContent = '';
        });

        // Remover classes de erro dos campos
        document.querySelectorAll('#stock-form .field-error').forEach(el => {
            el.classList.remove('field-error');
        });

        // Esconder erro geral
        const stockError = document.getElementById('stock-error');
        if (stockError) {
            stockError.classList.add('d-none');
        }
    }

    showToast(message, type = 'info') {
        const container = document.querySelector('.toast-container');
        if (!container) return;

        const colors = {
            success: 'text-bg-success',
            error: 'text-bg-danger',
            warning: 'text-bg-warning',
            info: 'text-bg-info'
        };

        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center ${colors[type] || colors.info} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toastEl);

        const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();

        // Remover elemento após ocultar
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }

    async request(url, method, body = null) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('CSRF token não encontrado');
        }

        const options = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken.content
            }
        };

        if (body) {
            options.body = body;
        }

        const response = await fetch(url, options);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Resposta não é JSON válido');
        }

        return await response.json();
    }
}