@extends('layouts.app')


@section('content')


  <section class="collection-section py-5">
        <div class="container-fluid px-4 px-lg-5">

            <h2 class="section-title text-center mb-5">Shop By Collections</h2>

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





@endsection