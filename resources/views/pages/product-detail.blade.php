@extends('layouts.app')



@section('head_extras')

@endsection



@section('content')

     

    <!-- Product Hero Section -->
    <section class="product-hero-section py-5" style="background-color: var(--c-white);">
        <div class="container">
            <div class="row gx-lg-5">

                <div class="col-lg-7 mb-5 mb-lg-0">

                    <div class="product-gallery-container d-flex gap-3 align-items-stretch" style="height: 600px;">

                        <div class="vertical-thumb-column d-none d-md-flex flex-column align-items-center py-2"
                            style="width: 80px; flex-shrink: 0;">
                            <button class="thumb-nav-btn mb-2" onclick="scrollThumbs('up')">
                                <i class="fa-solid fa-angle-up"></i>
                            </button>

                            <div class="thumb-track d-flex flex-column gap-2 flex-grow-1 overflow-hidden w-100"
                                id="thumbTrack">

                                {{-- Main product image as first thumbnail --}}
                                <div class="thumb-item active" data-bs-target="#productGallery" data-bs-slide-to="0">
                                    <img onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" src="{{ asset('storage/'.$product->prod_image) }}" alt="{{ $product->prod_name }}">
                                </div>

                                {{-- Additional product images --}}
                                @foreach($product->images as $index => $image)
                                <div class="thumb-item" data-bs-target="#productGallery" data-bs-slide-to="{{ $index + 1 }}">
                                    <img onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $product->prod_name }} - Image {{ $index + 2 }}">
                                </div>
                                @endforeach

                            </div>

                            <button class="thumb-nav-btn mt-2" onclick="scrollThumbs('down')">
                                <i class="fa-solid fa-angle-down"></i>
                            </button>
                        </div>

                        <div id="productGallery"
                            class="carousel slide luxury-carousel position-relative flex-grow-1 h-100"
                            data-bs-ride="false">

                            {{-- 
                            <button class="hover-action-btn btn-lg-circle position-absolute z-3" title="Add to Wishlist"
                                style="top: 20px; right: 20px;">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                            --}}

                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#productGallery" data-bs-slide-to="0"
                                    class="active" aria-current="true" aria-label="Slide 1"></button>
                                @foreach($product->images as $index => $image)
                                <button type="button" data-bs-target="#productGallery" data-bs-slide-to="{{ $index + 1 }}"
                                    aria-label="Slide {{ $index + 2 }}"></button>
                                @endforeach
                            </div>

                            <div class="carousel-inner h-100" style="background-color: var(--c-linen);">
                                {{-- Main product image --}}
                                <div class="carousel-item active h-100">
                                    <img onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" src="{{ asset('storage/'.$product->prod_image) }}"
                                        class="d-block w-100 h-100 product-main-img" alt="{{ $product->prod_name }}"
                                        style="object-fit: cover;">
                                </div>
                                {{-- Additional images --}}
                                @foreach($product->images as $image)
                                <div class="carousel-item h-100">
                                    <img onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" src="{{ asset('storage/'.$image->image_path) }}"
                                        class="d-block w-100 h-100 product-main-img" alt="{{ $product->prod_name }}"
                                        style="object-fit: cover;">
                                </div>
                                @endforeach
                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#productGallery"
                                data-bs-slide="prev">
                                <span class="ctrl-btn-luxury d-flex align-items-center justify-content-center"
                                    aria-hidden="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 18l-6-6 6-6" />
                                    </svg>
                                </span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productGallery"
                                data-bs-slide="next">
                                <span class="ctrl-btn-luxury d-flex align-items-center justify-content-center"
                                    aria-hidden="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 18l6-6-6-6" />
                                    </svg>
                                </span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>

                    <div class="accordion accordion-luxury mt-5" id="productDetailsAccordion">

                        <div class="accordion-item">    
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Product Details
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                data-bs-parent="#productDetailsAccordion">
                                <div class="accordion-body text-muted"
                                    style="font-family: var(--f-body); font-size: 13.5px; line-height: 1.8;">
                                    {!! $product->prod_description !!}
                                </div>
                            </div>
                        </div>

                        @if($product->material || $product->prod_material)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Materials & Care
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                data-bs-parent="#productDetailsAccordion">
                                <div class="accordion-body text-muted"
                                    style="font-family: var(--f-body); font-size: 13.5px; line-height: 1.8;">
                                    @if($product->material)
                                        <p class="mb-2"><strong>{{ $product->material->name }}</strong></p>
                                    @endif
                                    @if($product->prod_material)
                                        {!! $product->prod_material !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($product->prod_measurements)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Measurements
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                data-bs-parent="#productDetailsAccordion">
                                <div class="accordion-body text-muted"
                                    style="font-family: var(--f-body); font-size: 13.5px; line-height: 1.8;">
                                    {!! $product->prod_measurements !!}
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="sticky-pdp-info pe-lg-4">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-3 text-uppercase"
                                style="font-family: var(--f-body); font-size: 10px; letter-spacing: 2px;">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a>
                                </li>
                                @if($product->category)
                                <li class="breadcrumb-item"><a href="{{ route('category.show', $product->category->slug) }}"
                                        class="text-muted text-decoration-none">{{ $product->category->name }}</a></li>
                                @endif
                                @if($product->subcategory)
                                <li class="breadcrumb-item"><a href="{{ route('subcategory.show', $product->subcategory->subcat_slug) }}"
                                        class="text-muted text-decoration-none">{{ $product->subcategory->subcat_name }}</a></li>
                                @endif
                                <li class="breadcrumb-item active" aria-current="page"
                                    style="color: var(--c-primary); font-weight: 600;">{{ $product->prod_name }}</li>
                            </ol>
                        </nav>

                        <h1 class="display-5 font-heading mb-2" style="color: var(--c-primary);">{{ $product->prod_name }}</h1>

                        @if($product->prod_sku_code)
                        <div class="d-flex align-items-center flex-wrap mb-4">
                            
                            <span class="text-muted"
                                style="font-family: var(--f-body); font-size: 12px; letter-spacing: 1px;">
                                SKU: {{ $product->prod_sku_code }}
                            </span>

                        </div>
                        @endif

                        <div class="price-block mb-4 pb-4 border-bottom-delicate">
                            <div class="d-flex align-items-center gap-3">
                                @if(!empty($product->prod_sale_price) && $product->prod_price > $product->prod_sale_price)
                                    <span class="fs-3 fw-bold" style="color: var(--c-primary);">₹{{ number_format($product->prod_sale_price, 2) }}</span>
                                    <span class="fs-5 text-decoration-line-through text-muted">₹{{ number_format($product->prod_price, 2) }}</span>
                                    @if($product->offer_percentage > 0)
                                    <span class="badge bg-gold text-dark rounded-0 px-2 py-1"
                                        style="font-size: 10px; letter-spacing: 1px;">SAVE {{ $product->offer_percentage }}%</span>
                                    @endif
                                @else
                                    <span class="fs-3 fw-bold" style="color: var(--c-primary);">₹{{ number_format($product->prod_sale_price ?: $product->prod_price, 2) }}</span>
                                @endif
                            </div>
                            <p class="text-muted mt-2" style="font-size: 11px;">Inclusive of all taxes. Shipping
                                calculated at checkout.</p>
                        </div>

                        @php /*
                        @if($product->prod_stock > 0 && $product->prod_stock <= 10)
                        <div class="d-flex align-items-center mb-4"
                            style="color: var(--c-gold); font-size: 13px; font-weight: 500;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span
                                style="font-family: var(--f-body); font-size: 12px; color: var(--c-primary); font-weight: 500;">Hurry
                                up, only {{ $product->prod_stock }} {{ Str::plural('item', $product->prod_stock) }} left in stock.</span>
                        </div>
                        @endif
                        */ @endphp

                        @if($product->colors && $product->colors->count() > 0)
                        <div class="mb-4">
                            <label class="text-uppercase mb-2 d-block"
                                style="font-family: var(--f-body); font-size: 11px; font-weight: 600; letter-spacing: 1px; color: var(--c-primary);">Colors</label>
                            <div class="d-flex flex-wrap gap-2">
                                <select id="pdpColorSelect" class="form-select rounded-0 border-dark" style="width: auto; min-width: 150px; font-family: var(--f-body); font-size: 13px; font-weight: 500;">
                                    <option value="" disabled selected>Select Color</option>
                                    @foreach($product->colors as $color)
                                    <option value="{{ $color->id }}">{{ $color->color_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        @if($product->sizes && $product->sizes->count() > 0)
                        <div class="mb-4">
                            <label class="text-uppercase mb-2 d-block"
                                style="font-family: var(--f-body); font-size: 11px; font-weight: 600; letter-spacing: 1px; color: var(--c-primary);">Sizes</label>
                            <div class="d-flex flex-wrap gap-2">
                                <select id="pdpSizeSelect" class="form-select rounded-0 border-dark" style="width: auto; min-width: 150px; font-family: var(--f-body); font-size: 13px; font-weight: 500;">
                                    <option value="" disabled selected>Select Size</option>
                                    @foreach($product->sizes as $size)
                                    <option value="{{ $size->id }}" data-price="{{ $size->offer_price ?? $size->price }}">{{ $size->size }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label class="text-uppercase mb-2 d-block"
                                style="font-family: var(--f-body); font-size: 11px; font-weight: 600; letter-spacing: 1px; color: var(--c-primary);">Quantity</label>
                            <div class="qty-selector d-inline-flex align-items-center justify-content-between">
                                <button type="button" class="btn-qty"
                                    onclick="this.parentNode.querySelector('input[type=number]').stepDown()">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                </button>
                                <input class="qty-input text-center" min="1" name="quantity" value="1" type="number" id="pdpQuantity">
                                <button type="button" class="btn-qty"
                                    onclick="this.parentNode.querySelector('input[type=number]').stepUp()">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-3 mb-5">
                            <button class="btn-luxury-solid w-100 font-marcellus fs-5" type="button"
                                id="pdpAddToCart" data-product-id="{{ $product->id }}">
                                Add to cart
                            </button>

                             <button
                                class="btn-luxury-outline w-100 font-marcellus fs-5 d-flex justify-content-center align-items-center gap-2" id="pdpBuyNow" data-product-id="{{ $product->id }}">
                                Buy Now
                                <div class="d-flex gap-1 align-items-center ms-2">
                                    <img src="{{ asset('images/payment-m-logo/paytm.png') }}" alt="Paytm"
                                        style="height: 24px; width: 24px; border-radius: 50%; background-color: #f0f0f0; object-fit: cover;">
                                    <img src="{{ asset('images/payment-m-logo/phonepe-icon.png') }}" alt="PhonePe"
                                        style="height: 24px; width: 24px; border-radius: 50%; background-color: #f0f0f0; object-fit: cover;">
                                    <img src="{{ asset('images/payment-m-logo/google.png') }}" alt="Google Pay"
                                        style="height: 24px; width: 24px; border-radius: 50%; background-color: #f0f0f0; object-fit: cover;">
                                </div>
                            </button>

                            <div class="d-flex justify-content-center gap-2 mt-2 flex-wrap">
                                <img src="{{ asset('images/payment-m-logo/visa.png') }}" alt="Visa"
                                    class="border rounded bg-white"
                                    style="height: 32px; width: 50px; object-fit: contain;">
                                <img src="{{ asset('images/payment-m-logo/mastercard.png') }}" alt="Mastercard"
                                    class="border rounded bg-white"
                                    style="height: 32px; width: 50px; object-fit: contain;">
                                <img src="{{ asset('images/payment-m-logo/american-express.png') }}" alt="Amex"
                                    class="border rounded bg-white"
                                    style="height: 32px; width: 50px; object-fit: contain;">
                                <img src="{{ asset('images/payment-m-logo/paypal.png') }}" alt="PayPal"
                                    class="border rounded bg-white"
                                    style="height: 32px; width: 50px; object-fit: contain;">
                                <img src="{{ asset('images/payment-m-logo/discover.png') }}" alt="Discover"
                                    class="border rounded bg-white"
                                    style="height: 32px; width: 50px; object-fit: contain;">
                                <img src="{{ asset('images/payment-m-logo/gpay.png') }}" alt="Google Pay"
                                    class="border rounded bg-white"
                                    style="height: 32px; width: 50px; object-fit: contain;">
                                <img src="{{ asset('images/payment-m-logo/applepay.png') }}" alt="Apple Pay"
                                    class="border rounded bg-white"
                                    style="height: 32px; width: 50px; object-fit: contain;">
                            </div>

                            @php /*

                            @if($product->prod_expected_delivery)
                            <div class="delivery-estimate-container mt-4 pt-3">
                                <h6 class="text-center font-marcellus mb-4 fw-bold text-dark"
                                    style="font-size: 1.05rem;">
                                    Customized orders may take {{ $product->prod_expected_delivery }} days for delivery
                                </h6>

                              

                                <div class="timeline-wrapper position-relative mx-auto" style="max-width: 450px;">
                                    <div class="position-absolute w-100"
                                        style="height: 1px; background-color: #d3d3d3; top: 17px; left: 0; z-index: 1;">
                                    </div>

                                    <div class="d-flex justify-content-between position-relative z-2">
                                        <div class="d-flex flex-column align-items-center text-center"
                                            style="background-color: var(--bg-color); padding: 0 10px; width: 140px;">
                                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mb-2"
                                                style="width: 35px; height: 35px;">
                                                <i class="fa-regular fa-clipboard" style="font-size: 1rem;"></i>
                                            </div>
                                            <span class="font-marcellus text-dark"
                                                style="font-size: 0.95rem;">Ordered</span>
                                            <span class="font-marcellus text-muted mt-1"
                                                style="font-size: 0.8rem;"></span>
                                        </div>

                                        <div class="d-flex flex-column align-items-center text-center"
                                            style="background-color: var(--bg-color); padding: 0 10px;  width: 140px;">
                                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mb-2"
                                                style="width: 35px; height: 35px;">
                                                <i class="fa-solid fa-truck-fast" style="font-size: 0.95rem;"></i>
                                            </div>
                                            <span class="font-marcellus text-dark" style="font-size: 0.95rem;">Order
                                                Ready</span>
                                            <span class="font-marcellus text-muted mt-1"
                                                style="font-size: 0.8rem;"></span>
                                        </div>

                                        <div class="d-flex flex-column align-items-center text-center"
                                            style="background-color: var(--bg-color); padding: 0 10px;  width: 140px;">
                                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mb-2"
                                                style="width: 35px; height: 35px;">
                                                <i class="fa-solid fa-house" style="font-size: 0.95rem;"></i>
                                            </div>
                                            <span class="font-marcellus text-dark"
                                                style="font-size: 0.95rem;">Delivered</span>
                                            <span class="font-marcellus text-muted mt-1"
                                                style="font-size: 0.8rem;"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4 pt-2 font-marcellus text-muted"
                                    style="font-size: 0.85rem;">
                                    Powered by <i class="fa-solid fa-circle-chevron-up text-dark ms-1 me-1"></i> <a
                                        href="#" class="text-muted text-decoration-underline">NestScale</a>
                                </div>


                            </div>
                            @endif

                            */ @endphp



                        </div>

                        <div class="product-perks pt-4 border-top-delicate">
                            <div class="d-flex align-items-center mb-3">
                                <span class="perk-icon me-3">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="8" width="18" height="4" rx="1" ry="1" />
                                        <path d="M12 8v13" />
                                        <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7" />
                                        <path
                                            d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5" />
                                    </svg>
                                </span>
                                <span
                                    style="font-family: var(--f-body); font-size: 12px; color: var(--c-primary); font-weight: 500;">White
                                    Glove Delivery</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <span class="perk-icon me-3">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                        <polyline points="22 4 12 14.01 9 11.01" />
                                    </svg>
                                </span>
                                <span
                                    style="font-family: var(--f-body); font-size: 12px; color: var(--c-primary); font-weight: 500;">Eco-Certified
                                    Materials</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="perk-icon me-3">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    </svg>
                                </span>
                                <span
                                    style="font-family: var(--f-body); font-size: 12px; color: var(--c-primary); font-weight: 500;">Lifetime
                                    Service Warranty</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>



    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <section class="product-section py-5">
        <div
            class="container-fluid px-4 px-lg-5 position-relative mb-4 d-flex justify-content-center align-items-center">
            <h2 class="section-title m-0">
                @if($product->subcategory)
                    {{ $product->subcategory->subcat_name }}
                @else
                    Related Products
                @endif
            </h2>
            @if($product->subcategory)
            <a href="{{ route('subcategory.show', $product->subcategory->subcat_slug) }}" class="view-all-link position-absolute end-0 me-4 me-lg-5">View all</a>
            @endif
        </div>

        <div class="container-fluid px-4 px-lg-5">
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-4">

                @foreach($relatedProducts as $relProd)
                    @include('components.product-card', ['product' => $relProd])
                @endforeach

            </div>
        </div>
    </section>
    @endif




    <!-- Our Trusted Partners Section  -->
    @if($partners->count() > 0)
    <section class="trusted-clients-section py-5 mb-4">
        <div class="container-fluid px-4 px-lg-5">

            <h2 class="text-center font-marcellus mb-5" style="color: var(--text-main); font-size: 2.2rem;">
                Our Trusted Partners
            </h2>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 g-lg-4 justify-content-center">

                @foreach($partners as $partner)
                <div class="col">
                    <div class="client-logo-card">
                        <img src="{{ asset('storage/'.$partner->image) }}" alt="{{ $partner->title }}">
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </section>
    @endif




    <section>

        <img src="{{asset('images/why-buy-from-us/why-buy-from-us.png')}}" alt="" class="img-fluid w-100 py-4 px-4">

    </section>




@endsection


@section('footer_extras')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('pdpAddToCart');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            handleCartAction(this, 'add');
        });
    }

    const buyBtn = document.getElementById('pdpBuyNow');
    if (buyBtn) {
        buyBtn.addEventListener('click', function() {
            handleCartAction(this, 'buy');
        });
    }

    function handleCartAction(btn, type) {
        const productId = btn.dataset.productId;
        const quantity = parseInt(document.getElementById('pdpQuantity').value) || 1;

        // Get selected size if any
        const sizeSelect = document.getElementById('pdpSizeSelect');
        const sizeId = sizeSelect ? sizeSelect.value : null;

        // Get selected color if any
        const colorSelect = document.getElementById('pdpColorSelect');
        const colorId = colorSelect ? colorSelect.value : null;

        if (sizeSelect && !sizeId) {
            if (typeof JodhaCart !== 'undefined' && JodhaCart.showToast) {
                JodhaCart.showToast('Please select a size', 'error');
            } else {
                alert('Please select a size');
            }
            return;
        }

        if (colorSelect && !colorId) {
            if (typeof JodhaCart !== 'undefined' && JodhaCart.showToast) {
                JodhaCart.showToast('Please select a color', 'error');
            } else {
                alert('Please select a color');
            }
            return;
        }

        if (typeof JodhaCart !== 'undefined') {
            if (type === 'buy') {
                JodhaCart.buyNow(productId, quantity, sizeId || null, colorId || null);
            } else {
                JodhaCart.addToCart(productId, quantity, sizeId || null, colorId || null);
            }
        }
    }
});
</script>
@endsection