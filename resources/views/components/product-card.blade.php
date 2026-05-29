

<div class="col product-item">

<a href="{{ route('product.show', $product->prod_slug) }}"
   class="product-card card-with-hover text-decoration-none">

    <div class="product-img-wrapper">

        <img 
            data-src="{{ asset('storage/'.$product->prod_image) }}"
            class="img-fluid w-100 lazy-image"
            alt="{{ $product->prod_name }}"
            onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';"
        >

        {{-- SALE badge --}}
        @if($product->offer_percentage > 0)
            <span class="grid-sale-badge">SALE</span>
        @endif

        <!--<div class="card-hover-actions">
            <button class="hover-action-btn" title="Quick View">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <button class="hover-action-btn" title="Add to Cart">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>-->

    </div>


    <div class="product-info">

        {{-- COLORS (optional) --}}
        @if(isset($product->colors) && $product->colors->count())

        <div class="color-swatches">

            @foreach($product->colors->take(4) as $color)
                <div class="swatch"
                     style="background-color: {{ $color->color_code }}">
                </div>
            @endforeach

            @if($product->colors->count() > 4)
                <span class="swatch-text">
                    +{{ $product->colors->count() - 4 }}
                </span>
            @endif

        </div>

        @endif


        <h3 class="grid-product-title font-marcellus">
            {{ $product->prod_name }}
        </h3>


        <div class="grid-price-row">

            {{-- SALE PRICE --}}
            @if(!empty($product->prod_sale_price) && $product->prod_price > $product->prod_sale_price)

                <span class="grid-current-price">
                    ₹{{ number_format($product->prod_sale_price,2) }}
                </span>

                <span class="grid-old-price">
                    ₹{{ number_format($product->prod_price,2) }}
                </span>

                @if($product->offer_percentage > 0)
                <span class="grid-discount">
                    Save {{ $product->offer_percentage }}%
                </span>
                @endif

            {{-- NORMAL PRICE (OR EQUAL) --}}
            @else

                <span class="grid-current-price">
                    ₹{{ number_format($product->prod_sale_price ?: $product->prod_price,2) }}
                </span>

            @endif

        </div>

    </div>

</a>

</div>

