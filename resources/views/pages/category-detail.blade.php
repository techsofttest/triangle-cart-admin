@extends('layouts.app')

@section('content')

    <!-- Title / Breadcrumb Section -->
    <section class="page-title-section text-center py-5" style="background-color: var(--c-linen);">
        <div class="container mt-4">
            <h1 class="font-heading mb-3 text-uppercase letter-spacing-2" style="color: var(--c-primary);">{{ $category->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0" style="font-family: var(--f-body); font-size: 13px;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('category.index') }}" class="text-decoration-none text-muted">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Category Description (Optional) -->
    @if($category->description)
    <section class="collection-desc-section pt-5 pb-2">
        <div class="container text-center">
            <p class="text-muted mx-auto" style="max-width: 800px; font-family: var(--f-body); font-size: 15px; line-height: 1.8;">
                {!! $category->description !!}
            </p>
        </div>
    </section>
    @endif

    <!-- Products Grid -->
    <section class="products-grid-section py-5">
        <div class="container pb-5">
            
            @if($products->isEmpty())
                <div class="text-center py-5">
                    <h5 class="font-marcellus text-muted">No products found in this category.</h5>
                    <a href="{{ route('category.index') }}" class="btn-luxury-outline mt-3 px-4 py-2 text-decoration-none">Back to Categories</a>
                </div>
            @else
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 gx-lg-5 pt-3">
                    @foreach($products as $product)
                        <div class="col">
                            @include('components.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

@endsection