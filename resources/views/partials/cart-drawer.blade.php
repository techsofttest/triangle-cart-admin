<!-- Luxury Cart Drawer -->
<div class="offcanvas offcanvas-end luxury-cart-drawer" tabindex="-1" id="cartDrawer"
    aria-labelledby="cartDrawerLabel">

    <div class="offcanvas-header border-bottom-delicate py-4 px-4">
        <h5 id="cartDrawerLabel" class="text-uppercase letter-spacing-2 mb-0"
            style="font-family: var(--f-body); font-size: 13px; font-weight: 600; color: var(--c-primary);">Cart
            (<span id="cartDrawerCount">0</span>)
        </h5>
        <button type="button" class="btn-close-luxury" data-bs-dismiss="offcanvas" aria-label="Close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <div class="offcanvas-body p-4" id="cartDrawerBody">
        <!-- Cart items will be rendered here dynamically -->
        <div id="cartEmptyState" class="text-center py-5">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1"
                stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                <circle cx="8" cy="21" r="1"></circle>
                <circle cx="19" cy="21" r="1"></circle>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            <p class="text-muted font-marcellus" style="font-size: 14px;">Your cart is empty</p>
            <a href="{{ route('home') }}" class="btn-luxury-outline px-4 py-2 text-decoration-none"
                style="font-size: 12px;" data-bs-dismiss="offcanvas">Continue Shopping</a>
        </div>

        <div id="cartItemsContainer">
            <!-- Dynamically populated -->
        </div>
    </div>

    <div class="offcanvas-footer border-top-delicate p-4 bg-white" id="cartDrawerFooter" style="display: none;">

      
        <div class="collapse mb-4" id="orderNoteCollapse">
            <textarea class="form-control luxury-cart-note" rows="3"
                placeholder="Special instructions for delivery or custom requests..."></textarea>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2 mt-2">
            <span class="text-uppercase letter-spacing-1"
                style="font-family: var(--f-body); font-size: 12px; font-weight: 600; color: var(--c-primary);">Subtotal</span>
            <span class="fs-5 fw-bold" style="color: var(--c-primary);" id="cartSubtotal">₹0.00</span>
        </div>
        <p class="text-muted mb-4" style="font-family: var(--f-body); font-size: 11px;">Taxes and shipping
            calculated at checkout</p>

        <div class="d-flex flex-column gap-3">
            <button class="btn-luxury-solid w-100" onclick="window.location.href='{{ route('cart.index') }}'">View Cart</button>

            <a href="{{route('checkout.index')}}" class="btn-luxury-outline w-100 d-flex justify-content-between align-items-center px-4">
                <span>Checkout</span>
                <span class="text-muted" style="font-size: 10px;">•</span>
                <span id="cartCheckoutTotal">₹0.00</span>
            </a>
        </div>

    </div>
</div>
