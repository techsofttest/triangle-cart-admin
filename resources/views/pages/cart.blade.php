@extends('layouts.app')


@section('head_extras')

@endsection



@section('content')




    <!-- Progress Bar -->
    <section class="cart-progress-section pt-5 pb-4" style="background-color: var(--c-white);">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">

                    <div
                        class="luxury-progress-tracker d-flex justify-content-between align-items-center position-relative">

                        <div class="progress-line position-absolute top-50 start-0 translate-middle-y w-100 z-0"></div>

                        <div class="progress-step active position-relative z-1 d-flex flex-column align-items-center px-3"
                            style="background-color: var(--c-white);">
                            <span
                                class="step-number d-flex align-items-center justify-content-center rounded-circle mb-2">1</span>
                            <span class="step-label text-uppercase letter-spacing-1">Shopping Bag</span>
                        </div>

                        <div class="progress-step position-relative z-1 d-flex flex-column align-items-center px-3"
                            style="background-color: var(--c-white);">
                            <span
                                class="step-number d-flex align-items-center justify-content-center rounded-circle mb-2">2</span>
                            <span class="step-label text-uppercase letter-spacing-1">Address</span>
                        </div>

                        <div class="progress-step position-relative z-1 d-flex flex-column align-items-center px-3"
                            style="background-color: var(--c-white);">
                            <span
                                class="step-number d-flex align-items-center justify-content-center rounded-circle mb-2">3</span>
                            <span class="step-label text-uppercase letter-spacing-1">Payment</span>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>



    <!-- Main Cart Section -->
    <section class="cart-main-section pb-5" style="background-color: var(--c-white);">
        <div class="container">
            <div class="row gx-lg-5">

                <div class="col-lg-7 mb-5 mb-lg-0">

                    <div class="d-flex justify-content-between align-items-end mb-4 pb-2 border-bottom-delicate">
                        <h4 class="font-heading mb-0" style="color: var(--c-primary);">Shopping Bag</h4>
                        <span class="text-muted" style="font-family: var(--f-body); font-size: 13px;"><span id="mainCartCount">0</span> Items</span>
                    </div>

                    <div id="mainCartEmptyState" class="text-center py-5" style="display: none;">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1"
                            stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <circle cx="8" cy="21" r="1"></circle>
                            <circle cx="19" cy="21" r="1"></circle>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                        </svg>
                        <p class="text-muted font-marcellus" style="font-size: 18px;">Your shopping bag is empty</p>
                        <a href="{{ route('home') }}" class="btn-luxury-outline px-5 py-3 mt-3 text-decoration-none"
                            style="font-size: 13px;">Continue Shopping</a>
                    </div>

                    <div id="mainCartItemsContainer">
                        <!-- Dynamically populated by JS -->
                    </div>

                </div>

                <div class="col-lg-5" id="mainCartSummaryBox" style="display: none;">
                    <div class="sticky-cart-summary p-4 p-lg-5">

                        <h5 class="font-heading mb-4 text-uppercase letter-spacing-2"
                            style="font-size: 15px; color: var(--c-primary);">Order Summary</h5>


                        @php /*

                        <div class="mb-4 pb-4 border-bottom-delicate-dark">
                            <a href="#estimateShipping" data-bs-toggle="collapse"
                                class="d-flex justify-content-between align-items-center text-decoration-none"
                                style="color: var(--c-primary); font-family: var(--f-body); font-size: 13px;">
                                Estimate Shipping
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </a>
                            <div class="collapse mt-3" id="estimateShipping">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="dropdown custom-select-dropdown w-100">
                                            <button
                                                class="btn btn-link w-100 text-start text-decoration-none d-flex justify-content-between align-items-center luxury-input-minimal px-0"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                style="color: var(--c-primary);">
                                                <span class="selected-text">India</span>
                                                <i class="fa-solid fa-chevron-down"
                                                    style="font-size: 10px; color: var(--c-gold);"></i>
                                            </button>
                                            <ul class="dropdown-menu w-100 rounded-0 border-0 shadow-sm mt-1">
                                                <li><a class="dropdown-item active" href="#">India</a></li>
                                                <li><a class="dropdown-item" href="#">United Arab Emirates</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control luxury-input-minimal"
                                            placeholder="State/Province">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control luxury-input-minimal"
                                            placeholder="PIN Code">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <button id="btnCalculateShipping"
                                            class="btn btn-outline-dark rounded-0 w-100 py-2"
                                            style="font-family: var(--f-body); font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Calculate</button>
                                    </div>

                                    <div id="shippingResults" class="col-12 mt-4" style="display: none;">
                                        <div class="p-3"
                                            style="background-color: var(--c-white); border: 1px solid rgba(0,0,0,0.08);">
                                            <h6 class="text-uppercase letter-spacing-1 mb-3"
                                                style="font-family: var(--f-body); font-size: 11px; color: var(--c-primary); font-weight: 600;">
                                                Available Rates</h6>

                                            <div class="form-check luxury-radio mb-3">
                                                <input class="form-check-input" type="radio" name="shippingRate"
                                                    id="rateStandard" value="1200" checked>
                                                <label
                                                    class="form-check-label d-flex justify-content-between w-100 ms-2"
                                                    for="rateStandard"
                                                    style="font-family: var(--f-body); font-size: 13px; color: var(--c-body); cursor: pointer;">
                                                    <span>Standard Delivery (5-7 Days)</span>
                                                    <span class="fw-bold" style="color: var(--c-primary);">₹1,200</span>
                                                </label>
                                            </div>

                                            <div class="form-check luxury-radio mb-4">
                                                <input class="form-check-input" type="radio" name="shippingRate"
                                                    id="rateWhiteGlove" value="2500">
                                                <label
                                                    class="form-check-label d-flex justify-content-between w-100 ms-2"
                                                    for="rateWhiteGlove"
                                                    style="font-family: var(--f-body); font-size: 13px; color: var(--c-body); cursor: pointer;">
                                                    <span>White Glove Assembly (3-5 Days)</span>
                                                    <span class="fw-bold" style="color: var(--c-primary);">₹2,500</span>
                                                </label>
                                            </div>

                                            <button id="btnApplyShipping" class="btn btn-luxury-solid w-100 py-2"
                                                style="font-size: 11px; height: auto;">Apply to Total</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        */ @endphp



                        <div class="ledger-block mb-4">
                            <div class="d-flex justify-content-between mb-2"
                                style="font-family: var(--f-body); font-size: 13px; color: var(--c-body);">
                                <span>Subtotal</span>
                                <span id="mainCartSubtotal">₹0.00</span>
                            </div>
          

                            <div class="d-flex justify-content-between mb-4 pb-3 border-bottom-delicate-dark"
                                style="font-family: var(--f-body); font-size: 13px; color: var(--c-body);">
                                <span>Shipping</span>
                                <span>Calculated at next step</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="font-heading text-uppercase letter-spacing-1"
                                    style="font-size: 16px; color: var(--c-primary);">Grand Total</span>
                                <span class="fs-4 fw-bold" style="color: var(--c-primary);" id="mainCartTotal">₹0.00</span>
                            </div>
                            <p class="text-muted text-end" style="font-size: 10px;">Inclusive of all taxes</p>
                        </div>

                        <div class="d-flex flex-column gap-3 mb-4">
                            <a href="{{route('checkout.index')}}" class="btn-luxury-solid w-100 py-3" style="font-size: 13px;text-align:center">Checkout Now <i
                                    class="fa-solid fa-lock ms-2 small"></i></a>
                            <a href="{{ route('home') }}" class="btn-luxury-outline w-100 py-3 text-center text-decoration-none"
                                style="font-size: 13px;">Continue Shopping</a>
                        </div>

                        <div class="d-flex justify-content-center gap-4 pt-2">
                            <div class="text-center text-muted"
                                style="font-size: 10px; font-family: var(--f-body); text-transform: uppercase; letter-spacing: 1px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.2" class="mb-1 d-block mx-auto">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                                Secure
                            </div>
                            <div class="text-center text-muted"
                                style="font-size: 10px; font-family: var(--f-body); text-transform: uppercase; letter-spacing: 1px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.2" class="mb-1 d-block mx-auto">
                                    <rect x="3" y="8" width="18" height="4" rx="1" ry="1" />
                                    <path d="M12 8v13" />
                                    <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7" />
                                    <path
                                        d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5" />
                                </svg>
                                White Glove
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>





@endsection