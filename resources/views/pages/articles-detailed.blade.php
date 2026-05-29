@extends('layouts.app')

@section('head_metas')
    <title>{{ $journal->meta_title ?? $journal->title }} | Jodha Journal</title>
    <meta name="description" content="{{ $journal->meta_description ?? Str::limit(strip_tags($journal->content), 160) }}">
    <meta name="keywords" content="{{ $journal->meta_keywords }}">
@endsection

@section('content')
    <!-- Article Hero -->
    <section class="article-hero position-relative d-flex align-items-center justify-content-center overflow-hidden"
        style="height: 70vh;">
        <div class="position-absolute top-0 start-0 w-100 h-100">
            <div class="overlay-dark position-absolute top-0 start-0 w-100 h-100"
                style="background: rgba(0,0,0,0.5); z-index: 1;"></div>
            <img src="{{ asset('storage/'.$journal->image) }}" class="w-100 h-100 object-fit-cover"
                alt="{{ $journal->title }}" style="transform: scale(1.05);">
        </div>

        <div class="container position-relative z-2 text-center text-white mt-5">
            @if ($journal->category)
                <a href="{{ route('article.index', ['category' => $journal->category->slug]) }}" 
                   class="text-gold text-uppercase letter-spacing-2 text-decoration-none d-block mb-3"
                   style="font-size: 11px; font-weight: 600;">
                    {{ $journal->category->name }}
                </a>
            @else
                <span class="text-gold text-uppercase letter-spacing-2 d-block mb-3"
                      style="font-size: 11px; font-weight: 600;">
                    {{ $journal->label ?? 'Journal' }}
                </span>
            @endif
            <div class="mx-auto" style="max-width: 800px;">
                <h1 class="display-4 font-heading mb-4" style="line-height: 1.1;">{{ $journal->title }}</h1>
            </div>
            <div class="d-flex align-items-center justify-content-center gap-3 mt-4 text-uppercase letter-spacing-1"
                style="font-family: var(--f-body); font-size: 10px; opacity: 0.9;">
                <span>By Jodha Editorial</span>
                <span style="width: 4px; height: 4px; background: var(--c-gold); border-radius: 50%;"></span>
                <span>{{ $journal->date ? \Carbon\Carbon::parse($journal->date)->format('F d, Y') : $journal->created_at->format('F d, Y') }}</span>
            </div>
        </div>
    </section>

    <!-- Article Content -->
    <section class="article-content-section py-5" style="background-color: var(--c-white);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <div class="article-body"
                        style="font-family: var(--f-body); font-size: 17px; line-height: 2; color: var(--c-body); font-weight: 300;">
                        {!! $journal->content !!}
                    </div>

                    <div class="article-footer d-flex flex-column flex-md-row justify-content-between align-items-center mt-5 pt-4 border-top-delicate">
                        <div class="tags mb-3 mb-md-0 d-flex gap-2">
                            <span class="text-uppercase text-muted letter-spacing-1"
                                style="font-size: 10px; font-weight: 600;">Share On:</span>
                        </div>

                        <div class="share-links d-flex align-items-center gap-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn-social-stroke"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn-social-stroke"><i class="fa-brands fa-twitter"></i></a>
                            <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn-social-stroke"><i class="fa-brands fa-pinterest-p"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles -->
    @if ($relatedJournals->count() > 0)
    <section class="blog-section section-spacing pt-5">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-gold text-uppercase letter-spacing-2 text-small">Articles & News</span>
                <h2 class="font-heading mt-2">Continue Reading</h2>
                <div class="divider bg-gold mx-auto mt-3" style="width: 40px; height: 3px;"></div>
            </div>

            <div class="row g-4">
                @foreach ($relatedJournals as $related)
                    <div class="col-lg-4 col-md-6">
                        <article class="blog-card h-100">
                            <div class="blog-img-wrapper overflow-hidden position-relative">
                                <span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">
                                    {{ $related->date ? \Carbon\Carbon::parse($related->date)->format('M d') : $related->created_at->format('M d') }}
                                </span>
                                <a href="{{ route('article.show', $related->slug) }}">
                                    <img src="{{ asset('storage/'.$related->image) }}" alt="{{ $related->title }}"
                                        class="blog-img w-100 object-fit-cover" style="height: 280px;">
                                </a>
                            </div>
                            <div class="blog-content pt-4 pb-2">
                                <a href="{{ route('article.index', ['category' => $related->category->slug ?? '']) }}"
                                    class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">
                                    {{ $related->category->name ?? $related->label ?? 'Journal' }}
                                </a>
                                <h4 class="font-heading mt-2 mb-3"><a href="{{ route('article.show', $related->slug) }}"
                                        class="text-dark text-decoration-none">{{ $related->title }}</a></h4>
                                <p class="text-muted small mb-3">
                                    {{ Str::limit(strip_tags($related->content), 100) }}
                                </p>
                                <a href="{{ route('article.show', $related->slug) }}" class="btn-link text-dark fw-bold text-decoration-none small">Read Article <i
                                        class="fa-solid fa-arrow-right ms-1 text-gold"></i></a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
            
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="{{ route('article.index') }}" class="btn btn-outline-dark rounded-0 px-5 py-3 letter-spacing-1">View All Journal</a>
                </div>
            </div>
        </div>
    </section>
    @endif
@endsection