@extends('layouts.app')

@section('content')


    <!-- Products Grid -->
    <section class="products-grid-section py-5">
        <div class="container pb-5">

            <div class="text-center py-5">
                    <h5 class="font-marcellus text-muted"></h5>
                    <a href="{{ route('home') }}" class="btn-luxury-outline mt-3 px-4 py-2 text-decoration-none">Back to Home</a>
            </div>
            
            @if($products->isEmpty())
                <div class="text-center py-5">
                    <h5 class="font-marcellus text-muted">No products found for the term</h5>
                    <a href="{{ route('collections.index') }}" class="btn-luxury-outline mt-3 px-4 py-2 text-decoration-none">Back to Collections</a>
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