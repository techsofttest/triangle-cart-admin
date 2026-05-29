@extends('layouts.app')

@section('content')

    <!-- Title / Breadcrumb Section -->
    <section class="page-title-section text-center py-5" style="background-color: var(--c-linen);">
        <div class="container mt-4">
            <h1 class="font-heading mb-3 text-uppercase letter-spacing-2" style="color: var(--c-primary);">{{ $material->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0" style="font-family: var(--f-body); font-size: 13px;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('product.index') }}" class="text-decoration-none text-muted">Products</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $material->name }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="products-grid-section py-5">
        <div class="container pb-5">
            @if($products->isEmpty())
                <div class="text-center py-5">
                    <h5 class="font-marcellus text-muted">No products found for this material.</h5>
                    <a href="{{ route('product.index') }}" class="btn-luxury-outline mt-3 px-4 py-2 text-decoration-none">Back to Products</a>
                </div>
            @else
                <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 gx-lg-5 pt-3">
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
