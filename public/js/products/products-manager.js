/**
 * Gerenciador de Produtos
 * Arquivo: public/js/products/products-manager.js
 */
class ProductManager {
    constructor() {
        this.modal = null;
        this.form = null;
        this.isLoading = false;
        this.isEditMode = false;
        this.currentProductId = null;
        
        this.init();
    }

    init() {
        // Verificar se os elementos existem antes de inicializar
        const modalElement = document.getElementById('productModal');
        const formElement = document.getElementById('product-form');
        
        if (!modalElement || !formElement) {
            console.warn('ProductManager: Modal ou formulário não encontrado');
            return;
        }

        this.modal = new bootstrap.Modal(modalElement);
        this.form = formElement;
        this.setupEvents();
    }

    setupEvents() {
        // Botão novo produto
        const btnNovo = document.getElementById('btn-novo-produto');
        if (btnNovo) {
            btnNovo.addEventListener('click', () => {
                this.openCreateModal();
            });
        }

        // Botão salvar
        const btnSave = document.getElementById('btn-save');
        if (btnSave) {
            btnSave.addEventListener('click', () => {
                this.saveProduct();
            });
        }

        // Alteração do tipo de produto
        const typeSelect = document.getElementById('product-type');
        if (typeSelect) {
            typeSelect.addEventListener('change', (e) => {
                this.toggleProductFields(e.target.value === 'product');
            });
        }

        // Botões de editar
        this.setupEditButtons();

        // Reset modal ao fechar
        const modalElement = document.getElementById('productModal');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', () => {
                this.resetForm();
            });
        }
    }

    setupEditButtons() {
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const productId = e.currentTarget.dataset.id;
                if (productId) {
                    this.openEditModal(productId);
                }
            });
        });
    }

    openCreateModal() {
        this.isEditMode = false;
        this.currentProductId = null;

        const modalTitle = document.getElementById('modal-title');
        const formMethod = document.getElementById('form-method');
        
        if (modalTitle) modalTitle.textContent = 'Novo Produto';
        if (formMethod) formMethod.value = 'POST';

        this.resetForm();
        this.modal.show();
    }

    async openEditModal(productId) {
        if (!productId) {
            this.showToast('ID do produto não encontrado', 'error');
            return;
        }

        this.isEditMode = true;
        this.currentProductId = productId;

        const modalTitle = document.getElementById('modal-title');
        const formMethod = document.getElementById('form-method');
        const productIdInput = document.getElementById('product-id');
        
        if (modalTitle) modalTitle.textContent = 'Editar Produto';
        if (formMethod) formMethod.value = 'PUT';
        if (productIdInput) productIdInput.value = productId;

        try {
            this.setLoading(true);

            const response = await this.request(`/products/${productId}/edit-data`, 'GET');

            if (response.success) {
                this.populateForm(response.data);
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

    async saveProduct() {
        if (this.isLoading) return;

        this.clearErrors();

        const formData = new FormData(this.form);
        
        // Garantir que checkbox seja enviado corretamente
        const activeCheckbox = document.getElementById('product-active');
        if (activeCheckbox && !activeCheckbox.checked) {
            formData.set('is_active', '0');
        }

        const url = this.isEditMode ? `/products/${this.currentProductId}` : '/products';

        try {
            this.setLoading(true);

            const response = await this.request(url, 'POST', formData);

            if (response.success) {
                this.showToast(response.message || 'Produto salvo com sucesso!', 'success');
                this.modal.hide();

                // Recarregar página após sucesso
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                if (response.errors) {
                    this.displayErrors(response.errors);
                }
                this.showToast(response.message || 'Erro ao salvar produto', 'error');
            }
        } catch (error) {
            console.error('Erro ao salvar produto:', error);
            this.showToast('Erro ao salvar produto', 'error');
        } finally {
            this.setLoading(false);
        }
    }

    populateForm(data) {
        // Preencher campos básicos
        this.setFieldValue('product-name', data.name);
        this.setFieldValue('product-category', data.category_id);
        this.setFieldValue('product-type', data.type);
        this.setFieldValue('product-description', data.description);
        this.setFieldValue('selling-price', data.selling_price);
        this.setFieldValue('purchase-price', data.purchase_price);
        this.setFieldValue('product-unit', data.unit);
        this.setFieldValue('stock-quantity', data.stock_quantity);
        this.setFieldValue('min-stock-level', data.min_stock_level);
        
        const activeCheckbox = document.getElementById('product-active');
        if (activeCheckbox) {
            activeCheckbox.checked = data.is_active;
        }

        // Mostrar/ocultar campos específicos
        this.toggleProductFields(data.type === 'product');
    }

    setFieldValue(fieldId, value) {
        const field = document.getElementById(fieldId);
        if (field && value !== null && value !== undefined) {
            field.value = value;
        }
    }

    resetForm() {
        if (this.form) {
            this.form.reset();
        }
        
        const activeCheckbox = document.getElementById('product-active');
        if (activeCheckbox) {
            activeCheckbox.checked = true;
        }
        
        this.toggleProductFields(false);
        this.clearErrors();
    }

    toggleProductFields(show) {
        const productFields = document.getElementById('product-fields');
        if (productFields) {
            productFields.style.display = show ? 'block' : 'none';
        }
    }

    setLoading(loading) {
        this.isLoading = loading;
        const saveBtn = document.getElementById('btn-save');
        const modalContent = document.querySelector('#productModal .modal-content');

        if (saveBtn) {
            if (loading) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
            } else {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Salvar';
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
        document.querySelectorAll('.error-message').forEach(el => {
            el.classList.remove('show');
            el.textContent = '';
        });

        // Remover classes de erro dos campos
        document.querySelectorAll('.field-error').forEach(el => {
            el.classList.remove('field-error');
        });

        // Esconder erro geral
        const formError = document.getElementById('form-error');
        if (formError) {
            formError.classList.add('d-none');
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