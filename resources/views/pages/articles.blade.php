@extends('layouts.app')

@section('head_metas')
    <title>The Jodha Journal | Royal Heritage Insights</title>
    <meta name="description" content="Explore the Jodha Journal for furniture care, interior design stories, and heritage insights.">
@endsection

@section('content')
    <!-- banner -->
    <section class="journal-hero py-5" style="background-color: var(--c-linen);">
        <div class="container text-center">
            <span class="text-gold text-uppercase letter-spacing-5 mb-3 d-block animate-reveal"
                style="font-size: 12px; font-weight: 700;">
                Insights & Inspiration
            </span>
            <h1 class="font-heading mb-4 animate-reveal delay-1" style="color: var(--c-primary); font-size: 52px;">The
                Jodha Furniture Journal</h1>
            <div class="mx-auto" style="max-width: 600px;">
                <p class="text-muted animate-reveal delay-2" style="font-family: var(--f-body); line-height: 1.8;">
                    A collection of stories, design guides, and heritage insights curated for the modern connoisseur.
                    Explore the intersection of tradition and contemporary luxury.
                </p>
            </div>
        </div>
    </section>

    <!-- blog list-inline -->
    <section class="blog-section  my-3 mb-4">
        <div class="container">

            <ul class="nav nav-pills d-flex justify-content-start justify-content-md-center flex-nowrap overflow-auto hide-scrollbar gap-4 mb-5 pb-4 px-3 px-md-0"
                id="journalTabs" role="tablist" style="border-bottom: 1px solid rgba(0,0,0,0.08);">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('article.index') }}" class="nav-link journal-cat-link {{ !request('category') ? 'active' : '' }} bg-transparent rounded-0 p-0 pb-2">
                        All Stories
                    </a>
                </li>
                @foreach ($categories as $category)
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('article.index', ['category' => $category->slug]) }}" 
                           class="nav-link journal-cat-link {{ request('category') == $category->slug ? 'active' : '' }} bg-transparent rounded-0 p-0 pb-2">
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="journalTabsContent">
                <div class="tab-pane fade show active" id="all-stories" role="tabpanel">
                    <div class="row g-5">
                        @forelse ($journals as $journal)
                            <div class="col-lg-4 col-md-6">
                                <article class="blog-card h-100">
                                    <div class="blog-img-wrapper overflow-hidden position-relative">
                                        <span
                                            class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">
                                            {{ $journal->date ? \Carbon\Carbon::parse($journal->date)->format('M d') : $journal->created_at->format('M d') }}
                                        </span>
                                        <a href="{{ route('article.show', $journal->slug) }}">
                                            <img src="{{ asset('storage/'.$journal->image) }}" alt="{{ $journal->title }}"
                                                class="blog-img w-100 object-fit-cover" style="height: 300px;">
                                        </a>
                                    </div>
                                    <div class="blog-content pt-4 pb-2">
                                        @if ($journal->category)
                                            <a href="{{ route('article.index', ['category' => $journal->category->slug]) }}"
                                                class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">
                                                {{ $journal->category->name }}
                                            </a>
                                        @else
                                            <span class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">
                                                {{ $journal->label ?? 'Journal' }}
                                            </span>
                                        @endif
                                        <h4 class="font-heading mt-2 mb-3">
                                            <a href="{{ route('article.show', $journal->slug) }}" class="text-dark text-decoration-none">
                                                {{ $journal->title }}
                                            </a>
                                        </h4>
                                        <p class="text-muted small mb-3">
                                            {{ Str::limit(strip_tags($journal->content), 120) }}
                                        </p>
                                        <a href="{{ route('article.show', $journal->slug) }}"
                                            class="btn-link text-dark fw-bold text-decoration-none small">Read Article <svg
                                                width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="var(--c-gold)" stroke-width="2" class="ms-1 align-middle">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                <polyline points="12 5 19 12 12 19"></polyline>
                                            </svg></a>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No articles found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="row mt-5 pt-5">
                <div class="col-12 d-flex justify-content-center">
                    {{ $journals->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </section>
@endsection