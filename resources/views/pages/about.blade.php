@extends('layouts.app')

@section('content')



<!-- hero section -->
    <section class="about-hero position-relative vh-100 overflow-hidden d-flex align-items-center">

        <div class="hero-bg-fixed">
            <img src="{{ asset('storage/' . ($cms->image ?? '')) }}" class="w-100 h-100 object-fit-cover shadow-inset" alt="About Us">
            <div class="video-overlay"
                style="background: linear-gradient(to bottom, rgba(26, 21, 20, 0.4), rgba(26, 21, 20, 0.7));"></div>
        </div>

        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center text-white">

                    <span class="text-gold text-uppercase letter-spacing-5 mb-4 d-block animate-reveal"
                        style="font-family: var(--f-body); font-weight: 500; font-size: 13px;">
                        Established 2023 [cite: 120]
                    </span>

                    <h1 class="display-2 font-heading mb-4 animate-reveal delay-1" style="line-height: 1.1;">
                        The Unparalleled <br> Guardian of Grandeur
                    </h1>

                    <div class="mx-auto border-top-gold pt-4 mt-2 animate-reveal delay-2" style="max-width: 600px;">
                        <p class="lead opacity-75 mb-0"
                            style="font-family: var(--f-body); font-weight: 300; letter-spacing: 0.5px;">
                            At Jodha, we believe that furniture should do more than just fill a room—it should transform
                            a space, tell a story, and elevate the atmosphere of a home.
                        </p>
                    </div>

                    <div class="scroll-indicator mt-5 pt-4 animate-bounce">
                        <svg width="24" height="40" viewBox="0 0 24 40" fill="none" stroke="var(--c-gold)"
                            stroke-width="1">
                            <rect x="1" y="1" width="22" height="38" rx="11" />
                            <path d="M12 8V16" stroke-linecap="round" />
                        </svg>
                    </div>

                </div>
            </div>
        </div>
    </section>



    <!-- origin-section -->
    <section class="origin-section py-10" style="background-color: var(--c-linen);">
        <div class="container">

            <div class="row align-items-center gx-lg-5">

                <div class="col-lg-12 mb-5 mb-lg-0">
                    <span class="text-gold text-uppercase letter-spacing-2 mb-3 d-block"
                        style="font-family: var(--f-body); font-weight: 600; font-size: 11px;">
                        Our Story
                    </span>
                    <h2 class="font-heading mb-4" style="color: var(--c-primary); font-size: 42px; line-height: 1.2;">
                        {!! $cms->title ?? '' !!}
                    </h2>
                    <div class="origin-text-wrapper pe-lg-4">
                        <div style="font-family: var(--f-body); font-size: 16px; line-height: 1.8; color: var(--c-body);">
                            {!! $cms->content ?? '' !!}
                        </div>
                    </div>
                </div>

                

        </div>
    </section>






    <!-- Trusted Partners -->
    <section class="partners-section ">
        <div class="container-fluid px-0">

            <div class="text-center mb-5">
                <span class="text-gold text-uppercase letter-spacing-3 text-small d-block mb-3">In Collaboration
                    With</span>
                <h2 class="font-heading" style="color: var(--c-primary); font-size: 2.5rem; font-weight: 400;">
                    Our Trusted Partners
                </h2>
            </div>

            <div class="partners-marquee-wrapper pt-4">
                <div class="partners-track">

                    <div class="partners-group">
                        @foreach($partners as $partner)
                        <div class="partner-logo">
                            <img src="{{ asset('storage/' . $partner->image) }}" alt="{{ $partner->title }}">
                        </div>
                        @endforeach
                    </div>

                    <div class="partners-group" aria-hidden="true">
                        @foreach($partners as $partner)
                        <div class="partner-logo">
                            <img src="{{ asset('storage/' . $partner->image) }}" alt="{{ $partner->title }}">
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>

        </div>
    </section>





    <!-- Featured In -->
    <section class="featured-in-section my-4">
        <div class="container">

            <div class="text-center mb-5">
                <span class="text-gold text-uppercase letter-spacing-3 text-small d-block mb-3">Featured In</span>
                <h2 class="font-heading" style="color: var(--c-primary); font-size: 2.5rem; font-weight: 400;">
                    Recognition That Matters
                </h2>
            </div>

            <div
                class="row row-cols-2 row-cols-md-4 g-5 mt-5 mb-5 align-items-center justify-content-center text-center">

                @foreach($recognitions as $item)
                <div class="col">
                    <div class="featured-logo">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </section>




    <style>


        /* Gallery Grid */
.gallery-grid {
    overflow: hidden;
}

.gallery-item {
    overflow: hidden;
}

.gallery-card {
    position: relative;
    height: 500px;
    overflow: hidden;
    cursor: pointer;
}

.gallery-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.gallery-card:hover .gallery-img {
    transform: scale(1.07);
}

/* Overlay */
.gallery-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(0, 0, 0, 0.55) 0%,
        rgba(0, 0, 0, 0.0) 50%
    );
    opacity: 0;
    transition: opacity 0.4s ease;
    display: flex;
    align-items: flex-end;
    padding: 2rem;
}

.gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-label {
    color: #fff;
    font-size: 0.85rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    border-bottom: 1px solid var(--c-gold, #c9a96e);
    padding-bottom: 4px;
}

/* Responsive */
@media (max-width: 767.98px) {
    .gallery-card {
        height: 320px;
    }
}

@media (min-width: 768px) and (max-width: 991.98px) {
    .gallery-card {
        height: 400px;
    }
}


.gallery-card {
    height: 500px;
    overflow: hidden;
}

.gallery-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

@media (max-width: 767.98px) {
    .gallery-card {
        height: 320px;
    }
}

@media (min-width: 768px) and (max-width: 991.98px) {
    .gallery-card {
        height: 400px;
    }
}


    </style>



    <!-- Trusted Partners -->
    <section class="partners-section py-1">
        <div class="container px-0">

            <div class="text-center mb-5">
                <span class="text-gold text-uppercase letter-spacing-3 text-small d-block mb-3">Gallery</span>
                <h2 class="font-heading" style="color: var(--c-primary); font-size: 2.5rem; font-weight: 400;">
                    A Glimpse Into Our Creations
                </h2>
            </div>


            <div class="row g-0 gallery-grid">


            <div class="col-12 px-2 my-2 col-md-4 gallery-item">
                <div class="gallery-card">
                    <img src="{{asset('images/about-us/jodha2.jpeg')}}" alt="Creation 1" class="gallery-img">
                </div>
            </div>

            <div class="col-12 px-2 my-2 col-md-4 gallery-item">
                <div class="gallery-card">
                    <img src="{{asset('images/about-us/jodha3.jpeg')}}" alt="Creation 2" class="gallery-img">
                </div>
            </div>

            <div class="col-12 px-2 my-2 col-md-4 gallery-item">
                <div class="gallery-card">
                    <img src="{{asset('images/about-us/jodha4.jpeg')}}" alt="Creation 3" class="gallery-img">
                </div>
            </div>


            <div class="col-12 px-2 my-2 col-md-4 gallery-item">
                <div class="gallery-card">
                    <img src="{{asset('images/about-us/jodha5.jpeg')}}" alt="Creation 3" class="gallery-img">
                </div>
            </div>


            <div class="col-12 px-2 my-2 col-md-4 gallery-item">
                <div class="gallery-card">
                    <img src="{{asset('images/about-us/jodha6.jpeg')}}" alt="Creation 3" class="gallery-img">
                </div>
            </div>


            <div class="col-12 px-2 my-2 col-md-4 gallery-item">
                <div class="gallery-card">
                    <img src="{{asset('images/about-us/jodha7.jpeg')}}" alt="Creation 3" class="gallery-img">
                </div>
            </div>


            </div>


        </div>
    </section>







    <!-- REVIEWS SECTION -->
    <section class="reviews-section py-5">
        <div class="container-fluid px-0">

            <div class="text-center mb-5">
                <h4 class="fw-normal mb-1 font-marcellus">Excellent</h4>
                <div class="google-stars mb-1">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                        class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p class="text-muted small mb-2 font-jost">Based on 24 Reviews</p>
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg" alt="Google"
                    width="80">
            </div>

            <div class="reviews-carousel-wrapper">
                <button class="review-control prev" id="prevBtn"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="review-control next" id="nextBtn"><i class="fa-solid fa-chevron-right"></i></button>

                <div class="reviews-grab-container" id="scrollContainer">
                    <div class="reviews-track" id="reviewTrack">
                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">A</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Ananya Sharma</h6>
                                        <span class="review-date">Feb 14, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">The Mother of Pearl tray is the center of attention in my
                                living room. The iridescence is even more beautiful in person than on the website.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">R</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Rajesh Iyer</h6>
                                        <span class="review-date">Jan 28, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Incredible attention to detail. I ordered a custom bedside
                                table and the bone inlay patterns are perfectly symmetrical. Worth every penny.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">M</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Meera Kapoor</h6>
                                        <span class="review-date">Mar 02, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Packaging was extremely secure. I was worried about the
                                mirror frame traveling so far, but it arrived in pristine condition. Highly recommend!
                            </p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">V</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Vikram Seth</h6>
                                        <span class="review-date">Feb 22, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">You can really feel the 'individually handmade' aspect of
                                these pieces. There’s a soul to this furniture that mass-produced items lack.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">P</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Priya Nair</h6>
                                        <span class="review-date">Jan 05, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Absolutely love the ethically sourced mission of Jodha. It
                                makes owning such a luxury item feel even better. The quality is world-class.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">A</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Arjun Das</h6>
                                        <span class="review-date">Dec 20, 2025</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">The custom floral pattern on my tissue box holder is a work
                                of art. The artisans at Jodha are truly masters of their craft.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">S</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Sneha Gupta</h6>
                                        <span class="review-date">Feb 09, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">A true statement piece! Every guest who comes over asks
                                about our new dining table. It has completely transformed our space.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">R</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Rohan Mehta</h6>
                                        <span class="review-date">Jan 15, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">The customer support was helpful throughout the design
                                process. They shared progress photos of my chest of drawers. Exceptional service.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">K</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Kavita Rao</h6>
                                        <span class="review-date">Mar 10, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Elegant finish and very sturdy. You can tell this isn't
                                just furniture; it’s an heirloom that will be in our family for generations.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">A</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Aditya Verma</h6>
                                        <span class="review-date">Dec 12, 2025</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">Fast shipping and the product matches the photos exactly.
                                Often bone inlay looks different in person, but Jodha's quality is consistent.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">N</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Neha Reddy</h6>
                                        <span class="review-date">Feb 28, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">I ordered the serving platters for a dinner party and they
                                were a huge hit. They add such a sophisticated touch to the table setting.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="avatar">S</div>
                                    <div>
                                        <h6 class="m-0 font-jost">Sanjay Pillai</h6>
                                        <span class="review-date">Jan 30, 2026</span>
                                    </div>
                                </div>
                                <img src="{{asset('images\icons\icons_google.svg')}}" alt="G" width="18">
                            </div>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                            <p class="review-text font-jost">The depth of the design in the bone inlay is remarkable.
                                It’s clear these are made by artisans who take immense pride in their heritage.</p>
                            <a href="#" class="read-more">Read More</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>






@endsection