// js/script.js

// Aguardar o carregamento completo da página
document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializar funcionalidades
    initializeCart();
    initializeFormValidation();
    initializeSearch();
    initializeProductActions();
    
    // Atualizar contador do carrinho na inicialização
    updateCartCount();
});

// ===== CARRINHO DE COMPRAS =====

// Inicializar funcionalidades do carrinho
function initializeCart() {
    // Event listeners para botões de adicionar ao carrinho
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const quantity = this.getAttribute('data-quantity') || 1;
            addToCart(productId, quantity);
        });
    });
    
    // Event listeners para atualizar quantidade no carrinho
    const quantityInputs = document.querySelectorAll('.cart-quantity');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const quantity = this.value;
            updateCartQuantity(productId, quantity);
        });
    });
    
    // Event listeners para remover itens do carrinho
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            removeFromCart(productId);
        });
    });
}

// Adicionar produto ao carrinho
function addToCart(productId, quantity = 1) {
    showLoading();
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            showNotification(data.message, 'success');
            updateCartCount();
            
            // Animar botão de adicionar ao carrinho
            const button = document.querySelector(`[data-product-id="${productId}"]`);
            if (button) {
                button.classList.add('btn-success');
                button.innerHTML = '<i class="fas fa-check"></i> Adicionado!';
                
                setTimeout(() => {
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                    button.innerHTML = 'Adicionar ao Carrinho';
                }, 2000);
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showNotification('Erro ao adicionar produto ao carrinho', 'error');
        console.error('Erro:', error);
    });
}

// Atualizar quantidade no carrinho
function updateCartQuantity(productId, quantity) {
    fetch('ajax/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            updateCartTotal();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Erro ao atualizar carrinho', 'error');
        console.error('Erro:', error);
    });
}

// Remover produto do carrinho
function removeFromCart(productId) {
    if (confirm('Tem certeza que deseja remover este item do carrinho?')) {
        fetch('ajax/remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateCartCount();
                
                // Remover linha do carrinho
                const cartItem = document.querySelector(`[data-cart-item="${productId}"]`);
                if (cartItem) {
                    cartItem.style.animation = 'fadeOut 0.5s ease-out';
                    setTimeout(() => {
                        cartItem.remove();
                        updateCartTotal();
                    }, 500);
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao remover produto', 'error');
            console.error('Erro:', error);
        });
    }
}

// Atualizar contador do carrinho
function updateCartCount() {
    fetch('ajax/get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = data.count || 0;
            
            // Animar contador se houver mudança
            if (data.count > 0) {
                cartCountElement.classList.add('animate-pulse');
                setTimeout(() => {
                    cartCountElement.classList.remove('animate-pulse');
                }, 1000);
            }
        }
    })
    .catch(error => {
        console.error('Erro ao atualizar contador do carrinho:', error);
    });
}

// Atualizar total do carrinho
function updateCartTotal() {
    fetch('ajax/get_cart_total.php')
    .then(response => response.json())
    .then(data => {
        const totalElement = document.getElementById('cart-total');
        if (totalElement) {
            totalElement.textContent = `R$ ${data.total.toFixed(2).replace('.', ',')}`;
        }
    })
    .catch(error => {
        console.error('Erro ao atualizar total do carrinho:', error);
    });
}

// ===== VALIDAÇÃO DE FORMULÁRIOS =====

function initializeFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                showNotification('Por favor, preencha todos os campos obrigatórios', 'error');
            }
            
            form.classList.add('was-validated');
        });
    });
    
    // Validação em tempo real
    const inputs = document.querySelectorAll('input[required], textarea[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
}

function validateField(field) {
    const isValid = field.checkValidity();
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
    }
    
    return isValid;
}

// Validação específica para email
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Validação específica para CPF (opcional)
function validateCPF(cpf) {
    cpf = cpf.replace(/[^\d]/g, '');
    
    if (cpf.length !== 11) return false;
    
    // Verificar se todos os dígitos são iguais
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    // Validar dígitos verificadores
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i);
    }
    
    let remainder = 11 - (sum % 11);
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.charAt(9))) return false;
    
    sum = 0;
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i);
    }
    
    remainder = 11 - (sum % 11);
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

// ===== BUSCA DE PRODUTOS =====

function initializeSearch() {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    
    if (searchForm && searchInput) {
        // Busca em tempo real (com debounce)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 3) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 500);
            } else if (query.length === 0) {
                clearSearchResults();
            }
        });
        
        // Busca ao submeter o formulário
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = searchInput.value.trim();
            if (query.length >= 3) {
                performSearch(query);
            }
        });
    }
}

function performSearch(query) {
    showLoading();
    
    fetch('ajax/search_products.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `query=${encodeURIComponent(query)}`
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        displaySearchResults(data.products || []);
    })
    .catch(error => {
        hideLoading();
        showNotification('Erro na busca', 'error');
        console.error('Erro:', error);
    });
}

function displaySearchResults(products) {
    const resultsContainer = document.getElementById('search-results');
    if (!resultsContainer) return;
    
    if (products.length === 0) {
        resultsContainer.innerHTML = '<p class="text-center">Nenhum produto encontrado.</p>';
        return;
    }
    
    let html = '<div class="row">';
    products.forEach(product => {
        html += `
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="${product.imagem || 'img/no-image.jpg'}" class="card-img-top" alt="${product.nome}">
                    <div class="card-body">
                        <h5 class="card-title">${product.nome}</h5>
                        <p class="card-text">${product.descricao}</p>
                        <p class="text-primary fw-bold">R$ ${parseFloat(product.preco).toFixed(2).replace('.', ',')}</p>
                        <button class="btn btn-primary add-to-cart" data-product-id="${product.id}">
                            Adicionar ao Carrinho
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    resultsContainer.innerHTML = html;
    
    // Reinicializar event listeners para os novos botões
    initializeCart();
}

function clearSearchResults() {
    const resultsContainer = document.getElementById('search-results');
    if (resultsContainer) {
        resultsContainer.innerHTML = '';
    }
}

// ===== AÇÕES DE PRODUTOS =====

function initializeProductActions() {
    // Filtros de categoria
    const categoryFilters = document.querySelectorAll('.category-filter');
    categoryFilters.forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.getAttribute('data-category-id');
            filterByCategory(categoryId);
        });
    });
    
    // Ordenação de produtos
    const sortSelect = document.getElementById('sort-products');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortBy = this.value;
            sortProducts(sortBy);
        });
    }
}

function filterByCategory(categoryId) {
    showLoading();
    
    fetch('ajax/filter_products.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `category_id=${categoryId}`
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        displayProducts(data.products || []);
    })
    .catch(error => {
        hideLoading();
        showNotification('Erro ao filtrar produtos', 'error');
        console.error('Erro:', error);
    });
}

function sortProducts(sortBy) {
    const productContainer = document.getElementById('products-container');
    if (!productContainer) return;
    
    const products = Array.from(productContainer.querySelectorAll('.product-card'));
    
    products.sort((a, b) => {
        switch (sortBy) {
            case 'name':
                return a.querySelector('.card-title').textContent.localeCompare(b.querySelector('.card-title').textContent);
            case 'price_asc':
                const priceA = parseFloat(a.querySelector('.product-price').getAttribute('data-price'));
                const priceB = parseFloat(b.querySelector('.product-price').getAttribute('data-price'));
                return priceA - priceB;
            case 'price_desc':
                const priceA2 = parseFloat(a.querySelector('.product-price').getAttribute('data-price'));
                const priceB2 = parseFloat(b.querySelector('.product-price').getAttribute('data-price'));
                return priceB2 - priceA2;
            default:
                return 0;
        }
    });
    
    // Reordenar elementos no DOM
    products.forEach(product => {
        productContainer.appendChild(product);
    });
}

// ===== UTILITÁRIOS =====

// Mostrar loading
function showLoading() {
    const loadingElement = document.getElementById('loading');
    if (loadingElement) {
        loadingElement.style.display = 'block';
    } else {
        // Criar elemento de loading se não existir
        const loading = document.createElement('div');
        loading.id = 'loading';
        loading.className = 'loading-overlay';
        loading.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(loading);
    }
}

// Esconder loading
function hideLoading() {
    const loadingElement = document.getElementById('loading');
    if (loadingElement) {
        loadingElement.style.display = 'none';
    }
}

// Mostrar notificações
function showNotification(message, type = 'info') {
    // Remover notificações existentes
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Criar nova notificação
    const notification = document.createElement('div');
    notification.className = `notification alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Remover automaticamente após 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Formatar moeda
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

// Máscara para CEP
function maskCEP(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    input.value = value;
}

// Máscara para telefone
function maskPhone(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d{4})(\d)/, '$1-$2');
    value = value.replace(/(\d{4})-(\d)(\d{4})/, '$1$2-$3');
    input.value = value;
}