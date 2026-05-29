@extends('layouts.app')


@section('content')

  <!-- Products listing -->
    <section class="product-listing-section py-3" style="background-color: var(--c-white);">
        <div class="container">

            <div class="shop-toolbar d-flex justify-content-between align-items-center mb-5 pb-3 pt-3 border-bottom-luxury sticky-top"
                style="top: 0; z-index: 100; background-color: var(--c-white); width: 100%;">

                <div class="d-flex align-items-center gap-2 gap-md-4">
                    <button
                        class="btn btn-link text-decoration-none shadow-none d-flex align-items-center text-uppercase"
                        type="button" data-bs-toggle="offcanvas" data-bs-target="#shopFilterOffcanvas"
                        aria-controls="shopFilterOffcanvas"
                        style="font-size: 11px; letter-spacing: 1px; color: var(--c-primary); font-weight: 600; padding: 8px 12px; border: 1px solid #0000002d;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        Filters
                    </button>
                </div>


                <div class="d-flex align-items-center gap-3">

                    <!-- Grid Switcher -->
                    <div class="grid-switcher d-flex align-items-center gap-2 me-lg-4"
                        style="font-family: var(--f-body); letter-spacing: 1px;">

                        <button class="btn grid-btn active" data-layout="standard" title="Standard Grid">
                            <i class="fa-solid fa-grip"></i>
                        </button>

                        <button class="btn grid-btn" data-layout="large" title="Large Grid">
                            <i class="fa-regular fa-square"></i>
                        </button>

                        <button class="btn grid-btn" data-layout="list" title="List View">
                            <i class="fa-solid fa-list"></i>
                        </button>

                    </div>

                    <div class="d-none d-md-flex align-items-center gap-3">
                        <span class="text-uppercase"
                            style="font-size: 11px; letter-spacing: 1px; color: var(--c-primary); font-weight: 600;">Sort
                            By:</span>

                        <div class="dropdown luxury-dropdown">
                            <button
                                class="btn btn-link text-decoration-none p-0 shadow-none d-flex align-items-center justify-content-between"
                                type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="selected-text">Best Selling</span>
                                <i class="fa-solid fa-chevron-down ms-3"
                                    style="font-size: 10px; color: var(--c-gold);"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-0 mt-2"
                                aria-labelledby="sortDropdown">
                                <li><a class="dropdown-item active" href="#" data-value="best-selling">Best Selling</a>
                                </li>
                                <li><a class="dropdown-item" href="#" data-value="a-z">Alphabetically, A-Z</a></li>
                                <li><a class="dropdown-item" href="#" data-value="z-a">Alphabetically, Z-A</a></li>
                                <li><a class="dropdown-item" href="#" data-value="low-high">Price, Low to High</a></li>
                                <li><a class="dropdown-item" href="#" data-value="high-low">Price, High to Low</a></li>
                            </ul>
                        </div>
                        <!-- Grid Switcher -->
                        <!-- <div class="grid-switcher d-none d-lg-flex align-items-center gap-2 me-4">
                            <button class="btn grid-btn" data-cols="6" title="2 Columns">
                                <i class="fa-solid fa-table-columns"></i>
                            </button>
                            <button class="btn grid-btn active" data-cols="4" title="3 Columns">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <button class="btn grid-btn" data-cols="12" title="List View">
                                <i class="fa-solid fa-list"></i>
                            </button>
                        </div> -->



                    </div>
                </div>
            </div>


            <div class="container-fluid px-4 px-lg-5 py-3 mb-4">
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-4 justify-content-center">

                    @foreach($collections as $collection)
                    <div class="col">
                        <a href="{{ route('collections.show', $collection->col_slug) }}" class="collection-link">
                            <div class="collection-img-wrapper">
                                <img src="{{ asset('storage/' . $collection->col_image) }}" alt="{{ $collection->col_name }}">
                            </div>
                            <h3 class="collection-title">{{ $collection->col_name }}</h3>
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>

            <!-- Product Listing -->
            <div class="row g-5">
                <div class="col-lg-12">

                    <div class="row row-cols-2 row-cols-md-4 g-4" id="product-grid">

                     @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach

                    </div>

                    <div id="pagination-container" class="d-flex justify-content-center mt-5 pt-4">
                        {{ $products->links('vendor.pagination.luxury') }}
                    </div>

                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    const gridBtns = document.querySelectorAll('.grid-btn');
                    const productGrid = document.getElementById('product-grid');
                    const productItems = document.querySelectorAll('#product-grid .product-item');

                    // Layout switching
                    if (gridBtns.length > 0 && productGrid) {
                        gridBtns.forEach(btn => {
                            btn.addEventListener('click', function (e) {
                                e.preventDefault();
                                gridBtns.forEach(b => b.classList.remove('active'));
                                this.classList.add('active');

                                const layout = this.getAttribute('data-layout');
                                productGrid.classList.remove('list-view');

                                productItems.forEach(item => {
                                    item.classList.remove('col-12', 'col-6', 'col-4', 'col-lg-12', 'col-lg-6', 'col-lg-4');

                                    if (layout === 'list') {
                                        item.classList.add('col-12', 'col-lg-12');
                                        productGrid.classList.add('list-view');
                                    }
                                    else if (layout === 'large') {
                                        item.classList.add('col-12', 'col-lg-6');
                                    }
                                    else if (layout === 'standard') {
                                        item.classList.add('col-6', 'col-lg-4');
                                    }
                                });
                            });
                        });
                    }

                    // AJAX Filtering
                    const categoryFilters = document.querySelectorAll('.category-filter');
                    const sortItems = document.querySelectorAll('.luxury-dropdown .dropdown-item');
                    const priceMinInput = document.getElementById('price-min');
                    const priceMaxInput = document.getElementById('price-max');
                    const clearFiltersBtn = document.getElementById('clear-filters-btn');
                    const activeFiltersSection = document.getElementById('active-filters-section');
                    const activeFiltersList = document.getElementById('active-filters-list');

                    let currentSort = 'best-selling';

                    function updateActiveFiltersUI() {
                        activeFiltersList.innerHTML = '';
                        let count = 0;

                        // Categories
                        categoryFilters.forEach(f => {
                            if (f.checked) {
                                const name = f.nextElementSibling.nextElementSibling.querySelector('span').innerText;
                                activeFiltersList.innerHTML += `
                                    <span class="badge rounded-0 d-flex align-items-center py-2 px-3" style="background-color: var(--c-linen); color: var(--c-primary); font-family: var(--f-body); font-size: 11px; font-weight: 500; border: 1px solid rgba(0,0,0,0.08);">
                                        ${name}
                                        <button type="button" class="btn-close ms-2 remove-filter" data-id="${f.value}" style="font-size: 8px; filter: grayscale(1);"></button>
                                    </span>`;
                                count++;
                            }
                        });

                        // Price (maybe only if not default range)
                        const maxRange = {{ $max_range }};
                        if(parseInt(priceMinInput.value) > 0 || parseInt(priceMaxInput.value) < maxRange) {
                            activeFiltersList.innerHTML += `
                                <span class="badge rounded-0 d-flex align-items-center py-2 px-3" style="background-color: var(--c-linen); color: var(--c-primary); font-family: var(--f-body); font-size: 11px; font-weight: 500; border: 1px solid rgba(0,0,0,0.08);">
                                    ₹${priceMinInput.value} - ₹${priceMaxInput.value}
                                </span>`;
                             count++;
                        }

                        if(count > 0) activeFiltersSection.classList.remove('d-none');
                        else activeFiltersSection.classList.add('d-none');

                        // Attach listeners to remove buttons
                        document.querySelectorAll('.remove-filter').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const f = Array.from(categoryFilters).find(i => i.value === this.getAttribute('data-id'));
                                if(f) {
                                    f.checked = false;
                                    fetchProducts();
                                }
                            });
                        });
                    }

                    function fetchProducts(page = 1) {
                        const selectedCategories = Array.from(categoryFilters)
                            .filter(i => i.checked)
                            .map(i => i.value)
                            .join(',');

                        const minPrice = priceMinInput.value;
                        const maxPrice = priceMaxInput.value;

                        const url = new URL(window.location.href);
                        url.searchParams.set('page', page);
                        if (selectedCategories) url.searchParams.set('categories', selectedCategories);
                        else url.searchParams.delete('categories');
                        
                        url.searchParams.set('min_price', minPrice);
                        url.searchParams.set('max_price', maxPrice);
                        url.searchParams.set('sort', currentSort);

                        window.history.pushState({}, '', url);
                        productGrid.style.opacity = '0.5';

                        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.json())
                        .then(data => {
                            productGrid.innerHTML = data.html;
                            document.getElementById('pagination-container').innerHTML = data.pagination;
                            productGrid.style.opacity = '1';
                            
                            // Re-initialize lazy loading for new products
                            if (typeof initLazyLoad === 'function') initLazyLoad();
                            if (typeof equalizeHeights === 'function') equalizeHeights('.product-card');

                            attachPaginationLinks();
                            updateActiveFiltersUI();
                        });
                    }

                    function attachPaginationLinks() {
                        document.querySelectorAll('#pagination-container a').forEach(link => {
                            link.addEventListener('click', function(e) {
                                e.preventDefault();
                                const url = new URL(this.href);
                                fetchProducts(url.searchParams.get('page'));
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            });
                        });
                    }

                    categoryFilters.forEach(f => f.addEventListener('change', () => fetchProducts()));

                    sortItems.forEach(item => {
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            sortItems.forEach(i => i.classList.remove('active'));
                            this.classList.add('active');
                            currentSort = this.getAttribute('data-value');
                            document.querySelectorAll('#sortDropdown .selected-text').forEach(t => t.innerText = this.innerText);
                            fetchProducts();
                        });
                    });

                    // Price Slider logic 
                    const rangeLeft = document.getElementById('input-left');
                    const rangeRight = document.getElementById('input-right');
                    const trackFill = document.getElementById('track-fill');

                    function setPriceRange() {
                        const min = parseInt(rangeLeft.value);
                        const max = parseInt(rangeRight.value);
                        
                        if (min > max) { rangeLeft.value = max; }
                        if (max < min) { rangeRight.value = min; }

                        priceMinInput.value = rangeLeft.value;
                        priceMaxInput.value = rangeRight.value;

                        const percent1 = (rangeLeft.value / rangeLeft.max) * 100;
                        const percent2 = (rangeRight.value / rangeRight.max) * 100;
                        trackFill.style.left = percent1 + "%";
                        trackFill.style.width = (percent2 - percent1) + "%";
                    }

                    if(rangeLeft && rangeRight) {
                        [rangeLeft, rangeRight].forEach(r => {
                            r.addEventListener('input', setPriceRange);
                            r.addEventListener('change', () => fetchProducts());
                        });
                        setPriceRange(); // Init
                    }

                    if(clearFiltersBtn) {
                        clearFiltersBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            categoryFilters.forEach(f => f.checked = false);
                            rangeLeft.value = 0;
                            rangeRight.value = {{ $max_range }};
                            setPriceRange();
                            fetchProducts();
                        });
                    }

                    attachPaginationLinks();
                });
            </script>
        </div>
    </section>






     <!-- Filter Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="shopFilterOffcanvas"
        aria-labelledby="shopFilterOffcanvasLabel"
        style="width: 350px; border-right: none; box-shadow: 2px 0 15px rgba(0,0,0,0.05);">

        <div class="offcanvas-header border-bottom"
            style="border-color: rgba(0,0,0,0.08) !important; padding: 1.5rem 2rem;">
            <h5 class="offcanvas-title font-heading mb-0" id="shopFilterOffcanvasLabel"
                style="color: var(--c-primary); font-size: 20px;">Filters</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"
                style="filter: grayscale(1);"></button>
        </div>

        <div class="offcanvas-body custom-scrollbar" style="padding: 2rem;">

            <div class="active-filters-container mb-3 pb-4 border-bottom d-none" id="active-filters-section"
                style="border-color: rgba(0,0,0,0.08) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="filter-title mb-0" style="font-size: 14px;">Applied Filters</h6>
                    <a href="#" id="clear-filters-btn" class="text-muted text-decoration-none"
                        style="font-family: var(--f-body); font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Clear
                        All</a>
                </div>

                <div class="d-flex flex-wrap gap-2" id="active-filters-list">
                    {{-- Badges will be injected here --}}
                </div>
            </div>

            <!-- Sort By -->
            <div class="active-filters-container mb-3 pb-3 border-bottom"
                style="border-color: rgba(0,0,0,0.08) !important;">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-uppercase"
                        style="font-size: 11px; letter-spacing: 1px; color: var(--c-primary); font-weight: 600;">Sort
                        By:</span>

                    <div class="dropdown luxury-dropdown">
                        <button
                            class="btn btn-link text-decoration-none p-0 shadow-none d-flex align-items-center justify-content-between"
                            type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="selected-text">Best Selling</span>
                            <i class="fa-solid fa-chevron-down ms-3" style="font-size: 10px; color: var(--c-gold);"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-0 mt-2"
                            aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item active" href="#" data-value="best-selling">Best Selling</a></li>
                            <li><a class="dropdown-item" href="#" data-value="a-z">Alphabetically, A-Z</a></li>
                            <li><a class="dropdown-item" href="#" data-value="z-a">Alphabetically, Z-A</a></li>
                            <li><a class="dropdown-item" href="#" data-value="low-high">Price, Low to High</a></li>
                            <li><a class="dropdown-item" href="#" data-value="high-low">Price, High to Low</a></li>
                        </ul>
                    </div>
                </div>
            </div>





            <div class="filter-group mb-5">
                <h6 class="filter-title">Category</h6>
                <ul class="list-unstyled luxury-checkbox-list mt-3">
                    @foreach($categories as $category)
                    <li>
                        <label class="luxury-checkbox">
                            <input type="checkbox" class="category-filter" value="{{ $category->id }}">
                            <span class="checkmark"></span>
                            <span class="label-text d-flex justify-content-between w-100">
                                <span>{{ $category->name }}</span>
                                <span class="text-muted" style="font-size: 10px;">({{ $category->products_count }})</span>
                            </span>
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="filter-group mb-5">
                <h6 class="filter-title">Price Range</h6>

                <div class="price-slider-container mt-4">
                    <div class="multi-range-slider">
                        <input type="range" id="input-left" min="0" max="{{ $max_range }}" value="0">
                        <input type="range" id="input-right" min="0" max="{{ $max_range }}" value="{{ $max_range }}">
                        <div class="slider-track">
                            <div class="track-fill" id="track-fill"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="price-input-box">
                            <span class="currency">₹</span>
                            <input type="number" id="price-min" value="0" readonly>
                        </div>
                        <span class="mx-2 text-muted">-</span>
                        <div class="price-input-box">
                            <span class="currency">₹</span>
                            <input type="number" id="price-max" value="{{ $max_range }}" readonly>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>





@endsection



@section('footer_extra')



<script>
                document.addEventListener('DOMContentLoaded', function () {
                    const gridBtns = document.querySelectorAll('.grid-btn');
                    const productGrid = document.getElementById('product-grid');
                    const productItems = document.querySelectorAll('#product-grid .product-item');

                    if (gridBtns.length > 0 && productGrid) {
                        gridBtns.forEach(btn => {
                            btn.addEventListener('click', function (e) {
                                e.preventDefault();
                                gridBtns.forEach(b => b.classList.remove('active'));
                                this.classList.add('active');

                                const layout = this.getAttribute('data-layout');
                                productGrid.classList.remove('list-view');

                                productItems.forEach(item => {
                                    item.classList.remove('col-12', 'col-6', 'col-4', 'col-lg-12', 'col-lg-6', 'col-lg-4');

                                    if (layout === 'list') {
                                        item.classList.add('col-12', 'col-lg-12');
                                        productGrid.classList.add('list-view');
                                    }
                                    else if (layout === 'large') {
                                        item.classList.add('col-12', 'col-lg-6');
                                    }
                                    else if (layout === 'standard') {
                                        item.classList.add('col-6', 'col-lg-4');
                                    }
                                });
                            });
                        });
                    }
                });
            </script>




@endsection