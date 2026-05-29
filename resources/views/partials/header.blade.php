    <!-- header -->
    <header>

    <style>

        .mega-menu .product-card
        {
            height:unset !important;
        }

        .mega-menu .product-img-wrapper
        {
            height:100px !important;
        }

    </style>


        <div class="top-bar d-flex justify-content-between align-items-center flex-wrap">

            <div class="search-container position-relative">
                <input type="text" id="headerSearchInput" class="search-input" placeholder="Search" autocomplete="off">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <div id="searchResults" class="search-results-dropdown shadow-sm d-none"></div>
            </div>

            <div class="logo-container">
                <a href="{{route('home')}}" class="brand-logo"><img src="{{asset('images\logo\brand-logo-nobg.png')}}" alt=""
                        style="width: 180px;"></a>
            </div>

            <div class="user-actions d-flex align-items-center gap-4">
                @auth('customer')
                <a href="{{ route('profile.dashboard') }}" class="d-none d-lg-flex">
                    <i class="fa-regular fa-user"></i> {{ explode(' ', Auth::guard('customer')->user()->name)[0] }}
                </a>
                <form action="/customer/logout" method="POST" class="d-none d-lg-block">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 text-dark text-decoration-none small">Logout</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="d-none d-lg-flex">
                    <i class="fa-regular fa-user"></i> Login
                </a>
                @endauth


                <!-- <a href="#" class="d-lg-none text-dark mobile-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a> -->

                <a href="#" id="headerCartLink" class="mobile-icon text-decoration-none">
                    <div class="cart-icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                            style="vertical-align: -3px;">
                            <circle cx="8" cy="21" r="1"></circle>
                            <circle cx="19" cy="21" r="1"></circle>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12">
                            </path>
                        </svg>
                        <span class="cart-badge" style="display: none;">0</span>
                    </div>
                    <span class="d-none d-lg-inline ms-1">Cart</span>
                </a>

                <a href="#mobileMenu" data-bs-toggle="offcanvas" role="button" aria-controls="mobileMenu"
                    class="d-lg-none text-dark mobile-icon text-decoration-none">
                    <i class="fa-solid fa-bars"></i>
                </a>
            </div>
        </div>

        <nav class="navbar navbar-expand-lg main-nav p-0">
            <div class="container-fluid justify-content-center position-relative">
                <ul class="navbar-nav d-flex flex-row">
                    
                    <li class="nav-item"><a class="nav-link" href="{{route('home')}}">Home</a></li>

                    @if($materials->count() > 0)
                        <li class="nav-item dropdown standard-dropdown">
                            <a class="nav-link" href="javascript:void(0);">Products <i
                                    class="fa-solid fa-angle-down"></i></a>

                            <ul class="dropdown-menu shadow-sm">
                                @foreach($materials as $material)
                                    <li><a class="dropdown-item" href="{{ route('product.material.show', $material->slug) }}">{{ $material->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{route('product.index')}}">Products</a></li>
                    @endif
                    
                    <li class="nav-item dropdown standard-dropdown">
                        <a class="nav-link active-link" href="javascript:void(0);">Furniture <i
                                class="fa-solid fa-angle-down"></i></a>

                        <ul class="dropdown-menu shadow-sm">
                            @foreach($non_featured_collections as $col)
                                <li><a class="dropdown-item" href="{{ route('collections.show', $col->col_slug) }}">{{ $col->col_name }}</a></li>
                            @endforeach
                        </ul>
                    </li>


                    @foreach($header_categories->take(4) as $category)
                       
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                            </li>
                      
                    @endforeach

                    <li class="nav-item"><a class="nav-link" target="_blank" href="https://wa.me/919895599002">Customization</a></li>
                
                </ul>
            </div>
        </nav>
    </header>




    <!-- offcanvas-header -->
    <div class="offcanvas offcanvas-start border-0" tabindex="-1" id="mobileMenu"
        style="background-color: #f8f6f2; width: 340px;">

        <div class="offcanvas-header align-items-center pt-4 pb-2 px-4">
            <img src="{{asset('images/logo/brand-logo-nobg.png')}}" alt="" style="width: 180px;">
            <button type="button" class="btn-close shadow-none p-0 m-0" data-bs-dismiss="offcanvas" aria-label="Close"
                style="filter: grayscale(1); opacity: 0.6; font-size: 14px;"></button>
        </div>

        <div class="offcanvas-body custom-scrollbar px-4 pt-3 pb-5">

            @auth('customer')
            <div class="d-flex align-items-center justify-content-between p-3 mb-4 text-decoration-none login-card-mobile"
                style="background-color: var(--c-linen);">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 38px; height: 38px; border: 1px solid rgba(0, 0, 0, 0.363); color: var(--c-gold);">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-black font-heading" style="font-size: 15px; letter-spacing: 0.5px;">{{ explode(' ', Auth::guard('customer')->user()->name)[0] }}</span>
                        <form action="/customer/logout" method="POST">
                            @csrf
                            <button type="submit" class="border-0 bg-transparent p-0" style="color: rgba(0,0,0,0.5); font-size: 11px; font-family: var(--f-body); letter-spacing: 0.5px; text-transform: uppercase;">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}"
                class="d-flex align-items-center justify-content-between p-3 mb-4 text-decoration-none login-card-mobile"
                style="background-color: var(--c-linen);">
                <div class="d-flex align-items-center gap-3">

                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 38px; height: 38px; border: 1px solid rgba(0, 0, 0, 0.363); color: var(--c-gold);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-gold)"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-black font-heading"
                            style="font-size: 15px; letter-spacing: 0.5px;">Login</span>
                        <span
                            style="color: rgba(0,0,0,0.5); font-size: 11px; font-family: var(--f-body); letter-spacing: 0.5px; text-transform: uppercase;">View
                            orders & wishlist</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-black" style="font-size: 12px; opacity: 0.5;"></i>
            </a>
            @endauth



            <div class="mobile-highlight-box p-3 mb-4 position-relative overflow-hidden"
                style="background-color: var(--c-linen); min-height: 380px;">

                <div class="nav-panel main-panel transition-transform w-100">
                    <a href="{{route('home')}}"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading"
                        style="font-size: 17px;">
                        Home <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>

                    @if($materials->count() > 0)
                        <a href="#"
                            class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading drilldown-trigger"
                            data-target="submenu-products-materials" style="font-size: 17px;">
                            Products <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                    @else
                        <a href="{{route('product.index')}}"
                            class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading"
                            style="font-size: 17px;">
                            Products <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                    @endif

                    @if($non_featured_collections->count() > 0)
                        <a href="#"
                            class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading drilldown-trigger"
                            data-target="submenu-furniture" style="font-size: 17px;">
                            Furniture <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                    @else
                        <a href="{{route('collections.index')}}"
                            class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading"
                            style="font-size: 17px;">
                            Furniture <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                    @endif

                    @foreach($header_categories as $category)
                        
                            <a href="{{ route('category.show', $category->slug) }}"
                                class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading"
                                style="font-size: 17px;">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}"
                                        class="rounded object-fit-cover" style="width: 40px; height: 40px;">
                                    {{ $category->name }}
                                </div>
                                <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                            </a>
                    @endforeach

                    <a target="_blank" href="https://wa.me/919895599002"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading"
                        style="font-size: 17px;">
                        Customization <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>
                </div>

                @if($materials->count() > 0)
                    <div class="nav-panel sub-panel transition-transform w-100 position-absolute top-0 start-0 p-3 h-100"
                        id="submenu-products-materials">

                        <a href="#"
                            class="d-flex align-items-center mb-4 text-dark text-decoration-none font-heading drilldown-back"
                            style="font-size: 17px;">
                            <i class="fa-solid fa-chevron-left me-3" style="font-size: 14px;"></i>
                            <span style="text-decoration: underline; text-underline-offset: 6px;">Products</span>
                        </a>

                        <a href="{{ route('product.index') }}"
                            class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                            style="font-size: 16px;">
                            All Products <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>

                        @foreach($materials as $material)
                            <a href="{{ route('product.material.show', $material->slug) }}"
                                class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                                style="font-size: 16px;">
                                {{ $material->name }} <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($non_featured_collections->count() > 0)
                    <div class="nav-panel sub-panel transition-transform w-100 position-absolute top-0 start-0 p-3 h-100"
                        id="submenu-furniture">
                        <a href="#"
                            class="d-flex align-items-center mb-4 text-dark text-decoration-none font-heading drilldown-back"
                            style="font-size: 17px;">
                            <i class="fa-solid fa-chevron-left me-3" style="font-size: 14px;"></i>
                            <span style="text-decoration: underline; text-underline-offset: 6px;">Furniture</span>
                        </a>

                        <a href="{{ route('collections.index') }}"
                            class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                            style="font-size: 16px;">
                            All Furniture <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                        
                        @foreach($non_featured_collections as $col)
                            <a href="{{ route('collections.show', $col->col_slug) }}"
                                class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                                style="font-size: 16px;">
                                {{ $col->col_name }} <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                @foreach($header_categories as $category)
                    @php
                        $categoryCollections = $category->subcategories->flatMap(function($subcat) {
                            return $subcat->collections;
                        })->sortBy('col_order');
                    @endphp
                    @if($categoryCollections->count() > 0)
                        <div class="nav-panel sub-panel transition-transform w-100 position-absolute top-0 start-0 p-3 h-100"
                            id="submenu-{{ $category->slug }}">

                            <a href="#"
                                class="d-flex align-items-center mb-4 text-dark text-decoration-none font-heading drilldown-back"
                                style="font-size: 17px;">
                                <i class="fa-solid fa-chevron-left me-3" style="font-size: 14px;"></i>
                                <span style="text-decoration: underline; text-underline-offset: 6px;">{{ $category->name }}</span>
                            </a>

                            <a href="{{ route('category.show', $category->slug) }}"
                                class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                                style="font-size: 16px;">
                                View all <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                            </a>
                            
                            @foreach($categoryCollections as $col)
                                <a href="{{ route('collections.show', $col->col_slug) }}"
                                    class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                                    style="font-size: 16px;">
                                    {{ $col->col_name }} <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mobile-nav-section pt-3 mb-4">
                <h6 class="font-heading mb-3" style="font-size: 15px; font-weight: 600;">Top Categories</h6>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    

                @foreach($header_categories as $category)
                
                <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Coffee
                            {{ $category->name }}</a></li>

                @endforeach
                
                </ul>
            </div>

            

            <div class="mobile-footer mt-5 pt-4 border-top" style="border-color: rgba(0,0,0,0.08) !important;">

                <h6 class="font-heading mb-4"
                    style="font-size: 13px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase;">Get in
                    touch</h6>

                <div class="d-flex flex-column gap-3 mb-5">
                    <a href="tel:+919746005500"
                        class="text-dark text-decoration-none font-heading d-flex align-items-center gap-3 touch-target-44">
                        <div class="d-flex align-items-center justify-content-center rounded-circle border border-dark-subtle transition-icon"
                            style="width: 44px; height: 44px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-gold)"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg>
                        </div>
                        <span style="font-size: 16px;">+91 97460 05500</span>
                    </a>

                    <a href="mailto:support@jodha.in"
                        class="text-dark text-decoration-none font-heading d-flex align-items-center gap-3 touch-target-44">
                        <div class="d-flex align-items-center justify-content-center rounded-circle border border-dark-subtle transition-icon"
                            style="width: 44px; height: 44px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-gold)"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                </path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <span style="font-size: 16px;">support@jodha.in</span>
                    </a>
                </div>

                <h6 class="font-heading mb-4"
                    style="font-size: 13px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase;">Follow
                    us</h6>
                <div class="d-flex gap-3 mb-5">
                    <a href="#"
                        class="d-flex align-items-center justify-content-center rounded-circle border border-dark-subtle text-dark transition-icon"
                        style="width: 44px; height: 44px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="#"
                        class="d-flex align-items-center justify-content-center rounded-circle border border-dark-subtle text-dark transition-icon"
                        style="width: 44px; height: 44px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                </div>

                <div class="d-flex flex-wrap gap-3 align-items-center" style="filter: grayscale(1); opacity: 0.4;">
                    <i class="fa-brands fa-cc-visa fs-3"></i>
                    <i class="fa-brands fa-cc-mastercard fs-3"></i>
                    <i class="fa-brands fa-cc-amex fs-3"></i>
                    <i class="fa-brands fa-cc-paypal fs-3"></i>
                    <i class="fa-brands fa-apple-pay fs-3"></i>
                    <i class="fa-brands fa-google-pay fs-3"></i>
                </div>

            </div>

        </div>
    </div>