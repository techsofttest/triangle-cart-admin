@extends('layouts.app')

@section('content')

    <!-- Title / Breadcrumb Section -->
    <section class="page-title-section text-center py-5" style="background-color: var(--c-linen);">
        <div class="container mt-4">
            <h1 class="font-heading mb-3 text-uppercase letter-spacing-2" style="color: var(--c-primary);">Shop By Category</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0" style="font-family: var(--f-body); font-size: 13px;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                </ol>
            </nav>
        </div>
    </section>

  <section class="collection-section py-5">
        <div class="container-fluid px-4 px-lg-5">

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-4 justify-content-center">

                @foreach($categories as $category)

                <div class="col">
                    <a href="{{ route('category.show',['slug' => $category->slug]) }}" class="collection-link text-decoration-none">

                        <div class="collection-img-wrapper" style="border-radius: 50%; overflow: hidden; aspect-ratio: 1/1; border: 1px solid rgba(0,0,0,0.05);">
                            <img src="{{ asset('storage/'.$category->image) }}"
                                alt="{{ $category->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <h3 class="collection-title text-center mt-3 font-marcellus text-dark" style="font-size: 16px;">
                            {{ $category->name }}
                        </h3>

                    </a>
                </div>

            @endforeach
            
            </div>
        </div>
    </section>

@endsection