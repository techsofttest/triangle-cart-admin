@extends('layouts.app')


@section('content')

      <section class="hero-section"> 
        <div id="mainHeroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">

            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{asset('images\banner\gridimages.webp')}}" class="d-block w-100 hero-img"
                        alt="Elegant Living Room Setup">
                </div>
                <div class="carousel-item">
                    <img src="{{asset('images\banner\living-room2.jpg')}}" class="d-block w-100 hero-img"
                        alt="Luxury Home Decor">
                </div>
                <div class="carousel-item">
                    <img src="{{asset('images\banner\living-room3.jpg')}}" class="d-block w-100 hero-img"
                        alt="Modern Furniture">
                </div>
            </div>

            <div class="carousel-custom-controls">
                <button class="carousel-control-btn" type="button" data-bs-target="#mainHeroCarousel"
                    data-bs-slide="prev">
                    <i class="fa-solid fa-angle-left"></i>
                </button>
                <button class="carousel-control-btn" type="button" data-bs-target="#mainHeroCarousel"
                    data-bs-slide="next">
                    <i class="fa-solid fa-angle-right"></i>
                </button>
            </div>

        </div>
    </section>









    @if(isset($home_collections[0]))
    <section class="product-section py-5">

        <div class="container-fluid px-4 px-lg-5 position-relative mb-4 d-flex justify-content-center align-items-center">
            <h2 class="section-title m-0">{{ $home_collections[0]->col_name }}</h2>
            <a href="{{ route('collections.show', $home_collections[0]->col_slug) }}" class="view-all-link position-absolute end-0 me-4 me-lg-5">View all</a>
        </div>

        <div class="container-fluid px-4 px-lg-5">
            <div class="row mobile-swipe-row row-cols-md-3 row-cols-xl-5 g-4 pb-3">



                @foreach($home_collections[0]->products as $product)

                @include('components.product-card',['product' => $product])

                @endforeach


            </div>
        </div>
    </section>
    @endif








    @if(isset($home_product))
    <section class="container-fluid product-detail-section py-5 px-5">
        <div class="row">
            <div class="col-md-6 product-gallery-container">
                <div class="product-gallery-inner sticky-top" style="top: 20px;">
                    <div class="row gx-2">
                        <div class="col-md-2 product-thumbnails d-none d-md-flex flex-column gap-2 pe-lg-3">
                            <div class="thumbnail-wrapper border p-1 rounded-1 active" data-slide-to="0">
                                <img data-src="{{ asset('storage/'.$home_product->prod_image) }}"
                                    class="img-fluid lazy-image" alt="{{ $home_product->prod_name }}">
                            </div>
                            @foreach($home_product->images as $index => $image)
                            <div class="thumbnail-wrapper border p-1 rounded-1" data-slide-to="{{ $index + 1 }}">
                                <img src="{{ asset('storage/'.$image->image_path) }}"
                                    class="img-fluid" alt="{{ $home_product->prod_name }}">
                            </div>
                            @endforeach
                        </div>
                        <div class="col-md-10 product-main-image">
                            <div id="homeProductCarousel" class="carousel slide" data-bs-ride="false">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="{{ asset('storage/'.$home_product->prod_image) }}"
                                            class="img-fluid rounded-1 w-100" alt="{{ $home_product->prod_name }}">
                                    </div>
                                    @foreach($home_product->images as $index => $image)
                                    <div class="carousel-item">
                                        <img src="{{ asset('storage/'.$image->image_path) }}"
                                            class="img-fluid rounded-1 w-100" alt="{{ $home_product->prod_name }}">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 product-info-container font-jost px-lg-5 mt-4 mt-md-0">
                <h1 class="product-title font-marcellus mb-2">{{ $home_product->prod_name }}</h1>
                <p class="brand-text text-muted mb-4">{{ $home_product->subcategory ? $home_product->subcategory->subcat_name : 'Jodha' }}</p>

                <div class="price-container mb-4">
                    @if($home_product->prod_sale_price && $home_product->prod_price > $home_product->prod_sale_price)
                        <span class="grid-current-price fs-4 fw-normal text-dark">₹{{ number_format($home_product->prod_sale_price, 2) }}</span>
                        <span class="grid-old-price text-muted fw-light ms-2 text-decoration-line-through">₹{{ number_format($home_product->prod_price, 2) }}</span>
                        @if($home_product->offer_percentage > 0)
                            <span class="grid-discount text-gold fw-light ms-2">Save {{ $home_product->offer_percentage }}%</span>
                        @endif
                    @else
                        <span class="grid-current-price fs-4 fw-normal text-dark">₹{{ number_format($home_product->prod_sale_price ?: $home_product->prod_price, 2) }}</span>
                    @endif
                </div>

                <div class="attribute-section mb-5">
                    @if($home_product->prod_sku_code)
                    <div class="attr-row mb-3 fw-light">
                        <span class="attr-label text-dark me-2">SKU :</span>
                        <span class="attr-value text-muted">{{ $home_product->prod_sku_code }}</span>
                    </div>
                    @endif
                    @if($home_product->material || $home_product->prod_material)
                    <div class="attr-row mb-3 fw-light">
                        <span class="attr-label text-dark me-2">Material :</span>
                        <div class="attr-value text-muted d-inline-block pdp-attr-content">
                            @if($home_product->material)
                                {{ $home_product->material->name }}
                            @else
                                {!! Str::limit(strip_tags($home_product->prod_material), 100) !!}
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                @if($home_product->colors->count() > 0)
                <div class="variant-section mb-4">
                    <p class="variant-label text-muted fw-light mb-2">Color</p>
                    <div class="variant-options d-flex gap-2 fw-light">
                        <select id="pdpColorSelect" class="form-select rounded-0 border text-muted px-3 py-2" style="width: auto; min-width: 200px; font-size: 14px;background:var(--bg-color);">
                            <option value="" disabled selected>Select Color</option>
                            @foreach($home_product->colors as $color)
                                <option value="{{ $color->id }}">{{ $color->color_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if($home_product->sizes->count() > 0)
                <div class="variant-section mb-5">
                    <p class="variant-label text-muted fw-light mb-2">Size</p>
                    <div class="variant-options d-flex gap-2 fw-light">
                        <select id="pdpSizeSelect" class="form-select rounded-0 border text-muted px-3 py-2" style="width: auto; min-width: 200px; font-size: 14px;background:var(--bg-color);">
                            <option value="" disabled selected>Select Size</option>
                            @foreach($home_product->sizes as $size)
                                <option value="{{ $size->id }}">{{ $size->size }} - ₹{{ number_format($size->offer_price ?? $size->price, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <div class="cta-buttons d-grid gap-3">
                    <button id="homeAddToCartBtn" data-product-id="{{ $home_product->id }}" class="btn btn-outline-dark add-to-cart-btn btn-lg rounded-0 fw-light text-uppercase">Add to cart</button>
                    <a href="{{ route('product.show', $home_product->prod_slug) }}" class="btn solid-gold-btn btn-lg buy-now-btn rounded-0 fw-light text-uppercase text-center text-decoration-none">View full details</a>
                </div>
            </div>
        </div>
    </section>
    @endif












     @if(isset($home_collections[1]))
    <section class="product-section py-5">

        <div
            class="container-fluid px-4 px-lg-5 position-relative mb-4 d-flex justify-content-center align-items-center">
            <h2 class="section-title m-0">{{$home_collections[1]->col_name}}</h2>
            <a href="{{ route('collections.show', $home_collections[1]->col_slug) }}" class="view-all-link position-absolute end-0 me-4 me-lg-5">View all</a>
        </div>

        <div class="container-fluid px-4 px-lg-5">
            <div class="row mobile-swipe-row row-cols-md-3 row-cols-xl-5 g-4 pb-3">

                @foreach($home_collections[1]->products as $product)

                @include('components.product-card',['product' => $product])

                @endforeach

            </div>
        </div>
    </section>

    @endif



    
    <section class="collection-section py-5">
        <div class="container-fluid px-4 px-lg-5">

            <h2 class="section-title text-center mb-5">Shop By Collection</h2>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-4 justify-content-center">

                @foreach($collections as $collection)

                <div class="col">
                    <a href="{{ route('collections.show',['slug' => $collection->col_slug]) }}" class="collection-link">

                        <div class="collection-img-wrapper">
                            <img src="{{ asset('storage/'.$collection->col_image) }}"
                                alt="{{ $collection->col_name }}">
                        </div>

                        <h3 class="collection-title">
                            {{ $collection->col_name }}
                        </h3>

                    </a>
                </div>

            @endforeach
            

            </div>
        </div>
    </section>
  



    

    @if(isset($home_collections[2]))

    <section class="product-section py-5">
        <div
            class="container-fluid px-4 px-lg-5 position-relative mb-4 d-flex justify-content-center align-items-center">
            <h2 class="section-title m-0">{{$home_collections[2]->col_name}}</h2>
            <a href="{{ route('collections.show', $home_collections[2]->col_slug) }}" class="view-all-link position-absolute end-0 me-4 me-lg-5">View all</a>
        </div>

        <div class="container-fluid px-4 px-lg-5">
            <div class="row mobile-swipe-row row-cols-md-3 row-cols-xl-5 g-4 pb-3">

                @foreach($home_collections[2]->products as $product)

                @include('components.product-card',['product' => $product])

                @endforeach
            
               

            </div>
        </div>
    </section>

    @endif




    @if(isset($home_collections[3]))
    <section class="product-section py-5">
        <div
            class="container-fluid px-4 px-lg-5 position-relative mb-4 d-flex justify-content-center align-items-center">
            <h2 class="section-title m-0">{{$home_collections[3]->col_name}}</h2>
            <a href="{{ route('collections.show', $home_collections[3]->col_slug) }}" class="view-all-link position-absolute end-0 me-4 me-lg-5">View all</a>
        </div>

        <div class="container-fluid px-4 px-lg-5">
            <div class="row mobile-swipe-row row-cols-md-3 row-cols-xl-5 g-4 pb-3">

                @foreach($home_collections[3]->products as $product)

                @include('components.product-card',['product' => $product])

                @endforeach

            </div>
        </div>
    </section>
    @endif









    <section class="values-section py-5">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row text-center gy-4">
                <div class="col-md-4 feature-col">
                    <h3 class="feature-title">100% Handcrafted</h3>
                    <p class="feature-text">Every piece is meticulously crafted by skilled artisans</p>
                </div>
                <div class="col-md-4 feature-col border-desktop-sides">
                    <h3 class="feature-title">Ethically Sourced</h3>
                    <p class="feature-text">We prioritize ethical sourcing and sustainable practices</p>
                </div>
                <div class="col-md-4 feature-col">
                    <h3 class="feature-title">Individually Handmade</h3>
                    <p class="feature-text">Handcrafted with care and precision, one piece at a time</p>
                </div>
            </div>
        </div>
    </section>







    @if(isset($home_collections[4]))
    <section class="product-section py-5">
        <div
            class="container-fluid px-4 px-lg-5 position-relative mb-4 d-flex justify-content-center align-items-center">
            <h2 class="section-title m-0">{{$home_collections[4]->col_name}}</h2>
            <a href="{{ route('collections.show', $home_collections[4]->col_slug) }}" class="view-all-link position-absolute end-0 me-4 me-lg-5">View all</a>
        </div>

        <div class="container-fluid px-4 px-lg-5">
            <div class="row mobile-swipe-row row-cols-md-3 row-cols-xl-5 g-4 pb-3">

                @foreach($home_collections[4]->products as $product)

                @include('components.product-card',['product' => $product])

                @endforeach


            </div>
        </div>
    </section>
    @endif





    <section class="reviews-section py-5">
        <div class="container-fluid px-0">

            <div class="text-center mb-5">
                <h4 class="fw-normal mb-1 font-marcellus">Excellent</h4>
                <div class="google-stars mb-1">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                        class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p class="text-muted small mb-2 font-jost">Based on 24 Reviews</p>
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg" alt="Google"
                    width="80">
            </div>

            <div class="reviews-carousel-wrapper">
                <button class="review-control prev" id="prevBtn"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="review-control next" id="nextBtn"><i class="fa-solid fa-chevron-right"></i></button>

                <div class="reviews-grab-container" id="scrollContainer">
                    <div class="reviews-track" id="reviewTrack">
                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">A</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Ananya Sharma</h6>
                                        <span class="review-date">Feb 14, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">The Mother of Pearl tray is the center of attention in my
                                living room. The iridescence is even more beautiful in person than on the website.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">R</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Rajesh Iyer</h6>
                                        <span class="review-date">Jan 28, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Incredible attention to detail. I ordered a custom bedside
                                table and the bone inlay patterns are perfectly symmetrical. Worth every penny.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">M</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Meera Kapoor</h6>
                                        <span class="review-date">Mar 02, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Packaging was extremely secure. I was worried about the
                                mirror frame traveling so far, but it arrived in pristine condition. Highly recommend!
                            </p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">V</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Vikram Seth</h6>
                                        <span class="review-date">Feb 22, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">You can really feel the 'individually handmade' aspect of
                                these pieces. There’s a soul to this furniture that mass-produced items lack.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">P</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Priya Nair</h6>
                                        <span class="review-date">Jan 05, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Absolutely love the ethically sourced mission of Jodha. It
                                makes owning such a luxury item feel even better. The quality is world-class.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">A</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Arjun Das</h6>
                                        <span class="review-date">Dec 20, 2025</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">The custom floral pattern on my tissue box holder is a work
                                of art. The artisans at Jodha are truly masters of their craft.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">S</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Sneha Gupta</h6>
                                        <span class="review-date">Feb 09, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">A true statement piece! Every guest who comes over asks
                                about our new dining table. It has completely transformed our space.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">R</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Rohan Mehta</h6>
                                        <span class="review-date">Jan 15, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">The customer support was helpful throughout the design
                                process. They shared progress photos of my chest of drawers. Exceptional service.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">K</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Kavita Rao</h6>
                                        <span class="review-date">Mar 10, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Elegant finish and very sturdy. You can tell this isn't
                                just furniture; it’s an heirloom that will be in our family for generations.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">A</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Aditya Verma</h6>
                                        <span class="review-date">Dec 12, 2025</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Fast shipping and the product matches the photos exactly.
                                Often bone inlay looks different in person, but Jodha's quality is consistent.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">N</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Neha Reddy</h6>
                                        <span class="review-date">Feb 28, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">I ordered the serving platters for a dinner party and they
                                were a huge hit. They add such a sophisticated touch to the table setting.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">S</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Sanjay Pillai</h6>
                                        <span class="review-date">Jan 30, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">The depth of the design in the bone inlay is remarkable.
                                It’s clear these are made by artisans who take immense pride in their heritage.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>










    <section class="newsletter-section py-5">
        <div class="container py-lg-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 text-center text-white">

                    <h2 class="newsletter-title mb-3 font-marcellus">Sign Up & Save</h2>

                    <p class="newsletter-text mb-4 font-jost">
                        Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals
                    </p>

                    <form id="newsletterForm" class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control newsletter-input font-jost"
                                placeholder="Enter your email" required>
                            <button class="btn newsletter-btn font-jost" type="submit">
                                Subscribe
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>


    @endsection

    @section('footer_extras')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Thumbnail switching
        const thumbWrappers = document.querySelectorAll('.thumbnail-wrapper');
        const heroCarousel = document.getElementById('homeProductCarousel');
        
        if (heroCarousel) {
            const bsCarousel = new bootstrap.Carousel(heroCarousel);
            
            thumbWrappers.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    const slideTo = this.getAttribute('data-slide-to');
                    bsCarousel.to(slideTo);
                    
                    // Update active class
                    thumbWrappers.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            heroCarousel.addEventListener('slid.bs.carousel', function (event) {
                const index = event.to;
                thumbWrappers.forEach(t => t.classList.remove('active'));
                const activeThumb = document.querySelector(`.thumbnail-wrapper[data-slide-to="${index}"]`);
                if (activeThumb) activeThumb.classList.add('active');
            });
        }

        // Add to cart
        const addBtn = document.getElementById('homeAddToCartBtn');
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const quantity = 1;

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
                    JodhaCart.addToCart(productId, quantity, sizeId || null, colorId || null);
                }
            });
        }

        // Newsletter subscription
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const emailInput = this.querySelector('input[type="email"]');
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerText;

                submitBtn.disabled = true;
                submitBtn.innerText = 'Subscribing...';

                fetch('{{ route("newsletter.subscribe") }}', {
                    method: 'POST',
                    body: JSON.stringify({ email: emailInput.value }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alertify.success(data.message);
                        emailInput.value = '';
                    } else {
                        alertify.error(data.message);
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalBtnText;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alertify.error('An error occurred. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalBtnText;
                });
            });
        }
    });
</script>
@endsection
