/**
 * Jodha Cart - AJAX Cart Management
 */
const JodhaCart = {

    /**
     * Initialize cart on page load
     */
    init() {
        this.loadCart();
        this.bindCartIcon();
    },

    /**
     * Bind cart icon click to open drawer
     */
    bindCartIcon() {
        const cartLink = document.getElementById('headerCartLink');
        if (cartLink) {
            cartLink.addEventListener('click', function (e) {
                e.preventDefault();
                const drawer = new bootstrap.Offcanvas(document.getElementById('cartDrawer'));
                drawer.show();
            });
        }
    },

    /**
     * Get CSRF token from meta tag
     */
    getToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    },

    /**
     * Load cart from server
     */
    async loadCart() {
        try {
            const res = await fetch('/cart', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                this.renderCart(data);
            }
        } catch (err) {
            console.error('Failed to load cart:', err);
        }
    },

    /**
     * Add item to cart
     */
    async addToCart(productId, quantity = 1, sizeId = null, colorId = null) {
        try {
            const body = {
                product_id: productId,
                quantity: quantity,
            };
            if (sizeId) body.size_id = sizeId;
            if (colorId) body.color_id = colorId;

            const res = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });

            const data = await res.json();

            if (data.success) {
                this.renderCart(data);
                this.showToast(data.message, 'success');
                // Open cart drawer
                const drawer = new bootstrap.Offcanvas(document.getElementById('cartDrawer'));
                drawer.show();
            } else {
                this.showToast('Failed to add item to cart.', 'error');
            }
        } catch (err) {
            console.error('Add to cart error:', err);
            this.showToast('Something went wrong.', 'error');
        }
    },

    /**
     * Add item to cart and redirect to checkout
     */
    async buyNow(productId, quantity = 1, sizeId = null, colorId = null) {
        try {
            const body = {
                product_id: productId,
                quantity: quantity,
            };
            if (sizeId) body.size_id = sizeId;
            if (colorId) body.color_id = colorId;

            const res = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });

            const data = await res.json();

            if (data.success) {
                window.location.href = '/checkout';
            } else {
                this.showToast('Failed to process.', 'error');
            }
        } catch (err) {
            console.error('Buy now error:', err);
            this.showToast('Something went wrong.', 'error');
        }
    },

    /**
     * Update cart item quantity
     */
    async updateQuantity(cartKey, quantity) {
        if (quantity < 1) {
            this.removeItem(cartKey);
            return;
        }

        try {
            const res = await fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ cart_key: cartKey, quantity: quantity }),
            });

            const data = await res.json();

            if (data.success) {
                this.renderCart(data);
            }
        } catch (err) {
            console.error('Update cart error:', err);
        }
    },

    /**
     * Remove item from cart
     */
    async removeItem(cartKey) {
        try {
            const res = await fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ cart_key: cartKey }),
            });

            const data = await res.json();

            if (data.success) {
                this.renderCart(data);
                this.showToast(data.message, 'success');
            }
        } catch (err) {
            console.error('Remove from cart error:', err);
        }
    },

    /**
     * Render the cart drawer contents
     */
    renderCart(data) {
        // Update header badge
        const badges = document.querySelectorAll('.cart-badge');
        badges.forEach(badge => {
            badge.textContent = data.count;
            badge.style.display = data.count > 0 ? 'flex' : 'none';
        });

        // Update drawer count
        const drawerCount = document.getElementById('cartDrawerCount');
        if (drawerCount) drawerCount.textContent = data.count;

        // Update subtotal
        const subtotalEl = document.getElementById('cartSubtotal');
        const checkoutTotal = document.getElementById('cartCheckoutTotal');
        if (subtotalEl) subtotalEl.textContent = data.subtotal_formatted;
        if (checkoutTotal) checkoutTotal.textContent = data.subtotal_formatted;

        // Show/hide empty state and footer
        const emptyState = document.getElementById('cartEmptyState');
        const itemsContainer = document.getElementById('cartItemsContainer');
        const footer = document.getElementById('cartDrawerFooter');

        if (data.cart.length === 0) {
            if (emptyState) emptyState.style.display = 'block';
            if (itemsContainer) itemsContainer.innerHTML = '';
            if (footer) footer.style.display = 'none';
        } else {
            if (emptyState) emptyState.style.display = 'none';
            if (footer) footer.style.display = 'block';

            let html = '';
            data.cart.forEach(item => {
                html += this.buildCartItemHtml(item);
            });
            if (itemsContainer) itemsContainer.innerHTML = html;
        }

        // Update main cart page elements if they exist
        const mainCount = document.getElementById('mainCartCount');
        if (mainCount) mainCount.textContent = data.count;

        const mainSubtotal = document.getElementById('mainCartSubtotal');
        if (mainSubtotal) mainSubtotal.textContent = data.subtotal_formatted;

        const mainTotal = document.getElementById('mainCartTotal');
        if (mainTotal) mainTotal.textContent = data.subtotal_formatted;

        const mainEmptyState = document.getElementById('mainCartEmptyState');
        const mainItemsContainer = document.getElementById('mainCartItemsContainer');
        const mainSummaryBox = document.getElementById('mainCartSummaryBox');

        if (mainItemsContainer && mainEmptyState) {
            if (data.cart.length === 0) {
                mainEmptyState.style.display = 'block';
                mainItemsContainer.innerHTML = '';
                if (mainSummaryBox) mainSummaryBox.style.display = 'none';
            } else {
                mainEmptyState.style.display = 'none';
                if (mainSummaryBox) mainSummaryBox.style.display = 'block';

                let html = '';
                data.cart.forEach(item => {
                    html += this.buildMainCartItemHtml(item);
                });
                mainItemsContainer.innerHTML = html;
            }
        }
    },

    /**
     * Build HTML for a single cart item on the MAIN cart page
     */
    buildMainCartItemHtml(item) {
        return `
            <div class="cart-page-item d-flex gap-3 gap-md-4 py-4 border-bottom-delicate" data-cart-key="${item.cart_key}">
                <a href="${item.url}" class="cart-img-wrap flex-shrink-0" style="width: 100px; height: 100px;">
                    <img onerror="this.onerror=null;this.src='images/placeholder.png';" src="${item.image}" alt="${item.name}" class="w-100 h-100 object-fit-cover rounded">
                </a>

                <div class="cart-item-info d-flex flex-column justify-content-between w-100">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2">
                        <div>
                            <h5 class="mb-1" style="font-size: 16px;">
                                <a href="${item.url}" class="text-decoration-none font-heading" style="color: var(--c-primary);">${item.name}</a>
                            </h5>
                            ${item.variant ? `<p class="text-muted mb-1" style="font-family: var(--f-body); font-size: 12px;">${item.variant}</p>` : ''}
                        </div>
                        <span class="fs-6 fw-bold" style="color: var(--c-primary);">${item.price_formatted}</span>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3">
                        <div class="qty-selector d-inline-flex align-items-center justify-content-between border rounded" style="width: 100px; height: 35px; background: #fff;">
                            <button type="button" class="btn-qty border-0 bg-transparent px-2 text-dark" onclick="JodhaCart.updateQuantity('${item.cart_key}', ${item.quantity - 1})">
                                <i class="fa-solid fa-minus" style="font-size: 10px;"></i>
                            </button>

                            <input class="qty-input text-center border-0 bg-transparent p-0 w-100" min="1" value="${item.quantity}" type="number" style="font-size: 14px; pointer-events: none;">

                            <button type="button" class="btn-qty border-0 bg-transparent px-2 text-dark" onclick="JodhaCart.updateQuantity('${item.cart_key}', ${item.quantity + 1})">
                                <i class="fa-solid fa-plus" style="font-size: 10px;"></i>
                            </button>
                        </div>

                        <button class="btn p-0 text-dark text-decoration-none letter-spacing-1 d-flex align-items-center gap-2" onclick="JodhaCart.removeItem('${item.cart_key}')" style="font-family: var(--f-body); font-size: 11px; text-transform: uppercase; font-weight: 500;">
                            <i class="fa-regular fa-trash-can" style="font-size: 14px;"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
    },

    /**
     * Build HTML for a single cart item
     */
    buildCartItemHtml(item) {
        return `
            <div class="cart-item d-flex gap-4 mb-4 pb-4 border-bottom-delicate" data-cart-key="${item.cart_key}">
                <a href="${item.url}" class="cart-item-img-wrapper flex-shrink-0">
                    <img onerror="this.onerror=null;this.src='images/placeholder.png';" src="${item.image}" alt="${item.name}" class="cart-item-img">
                </a>

                <div class="cart-item-details d-flex flex-column justify-content-between w-100">
                    <div>
                        <h6 class="mb-1 text-uppercase"
                            style="font-family: var(--f-body); font-size: 11px; letter-spacing: 1px;">
                            <a href="${item.url}" class="text-decoration-none" style="color: var(--c-primary);">${item.name}</a>
                        </h6>
                        ${item.variant ? `<span class="d-block text-muted mb-2" style="font-family: var(--f-body); font-size: 11px;">${item.variant}</span>` : ''}
                        <span class="fw-bold" style="font-size: 13px; color: var(--c-primary);">${item.price_formatted}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <div class="qty-selector mini d-inline-flex align-items-center justify-content-between">
                            <button type="button" class="btn-qty" onclick="JodhaCart.updateQuantity('${item.cart_key}', ${item.quantity - 1})">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                            </button>
                            <input class="qty-input text-center" min="1" value="${item.quantity}" type="number"
                                onchange="JodhaCart.updateQuantity('${item.cart_key}', parseInt(this.value))"
                                style="pointer-events: none;">
                            <button type="button" class="btn-qty" onclick="JodhaCart.updateQuantity('${item.cart_key}', ${item.quantity + 1})">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                            </button>
                        </div>

                        <a href="javascript:void(0)" onclick="JodhaCart.removeItem('${item.cart_key}')" class="text-muted text-decoration-underline"
                            style="font-family: var(--f-body); font-size: 11px; letter-spacing: 1px;">Remove</a>
                    </div>
                </div>
            </div>
        `;
    },

    /**
     * Show a toast notification
     */
    showToast(message, type = 'success') {
        // Remove existing toast
        const existing = document.getElementById('jodhaToast');
        if (existing) existing.remove();

        const bgColor = type === 'success' ? 'var(--c-primary, #1a1a1a)' : '#dc3545';

        const toast = document.createElement('div');
        toast.id = 'jodhaToast';
        toast.innerHTML = `
            <div style="
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 99999;
                padding: 14px 24px;
                background: ${bgColor};
                color: white;
                font-family: var(--f-body, sans-serif);
                font-size: 13px;
                letter-spacing: 0.5px;
                border-radius: 0;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                gap: 10px;
                animation: slideInToast 0.3s ease-out;
                max-width: 350px;
            ">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    ${type === 'success'
                ? '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'
                : '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'
            }
                </svg>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.firstElementChild) {
                toast.firstElementChild.style.animation = 'slideOutToast 0.3s ease-in forwards';
            }
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
};


// Initialize cart when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    JodhaCart.init();
});
