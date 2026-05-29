@extends('layouts.app')

@section('content')
<section class="py-5 mt-5" style="background-color: var(--c-linen); min-height: 80vh;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="font-heading display-4 mb-3">Frequently Asked Questions</h1>
                    <p class="text-muted lead">Find some of the common questions asked by our community</p>
                </div>

                <div class="accordion accordion-flush shadow-sm rounded-0 overflow-hidden" id="faqAccordion">
                    @forelse($faqs as $faq)
                        <div class="accordion-item border-0 mb-3 shadow-none">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed font-heading fw-bold py-4 px-4 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $faq->id }}" aria-expanded="false">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="faq-{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body px-4 pb-4 pt-1 text-muted" style="background-color: #fff; line-height: 1.8;">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 bg-white rounded shadow-sm">
                            <i class="fa-solid fa-circle-question fa-3x text-light mb-3"></i>
                            <h5 class="text-muted">No FAQs available at the moment.</h5>
                        </div>
                    @endforelse
                </div>

                <div class="mt-5 text-center">
                    <p class="text-muted">Still have questions?</p>
                    <a href="{{ route('contact') }}" class="btn btn-luxury-outline px-4 py-2">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .font-heading { font-family: var(--f-head); }
    .accordion-button:not(.collapsed) {
        color: var(--c-gold);
        background-color: #fff !important;
        box-shadow: none;
    }
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,0.125);
    }
    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%239b804e'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    .btn-luxury-outline {
        border: 1px solid var(--c-gold);
        color: var(--c-gold);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 600;
        font-size: 11px;
        border-radius: 0;
        transition: all 0.3s ease;
    }
    .btn-luxury-outline:hover {
        background-color: var(--c-gold);
        color: #fff;
    }
</style>
@endsection
