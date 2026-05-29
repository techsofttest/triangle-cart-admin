@extends('layouts.app')

@section('content')
<section class="py-5 mt-5" style="background-color: var(--c-linen); min-height: 80vh;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h1 class="font-heading display-4 mb-3">Sitemap</h1>
                    <p class="text-muted lead">Explore all the corners of Jodha Furniture</p>
                </div>

                <div class="card border-0 shadow-sm p-4 p-lg-5" style="background-color: var(--c-white);">
                    
                    <!-- Categories Tree -->
                    <div class="mb-5">
                        <h3 class="font-heading mb-4 pb-2 border-bottom text-gold d-flex align-items-center">
                            <i class="fa-solid fa-layer-group me-3"></i> Categories
                        </h3>
                        <div class="row g-4">
                            @foreach($categories as $category)
                                <div class="col-md-6 col-lg-4">
                                    <div class="mb-4">
                                        <a href="{{ route('category.show', $category->slug) }}" class="fw-bold text-dark text-decoration-none h5 mb-3 d-block hover-gold transition-all">
                                            {{ $category->name }}
                                        </a>
                                        <ul class="list-unstyled ps-3 border-start" style="border-width: 2px !important;">
                                            @foreach($category->subcategories as $subcat)
                                                <li class="mb-2">
                                                    <a href="{{ route('subcategory.show', $subcat->slug) }}" class="text-muted text-decoration-none small hover-gold transition-all d-flex align-items-center">
                                                        <i class="fa-solid fa-chevron-right me-2" style="font-size: 8px; color: var(--c-gold);"></i>
                                                        {{ $subcat->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Collections -->
                    <div>
                        <h3 class="font-heading mb-4 pb-2 border-bottom text-gold d-flex align-items-center">
                            <i class="fa-solid fa-boxes-stacked me-3"></i> Collections
                        </h3>
                        <div class="row g-3">
                            @foreach($collections as $collection)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <a href="{{ route('collections.show', $collection->col_slug) }}" class="card p-3 text-center text-decoration-none hover-shadow transition-all h-100 d-flex align-items-center justify-content-center border-light">
                                        <span class="text-muted fw-500">{{ $collection->col_name }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .font-heading { font-family: var(--f-head); }
    .text-gold { color: var(--c-gold); }
    .hover-gold:hover { color: var(--c-gold) !important; transform: translateX(5px); }
    .transition-all { transition: all 0.3s ease; }
    .border-start { border-color: rgba(155, 128, 78, 0.2) !important; }
    .fw-500 { font-weight: 500; }
    .hover-shadow:hover { 
        box-shadow: 0 10px 20px rgba(0,0,0,0.05); 
        border-color: var(--c-gold) !important;
        transform: translateY(-3px);
    }
</style>
@endsection
