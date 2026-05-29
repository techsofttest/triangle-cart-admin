@extends('layouts.checkout')

@section('content')




 <!-- Checkout Body -->
    <section class="checkout-main-section pb-5" style="background-color: var(--c-white);">
        <div class="container">

            
            <div class="row gx-lg-5 d-flex flex-column flex-lg-row">

                <div class="col-lg-7 pt-4 pt-lg-5 mb-5 mb-lg-0 pe-lg-5 order-2 order-lg-1">
                    <form id="luxuryCheckoutForm">

                        @php /*
                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-end mb-4">
                                <h4 class="font-heading mb-0" style="color: var(--c-primary); font-size: 20px;">Contact
                                </h4>
                                <a href="#" class="text-decoration-none"
                                    style="font-family: var(--f-body); font-size: 13px; color: var(--c-primary); border-bottom: 1px solid #ccc;">Log
                                    in</a>
                            </div>

                            <input type="text" class="form-control luxury-input-minimal mb-3"
                                placeholder="Email or mobile phone number" required>

                            <div class="form-check luxury-checkbox-wrapper">
                                <input class="form-check-input luxury-checkbox-input" type="checkbox" id="newsOffers"
                                    checked>
                                <label class="form-check-label text-muted" for="newsOffers"
                                    style="font-family: var(--f-body); font-size: 13px;">
                                    Email me with news and exclusive offers
                                </label>
                            </div>
                        </div>
                        */ @endphp


                        @if(Auth::guard('customer')->check() && count($addresses) > 0)
                        <div class="mb-5">
                            <h4 class="font-heading mb-4" style="color: var(--c-primary); font-size: 20px;">Saved Addresses</h4>
                            <div class="row g-3">
                                @foreach($addresses as $addr)
                                <div class="col-md-6">
                                    <div class="card border p-3 cursor-pointer address-card @if($addr->is_default) border-gold @endif" 
                                         onclick="fillAddress({{ json_encode($addr) }}, this)"
                                         style="transition: all 0.3s ease; border-color: rgba(0,0,0,0.1) !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 fw-bold" style="font-size: 14px;">{{ $addr->name }}</h6>
                                            @if($addr->is_default)
                                                <span class="badge bg-gold" style="font-size: 9px;">DEFAULT</span>
                                            @endif
                                        </div>
                                        <p class="mb-0 text-muted small text-truncate">
                                            {{ $addr->address_line1 }}, {{ $addr->city }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="mb-5">
                            <h4 class="font-heading mb-4" style="color: var(--c-primary); font-size: 20px;">Delivery
                                Address
                            </h4>

                            <div class="row g-4">



                                <div class="col-md-6">
                                    <input type="text" name="first_name" class="form-control luxury-input-minimal"
                                        placeholder="First name" required>
                                </div>

                                <div class="col-md-6">
                                    <input type="text" name="last_name" class="form-control luxury-input-minimal" placeholder="Last name"
                                        required>
                                </div>

                                <div class="col-12">
                                    <input type="text" name="address" class="form-control luxury-input-minimal" placeholder="Address"
                                        required>
                                </div>

                                <div class="col-12">
                                    <input type="text" name="apartment" class="form-control luxury-input-minimal"
                                        placeholder="Apartment, suite, etc. (optional)">
                                </div>


                                <div class="col-12">
                                    <label class="text-uppercase text-muted d-block mb-1"
                                        style="font-family: var(--f-body); font-size: 10px; letter-spacing: 1px;">Country/Region</label>
                                    <input type="hidden" name="country" id="hidden_country" value="India">
                                    <div class="dropdown custom-select-dropdown w-100">
                                        <button
                                            class="btn btn-link w-100 text-start text-decoration-none d-flex justify-content-between align-items-center luxury-input-minimal px-0"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            style="color: var(--c-primary);">
                                            <span class="selected-text" id="display_country">India</span>
                                            <i class="fa-solid fa-chevron-down"
                                                style="font-size: 10px; color: var(--c-gold);"></i>
                                        </button>
                                        <ul class="dropdown-menu w-100 rounded-0 border-0 shadow-sm mt-1">
                                            <li><a class="dropdown-item active country-selector" href="#" data-value="India">India</a></li>
                                            <li><a class="dropdown-item country-selector" href="#" data-value="United Arab Emirates">United Arab Emirates</a></li>
                                            <li><a class="dropdown-item country-selector" href="#" data-value="United Kingdom">United Kingdom</a></li>
                                        </ul>
                                    </div>
                                </div>



                                <div class="col-md-4">
                                    <input type="text" name="city" class="form-control luxury-input-minimal" placeholder="City"
                                        required>
                                </div>

                                <div class="col-md-4">
                                   <input type="text" name="state" class="form-control luxury-input-minimal" placeholder="State"
                                        required>
                                </div>

                                <div class="col-md-4">
                                    <input type="text" name="pin_code" class="form-control luxury-input-minimal" placeholder="PIN code"
                                        required>
                                </div>

                                <div class="col-12 mb-2">
                                    <input type="tel" name="phone" class="form-control luxury-input-minimal" placeholder="Phone"
                                        required>
                                </div>


                                <div class="col-12 mb-2">
                                    <input type="email" name="email" class="form-control luxury-input-minimal" placeholder="Email"
                                        required>
                                </div>


                               

                                <div class="col-12 d-none">
                                    <div class="form-check luxury-checkbox-wrapper">
                                        <input class="form-check-input luxury-checkbox-input" type="checkbox"
                                            id="saveInfo">
                                        <label class="form-check-label text-muted" for="saveInfo"
                                            style="font-family: var(--f-body); font-size: 13px;">
                                            Save this information for next time
                                        </label>
                                    </div>
                                </div>

                                


                            </div>
                        </div>


                        @php /*

                        <div class="mb-5">
                            <h4 class="font-heading mb-4" style="color: var(--c-primary); font-size: 20px;">Shipping
                                method</h4>

                            <div class="border"
                                style="border-color: rgba(0,0,0,0.15) !important; background-color: var(--c-white);">

                                <div class="p-3 border-bottom" style="border-color: rgba(0,0,0,0.1) !important;">
                                    <div class="form-check luxury-radio m-0 py-1">
                                        
                                    
                                        <label
                                            class="form-check-label d-flex justify-content-between w-100 ms-2 cursor-pointer"
                                            for="standardShip"
                                            style="font-family: var(--f-body); font-size: 14px; color: var(--c-primary);">
                                            <span>Standard Shipping (5-7 Days)</span>
                                            <span class="fw-bold">Free</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="p-3">
                                    <div class="form-check luxury-radio m-0 py-1">
                                        <input class="form-check-input" type="radio" name="shippingMethod"
                                            id="whiteGloveShip">
                                        <label
                                            class="form-check-label d-flex justify-content-between w-100 ms-2 cursor-pointer"
                                            for="whiteGloveShip"
                                            style="font-family: var(--f-body); font-size: 14px; color: var(--c-primary);">
                                            <span>White Glove Assembly (2-4 Days)</span>
                                            <span class="fw-bold">₹2,500</span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        */ @endphp 


                        <input class="form-check-input" type="hidden" name="shippingMethod" value="normal" checked>



                        

                        <div class="mb-5">
                            <h4 class="font-heading mb-1" style="color: var(--c-primary); font-size: 20px;">Billing
                                address</h4>
                            <p class="text-muted mb-4" style="font-family: var(--f-body); font-size: 12px;">Select the
                                address that matches your card or payment method.</p>

                            <div class="border" style="border-color: rgba(0,0,0,0.15) !important; overflow: hidden;">

                                <div class="billing-box border-bottom"
                                    style="border-color: rgba(0,0,0,0.1) !important;">

                                    <div class="billing-header p-4 d-flex justify-content-between align-items-center"
                                        style="background-color: var(--c-linen); transition: background-color 0.3s ease;">
                                        <div class="form-check luxury-radio m-0">
                                            <input class="form-check-input billing-radio" type="radio"
                                                name="billingAddress" id="billingSame" data-target="desc-same" value="billingSame" checked>
                                            <label class="form-check-label ms-2 cursor-pointer" for="billingSame"
                                                style="font-family: var(--f-body); font-size: 14px; font-weight: 500; color: var(--c-primary);">
                                                Same as shipping address
                                            </label>
                                        </div>
                                    </div>
                                    <div id="desc-same" class="billing-desc" style="display: none;"></div>
                                </div>



                               

                                <div class="billing-box">

                                    <div class="billing-header p-4 d-flex justify-content-between align-items-center"
                                        style="background-color: var(--c-white); transition: background-color 0.3s ease;">
                                        <div class="form-check luxury-radio m-0">
                                            <input class="form-check-input billing-radio" type="radio"
                                                name="billingAddress" id="billingDifferent"
                                                data-target="desc-different" value="billingDifferent">
                                            <label class="form-check-label ms-2 cursor-pointer" for="billingDifferent"
                                                style="font-family: var(--f-body); font-size: 14px; font-weight: 500; color: var(--c-primary);">
                                                Use a different billing address
                                            </label>
                                        </div>
                                    </div>

                                    <div id="desc-different" class="billing-desc p-4 border-top text-start"
                                        style="background-color: #fafafa; border-color: rgba(0,0,0,0.08) !important; display: none;">

                                        <div class="row g-4">
                                            
                                            <div class="col-md-6">
                                                <input type="text" name="billing_first_name" class="form-control luxury-input-minimal"
                                                    style="background: transparent;"
                                                    placeholder="First name">
                                            </div>

                                            <div class="col-md-6">
                                                <input type="text" name="billing_last_name" class="form-control luxury-input-minimal"
                                                    style="background: transparent;" placeholder="Last name">
                                            </div>

                                            <div class="col-12">
                                                <input type="text" name="billing_address" class="form-control luxury-input-minimal"
                                                    style="background: transparent;" placeholder="Address">
                                            </div>

                                            <div class="col-12">
                                                <input type="text" name="billing_apartment" class="form-control luxury-input-minimal"
                                                    style="background: transparent;"
                                                    placeholder="Apartment, suite, etc. (optional)">
                                            </div>

                                            <div class="col-12">
                                                <label class="text-uppercase text-muted d-block mb-1"
                                                    style="font-family: var(--f-body); font-size: 10px; letter-spacing: 1px;">Country/Region</label>
                                                <input type="hidden" name="billing_country" id="hidden_billing_country" value="India">
                                                <div class="dropdown custom-select-dropdown w-100">
                                                    <button
                                                        class="btn btn-link w-100 text-start text-decoration-none d-flex justify-content-between align-items-center luxury-input-minimal px-0"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                        style="color: var(--c-primary); background: transparent;">
                                                        <span class="selected-text" id="display_billing_country">India</span>
                                                        <i class="fa-solid fa-chevron-down"
                                                            style="font-size: 10px; color: var(--c-gold);"></i>
                                                    </button>
                                                    <ul class="dropdown-menu w-100 rounded-0 border-0 shadow-sm mt-1">
                                                        <li><a class="dropdown-item active billing-country-selector" href="#" data-value="India">India</a></li>
                                                        <li><a class="dropdown-item billing-country-selector" href="#" data-value="United Arab Emirates">United Arab Emirates</a></li>
                                                        <li><a class="dropdown-item billing-country-selector" href="#" data-value="United Kingdom">United Kingdom</a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <input type="text" name="billing_city" class="form-control luxury-input-minimal"
                                                    style="background: transparent;" placeholder="City">
                                            </div>

                                            <div class="col-md-4">
                                                <input type="text" name="billing_state" class="form-control luxury-input-minimal"
                                                    style="background: transparent;" placeholder="State">
                                            </div>

                                            <div class="col-md-4">
                                                <input type="text" name="billing_pin_code" class="form-control luxury-input-minimal"
                                                    style="background: transparent;" placeholder="PIN code">
                                            </div>

                                            <div class="col-12">
                                                <input type="tel" name="billing_phone" class="form-control luxury-input-minimal"
                                                    style="background: transparent;" placeholder="Phone">
                                            </div>

                                            <div class="col-12">
                                                <input type="email" name="billing_email" class="form-control luxury-input-minimal"
                                                    style="background: transparent;" placeholder="Email">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                


                            </div>
                        </div>


                        <div class="mb-5">
                            <h4 class="font-heading mb-1" style="color: var(--c-primary); font-size: 20px;">Payment</h4>
                            <p class="text-muted mb-4" style="font-family: var(--f-body); font-size: 12px;">All
                                transactions are secure and encrypted.</p>

                            <div class="border" style="border-color: rgba(0,0,0,0.15) !important; overflow: hidden;">

                                <div class="payment-box border-bottom"
                                    style="border-color: rgba(0,0,0,0.1) !important;">

                                    <div class="payment-header p-4 d-flex justify-content-between align-items-center"
                                        style="background-color: var(--c-linen); transition: background-color 0.3s ease;">
                                        <div class="form-check luxury-radio m-0">
                                            <input class="form-check-input payment-radio" type="radio"
                                                name="paymentGateway" id="razorpay" data-target="desc-razorpay" checked>
                                            <label class="form-check-label ms-2 cursor-pointer" for="razorpay"
                                                style="font-family: var(--f-body); font-size: 14px; font-weight: 500; color: var(--c-primary);">
                                                Razorpay Secure
                                            </label>
                                        </div>
                                        <div class="payment-icons d-flex gap-2">
                                            <i class="fa-brands fa-apple-pay text-muted fs-4"></i>
                                            <i class="fa-brands fa-amazon-pay text-muted fs-4"></i>
                                            <i class="fa-brands fa-google-pay text-muted fs-4"></i>
                                            <i class="fa-brands fa-cc-visa text-muted fs-4"></i>
                                            <i class="fa-brands fa-cc-mastercard text-muted fs-4"></i>
                                        </div>
                                    </div>
                                  

                                </div>
                                  
                                </div>


                            </div>

                        <!-- Button -->
                        <div class="mobile-sticky-actions d-flex align-items-center justify-content-between mt-5 pt-3">

                            <a href="{{route('cart.index')}}"
                                class="text-decoration-none d-inline-flex align-items-center flex-shrink-0"
                                style="color: var(--c-primary); font-family: var(--f-body); font-size: 14px; font-weight: 500; letter-spacing: 1px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    class="me-2 d-none d-sm-block">
                                    <path d="M19 12H5" />
                                    <path d="M12 19l-7-7 7-7" />
                                </svg>
                                <span class="d-none d-sm-inline">Return to Cart</span>
                                <span class="d-inline d-sm-none"><i class="fa-solid fa-chevron-left me-1"></i>
                                    Cart</span>
                            </a>

                            <button type="submit"
                                class="btn-luxury-solid px-3 px-md-5 py-3 w-auto d-flex justify-content-center align-items-center ms-3 ms-md-0"
                                style="font-size: 13px; letter-spacing: 1.5px;">
                                <span>Pay Now</span>
                                <span class="mx-2 opacity-50" style="font-size: 10px;">•</span>
                                <span class="fw-bold" id="payNowTotal"
                                    style="font-family: var(--f-body); letter-spacing: 1px;">{{ $cartData['grand_total_formatted'] }}</span>
                            </button>

                        </div>

                    </form>
                </div>



                <!-- Ledger right -->
                <div class="col-lg-5 pt-0 pt-lg-5 responsive-ledger-border order-1 order-lg-2"
                    style="background-color: #fafafa;">

                    <div class="sticky-checkout-ledger p-4 p-lg-5">

                        <div class="checkout-items-list mb-4 pb-4 border-bottom-delicate-dark">
                            @if(isset($cartData['items']) && count($cartData['items']) > 0)
                                @foreach($cartData['items'] as $item)
                                <div class="d-flex align-items-center mb-4">
                                    <div class="position-relative me-3 flex-shrink-0">
                                        <div
                                            style="width: 64px; height: 64px; border: 1px solid rgba(0,0,0,0.1); background-color: var(--c-white); padding: 5px;">
                                            <img onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                                            style="width: 20px; height: 20px; font-size: 10px; opacity: 0.9;">{{ $item['quantity'] }}</span>
                                    </div>
                                    <div class="flex-grow-1 pe-3">
                                        <h6 class="mb-1"
                                            style="font-family: var(--f-body); font-size: 13px; font-weight: 600; color: var(--c-primary);">
                                            {{ $item['name'] }}</h6>
                                        <p class="text-muted mb-0" style="font-family: var(--f-body); font-size: 11px;">
                                            {{ $item['variant'] }}</p>
                                    </div>
                                    <span class="fw-bold flex-shrink-0"
                                        style="font-family: var(--f-body); font-size: 13px; color: var(--c-primary);">{{ $item['line_total_formatted'] }}</span>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted" style="font-family: var(--f-body); font-size: 13px;">Your cart is empty.</p>
                            @endif
                        </div>

                        <div class="mb-4 pb-4 border-bottom-delicate-dark">

                            <!-- Coupon code -->
                            <div class="coupon-section-v2 mb-4" id="couponContainer">
                                @if(isset($cartData['applied_coupon']) && $cartData['applied_coupon'])
                                    <div class="d-flex justify-content-between align-items-center p-3" style="background-color: var(--c-linen); border: 1px solid rgba(0,0,0,0.1);">
                                        <div>
                                            <span class="fw-bold" style="color: var(--c-primary); font-size: 13px;">
                                                <i class="fa-solid fa-tag me-2"></i> {{ $cartData['applied_coupon'] }}
                                            </span>
                                        </div>
                                        <button type="button" class="btn btn-link text-decoration-none py-0 px-2 m-0" style="color: var(--c-brown); font-size: 11px; font-weight: 500;" onclick="removeCoupon()">Remove</button>
                                    </div>
                                @else
                                    <div class="coupon-group-v2">
                                        <input type="text" id="couponCodeInput" class="coupon-input-v2" placeholder="Enter code here"
                                            aria-label="Coupon Code">

                                        <button type="button" class="coupon-apply-btn-v2" onclick="applyCoupon()">
                                            APPLY
                                        </button>
                                    </div>
                                @endif
                            </div>


                        </div>

                        <div class="ledger-block mb-4">
                            <div class="d-flex justify-content-between mb-2"
                                style="font-family: var(--f-body); font-size: 13px; color: var(--c-body);">
                                <span>Subtotal</span>
                                <span id="ledgerSubtotal">{{ $cartData['subtotal_formatted'] }}</span>
                            </div>

                            <div id="ledgerDiscountWrapper">
                            @if(isset($cartData['discount']) && $cartData['discount'] > 0)
                            <div class="d-flex justify-content-between mb-2"
                                style="font-family: var(--f-body); font-size: 13px; color: var(--c-body);">
                                <span>Discount <span class="badge" style="background-color: var(--c-gold); font-size: 10px; margin-left: 5px;">{{ $cartData['applied_coupon'] }}</span></span>
                                <span style="color: #2e7d32;">{{ $cartData['discount_formatted'] }}</span>
                            </div>
                            @endif
                            </div>

                            <div class="d-flex justify-content-between mb-4 pb-4 border-bottom-delicate-dark"
                                style="font-family: var(--f-body); font-size: 13px; color: var(--c-body);">
                                <span>Shipping</span>
                                <span id="ledgerShipping" class="fw-bold" style="font-size: 13px;">{{ $cartData['total_shipping_formatted'] }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <span class="font-heading"
                                    style="font-size: 18px; color: var(--c-primary);">Total</span>
                                <div class="text-end">
                                    <span class="text-muted me-2"
                                        style="font-family: var(--f-body); font-size: 11px;">INR</span>
                                    <span id="ledgerTotal" class="fs-4 fw-bold" style="color: var(--c-primary);">{{ $cartData['grand_total_formatted'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-none d-lg-flex flex-wrap gap-3 mt-5 pt-3 border-top-delicate-dark">
                            <a href="{{ route('policy.refund') }}" class="text-muted text-decoration-none"
                                style="font-family: var(--f-body); font-size: 11px;">Refund policy</a>
                            <a href="{{ route('policy.shipping') }}" class="text-muted text-decoration-none"
                                style="font-family: var(--f-body); font-size: 11px;">Shipping policy</a>
                            <a href="{{ route('policy.privacy') }}" class="text-muted text-decoration-none"
                                style="font-family: var(--f-body); font-size: 11px;">Privacy policy</a>
                            <a href="{{ route('policy.terms') }}" class="text-muted text-decoration-none"
                                style="font-family: var(--f-body); font-size: 11px;">Terms of service</a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection



@section('footer_extras')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    alertify.set('notifier','position', 'top-center');

    // Dropdown selectors
    $('.country-selector').on('click', function(e) {
        e.preventDefault();
        const val = $(this).data('value');
        $('#hidden_country').val(val);
        $('#display_country').text(val);
        $('.country-selector').removeClass('active');
        $(this).addClass('active');
    });

    // Billing Dropdown selectors
    $('.billing-country-selector').on('click', function(e) {
        e.preventDefault();
        const val = $(this).data('value');
        $('#hidden_billing_country').val(val);
        $('#display_billing_country').text(val);
        $('.billing-country-selector').removeClass('active');
        $(this).addClass('active');
    });

    function updateDOMfromCartData(cartData) {
        // ... (existing updateDOMfromCartData logic)
        let htmlCoupon = '';
        if (cartData.applied_coupon) {
            htmlCoupon = `
                <div class="d-flex justify-content-between align-items-center p-3" style="background-color: var(--c-linen); border: 1px solid rgba(0,0,0,0.1);">
                    <div>
                        <span class="fw-bold" style="color: var(--c-primary); font-size: 13px;">
                            <i class="fa-solid fa-tag me-2"></i> ${cartData.applied_coupon}
                        </span>
                    </div>
                    <button type="button" class="btn btn-link text-decoration-none py-0 px-2 m-0" style="color: var(--c-brown); font-size: 11px; font-weight: 500;" onclick="removeCoupon()">Remove</button>
                </div>
            `;
        } else {
            htmlCoupon = `
                <div class="coupon-group-v2">
                    <input type="text" id="couponCodeInput" class="coupon-input-v2" placeholder="Enter code here" aria-label="Coupon Code">
                    <button type="button" class="coupon-apply-btn-v2" onclick="applyCoupon()">APPLY</button>
                </div>
            `;
        }
        $('#couponContainer').html(htmlCoupon);

        $('#ledgerSubtotal').text(cartData.subtotal_formatted);
        $('#ledgerShipping').text(cartData.total_shipping_formatted);
        
        if (cartData.discount && cartData.discount > 0) {
            $('#ledgerDiscountWrapper').html(`
                <div class="d-flex justify-content-between mb-2" style="font-family: var(--f-body); font-size: 13px; color: var(--c-body);">
                    <span>Discount <span class="badge" style="background-color: var(--c-gold); font-size: 10px; margin-left: 5px;">${cartData.applied_coupon}</span></span>
                    <span style="color: #2e7d32;">${cartData.discount_formatted}</span>
                </div>
            `);
        } else {
            $('#ledgerDiscountWrapper').empty();
        }

        $('#ledgerTotal').text(cartData.grand_total_formatted);
        $('#payNowTotal').text(cartData.grand_total_formatted);
    }

    function applyCoupon() {
        // ... (existing applyCoupon logic)
        const code = document.getElementById('couponCodeInput').value;
        if(!code) {
            alertify.error('Please enter a coupon code');
            return;
        }

        $.ajax({
            url: "{{ route('cart.applyCoupon') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                coupon_code: code
            },
            success: function(response) {
                if(response.success) {
                    alertify.success(response.message);
                    updateDOMfromCartData(response.cartData);
                } else {
                    alertify.error(response.message);
                }
            },
            error: function() {
                alertify.error('Something went wrong processing coupon.');
            }
        });
    }

    function removeCoupon() {
        // ... (existing removeCoupon logic)
        $.ajax({
            url: "{{ route('cart.removeCoupon') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if(response.success) {
                    alertify.success(response.message);
                    updateDOMfromCartData(response.cartData);
                }
            }
        });
    }

    // Place Order submission
    $('#luxuryCheckoutForm').on('submit', function(e) {
        e.preventDefault();

        // Basic validation for state since it's a custom dropdown

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();

        submitBtn.prop('disabled', true).html('Processing...');

        $.ajax({
            url: "{{ route('order.place') }}",
            type: "POST",
            data: formData + "&_token={{ csrf_token() }}",
            success: function(response) {
                if (response.success) {
                    // Trigger Razorpay
                    var options = {
                        "key": response.key,
                        "amount": response.amount,
                        "currency": "INR",
                        "name": "Jodha Furniture",
                        "description": "Payment for order #" + response.order_number,
                        "image": "{{ asset('images/logo/favicon.png') }}",
                        "order_id": response.razorpay_order_id,
                        "handler": function (rzpResponse){
                            // Verify payment
                            $.ajax({
                                url: "{{ route('payment.verify') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    razorpay_payment_id: rzpResponse.razorpay_payment_id,
                                    razorpay_order_id: rzpResponse.razorpay_order_id,
                                    razorpay_signature: rzpResponse.razorpay_signature,
                                    order_number: response.order_number
                                },
                                success: function(verifyResponse) {
                                    if(verifyResponse.success) {
                                        alertify.success(verifyResponse.message);
                                        setTimeout(() => {
                                            window.location.href = verifyResponse.redirect_url;
                                        }, 1000);
                                    } else {
                                        alertify.error(verifyResponse.message);
                                        submitBtn.prop('disabled', false).html(originalBtnText);
                                    }
                                }
                            });
                        },
                        "prefill": {
                            "name": response.name,
                            "email": response.email,
                            "contact": response.contact
                        },
                        "theme": {
                            "color": "#9b804e"
                        },
                        "modal": {
                            "ondismiss": function(){
                                alertify.error('Payment cancelled.');
                                submitBtn.prop('disabled', false).html(originalBtnText);
                            }
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                } else {
                    alertify.error(response.message || 'Failed to initiate order.');
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        alertify.error(errors[key][0]);
                    });
                } else {
                    alertify.error('Something went wrong initiating the order.');
                }
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
</script>




 <!-- Edit Billing Address dropdown-->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Billing Address Accordion Logic
            const billingRadios = document.querySelectorAll('.billing-radio');
            const billingDescs = document.querySelectorAll('.billing-desc');
            const billingHeaders = document.querySelectorAll('.billing-header');

            billingRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    // 1. Hide all descriptions and reset header backgrounds to white
                    billingDescs.forEach(desc => desc.style.display = 'none');
                    billingHeaders.forEach(header => header.style.backgroundColor = 'var(--c-white)');

                    // 2. Show the targeted description form
                    const targetId = this.getAttribute('data-target');
                    const targetDesc = document.getElementById(targetId);
                    if (targetDesc && targetId === 'desc-different') {
                        targetDesc.style.display = 'block';
                        targetDesc.style.animation = 'fadeInDropdown 0.3s ease forwards';
                    }

                    // 3. Highlight the parent header background to Cashmere (var(--c-linen))
                    this.closest('.billing-header').style.backgroundColor = 'var(--c-linen)';
                });
            });

        });
        });
    </script>

    <script>
        function fillAddress(addr, element) {
            // Remove active class from all address cards
            $('.address-card').removeClass('border-gold').css('border-color', 'rgba(0,0,0,0.1)');
            $(element).addClass('border-gold').css('border-color', 'var(--c-gold)');

            // Fill the form
            $('input[name="address"]').val(addr.address_line1);
            $('input[name="apartment"]').val(addr.address_line2 || '');
            $('input[name="city"]').val(addr.city);
            $('input[name="state"]').val(addr.state);
            $('input[name="pin_code"]').val(addr.postal_code);
            $('input[name="phone"]').val(addr.phone);
            
            // Handle country
            const country = addr.country || 'India';
            $('#hidden_country').val(country);
            $('#display_country').text(country);
            
            // Highlight active country in list
            $('.country-selector').removeClass('active');
            $(`.country-selector[data-value="${country}"]`).addClass('active');

            alertify.success(`Address "${addr.name}" selected`);
        }
    </script>

    <style>
        .address-card:hover {
            border-color: var(--c-gold) !important;
            background-color: var(--c-linen);
        }
        .border-gold {
            border-color: var(--c-gold) !important;
            background-color: var(--c-linen);
        }
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endsection