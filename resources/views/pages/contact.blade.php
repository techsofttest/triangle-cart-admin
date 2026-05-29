@extends('layouts.app')

@section('content')

    <!-- hero section -->
    <section class="collection-showcase">
        <div class="collection-banner position-relative" style="height: 60vh;">
            <img src="{{asset('images/banner/living-room3.jpg')}}" alt="Contact Jodha"
                class="banner-img w-100 h-100 object-fit-cover" style="filter: brightness(0.7);">

            <div class="banner-content position-absolute top-50 start-50 translate-middle text-center w-100">
                <span class="text-white text-uppercase letter-spacing-2 small mb-2 d-block">We are here to help</span>
                <h1 class="display-4 text-white font-heading fw-light">Get in Touch</h1>
                <div class="divider bg-white mx-auto mt-3" style="width: 60px; height: 2px;"></div>
            </div>
        </div>
    </section>






    <section class="contact-info-section section-spacing pt-5 pb-5">
        <div class="container">

            <div class="text-center mb-5 pb-lg-5">
                <span class="text-gold text-uppercase letter-spacing-3 d-block mb-3"
                    style="font-size: 10px; font-weight: 600;">At Your Service</span>
                <h2 class="font-heading display-5 mb-0 fw-light" style="color: var(--c-primary);">Reach Out to Jodha Furniture
                </h2>
            </div>

            <div class="row g-4 g-lg-5 justify-content-center">

                <div class="col-md-4">
                    <div class="ultra-luxury-card h-100 p-5 bg-white d-flex flex-column text-center position-relative">

                        <div
                            class="icon-circle mx-auto mb-4 d-flex align-items-center justify-content-center transition-all text-gold">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>

                        <h6 class="font-heading text-uppercase letter-spacing-2 mb-3"
                            style="font-size: 13px; color: var(--c-primary);">Address</h6>

                        <div class="admin-safe-content flex-grow-1"
                            style="font-family: var(--f-body); font-size: 13px; line-height: 2; color: #777;">
                            Jodha Furniture<br>
                            Ashborn Collective Pvt Ltd<br>
                            8th Cross Road Opp. French Toast<br>
                            Panampilly Nagar - 682036<br>
                        </div>

                        <div class="mt-4 pt-4 position-relative cta-wrapper">
                            <a target="_blank" href="https://www.google.com/maps/place/Jodha+furniture/@9.961038,76.2937277,17z/data=!3m1!4b1!4m6!3m5!1s0x3b08739d309be081:0xed6c313c04a4fb64!8m2!3d9.961038!4d76.2937277!16s%2Fg%2F11ymjzsfw6!18m1!1e1?entry=ttu&g_ep=EgoyMDI2MDUxMy4wIKXMDSoASAFQAw%3D%3D"
                                class="text-dark text-decoration-none text-uppercase letter-spacing-2 luxury-link"
                                style="font-size: 10px; font-weight: 500;">Get Directions</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="ultra-luxury-card h-100 p-5 bg-white d-flex flex-column text-center position-relative">

                        <div
                            class="icon-circle mx-auto mb-4 d-flex align-items-center justify-content-center transition-all text-gold">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg>
                        </div>

                        <h6 class="font-heading text-uppercase letter-spacing-2 mb-3"
                            style="font-size: 13px; color: var(--c-primary);">Direct Line</h6>

                        <div class="admin-safe-content flex-grow-1 d-flex flex-column"
                            style="font-family: var(--f-body); font-size: 13px; line-height: 2; color: #777;">
                            <a href="tel:+919895599002" class="text-dark text-decoration-none hover-gold transition-all"
                                style="font-size: 14px;">+91 9895599002</a>
                            <a href="tel:+919847300077" class="text-dark text-decoration-none hover-gold transition-all"
                                style="font-size: 14px;">+919847300077</a>
                            <span style="font-size: 11px; opacity: 0.8;">10:30 Am - 7:00 Pm<br>Sun - Mon
                            </span>
                        </div>

                        <div class="mt-4 pt-4 position-relative cta-wrapper">
                            <a href="tel:+919895599002"
                                class="text-dark text-decoration-none text-uppercase letter-spacing-2 luxury-link"
                                style="font-size: 10px; font-weight: 500;">Call Now</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="ultra-luxury-card h-100 p-5 bg-white d-flex flex-column text-center position-relative">

                        <div
                            class="icon-circle mx-auto mb-4 d-flex align-items-center justify-content-center transition-all text-gold">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                        </div>

                        <h6 class="font-heading text-uppercase letter-spacing-2 mb-3"
                            style="font-size: 13px; color: var(--c-primary);">Email Us</h6>

                        <div class="admin-safe-content flex-grow-1"
                            style="font-family: var(--f-body); font-size: 13px; line-height: 2; color: #777;">
                            <a href="mailto:jodhahomesmarketing@gmail.com"
                                class="text-dark text-decoration-none hover-gold transition-all">jodhahomesmarketing@gmail.com</a>
                        </div>

                        <div class="mt-4 pt-4 position-relative cta-wrapper">
                            <a href="mailto:bhavana@jodhafurniture.com"
                                class="text-dark text-decoration-none text-uppercase letter-spacing-2 luxury-link"
                                style="font-size: 10px; font-weight: 500;">Drop a Note</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>






    <!-- Contact Form Section -->
    <section class="contact-form-section contact-parallax-bg"
        style="background-image: url('{{asset('images/banner/living-room2.jpg')}}');">
        <div class="parallax-overlay"></div>

        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="bg-white p-4 p-md-5 position-relative form-card-shadow">

                        <div class="position-absolute top-0 start-0 w-100"
                            style="height: 4px; background-color: var(--c-gold);"></div>

                        <div class="text-center mb-5 mt-3">
                            <span class="text-gold text-uppercase letter-spacing-2 text-small d-block mb-2"
                                style="font-size: 11px; font-weight: 600;">Personal Assistance</span>
                            <h2 class="font-heading mb-3 display-6" style="color: var(--c-primary);">Drop Us A Line</h2>
                            <p class="text-muted small mx-auto" style="max-width: 400px; line-height: 1.8;">
                                Our dedicated concierge team will review your inquiry and respond within 24 hours.
                            </p>
                        </div>

                        <form class="luxury-form text-start px-lg-3">
                            <div class="row g-5">

                                <div class="col-md-6">
                                    <div class="position-relative luxury-input-group">
                                        <input type="text" class="luxury-input w-100" id="fullName" placeholder=" "
                                            required>
                                        <label for="fullName">Full Name</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="position-relative luxury-input-group">
                                        <input type="email" class="luxury-input w-100" id="emailAddress" placeholder=" "
                                            required>
                                        <label for="emailAddress">Email Address</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="position-relative luxury-input-group">
                                        <input type="tel" class="luxury-input w-100" id="phoneNumber" placeholder=" ">
                                        <label for="phoneNumber">Telephone (Optional)</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="position-relative luxury-input-group">
                                        <textarea class="luxury-input w-100" id="message" placeholder=" "
                                            style="height: 120px; resize: none;" required></textarea>
                                        <label for="message">Your Message</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4 pt-2">
                                    <div class="form-check luxury-checkbox d-flex align-items-center gap-3">
                                        <input class="form-check-input mt-0 shadow-none" type="checkbox"
                                            id="privacyPolicy" required style="width: 16px; height: 16px;">
                                        <label class="form-check-label text-muted" for="privacyPolicy"
                                            style="font-size: 11px; letter-spacing: 0.5px;">
                                            I accept the <a href="#"
                                                class="text-dark position-relative hover-underline">Privacy Policy</a>
                                            and <a href="#" class="text-dark position-relative hover-underline">Terms of
                                                Service</a>.
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-5 text-center">
                                    <button type="submit"
                                        class="btn btn-dark rounded-0 px-5 py-3 w-100 text-uppercase letter-spacing-2 d-flex justify-content-center align-items-center gap-3 transition-all luxury-submit-btn"
                                        style="font-size: 12px; font-weight: 500;">
                                        Send Message
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection