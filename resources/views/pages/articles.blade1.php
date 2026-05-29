<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JODHA FURNITURE | Royal Heritage</title>
    <link rel="icon" href="assets\images\logo\favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&family=Outfit:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets\css\style.css">
    <link rel="stylesheet" href="assets/css/stylenew.css">
</head>

<body>


    <!-- header -->
    <header>
        <div class="top-bar d-flex justify-content-between align-items-center flex-wrap">

            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
            </div>

            <div class="logo-container">
                <a href="#" class="brand-logo"><img src="assets\images\logo\brand-logo-nobg.png" alt=""
                        style="width: 150px;"></a>
            </div>

            <div class="user-actions d-flex align-items-center gap-4">
                <a href="#" class="d-none d-lg-flex">
                    <i class="fa-regular fa-user"></i> Account
                </a>

                <!-- <a href="#" class="d-lg-none text-dark mobile-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a> -->

                <a href="#" class="mobile-icon text-decoration-none">
                    <div class="cart-icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                            style="vertical-align: -3px;">
                            <circle cx="8" cy="21" r="1"></circle>
                            <circle cx="19" cy="21" r="1"></circle>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12">
                            </path>
                        </svg>
                        <span class="cart-badge">3</span>
                    </div>
                    <span class="d-none d-lg-inline ms-1">Cart</span>
                </a>

                <a href="#mobileMenu" data-bs-toggle="offcanvas" role="button" aria-controls="mobileMenu"
                    class="d-lg-none text-dark mobile-icon text-decoration-none">
                    <i class="fa-solid fa-bars"></i>
                </a>
            </div>
        </div>

        <nav class="navbar navbar-expand-lg main-nav p-0">
            <div class="container-fluid justify-content-center position-relative">
                <ul class="navbar-nav d-flex flex-row">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Furniture <i
                                class="fa-solid fa-angle-down"></i></a></li>

                    <li class="nav-item dropdown mega-dropdown">
                        <a class="nav-link active-link" href="#">Home Decor <i class="fa-solid fa-angle-down"></i></a>

                        <div class="dropdown-menu mega-menu w-100 shadow-sm">
                            <div class="container-fluid px-5">
                                <div class="row">
                                    <div class="col menu-col">
                                        <h6>Table Top Accessories</h6>
                                        <ul class="menu-list">
                                            <li><a href="#">Trays</a></li>
                                            <li><a href="#">Cake Stands</a></li>
                                            <li><a href="#">Coasters</a></li>
                                            <li><a href="#">Candle Holders</a></li>
                                            <li><a href="#">Table Lamps</a></li>
                                        </ul>
                                    </div>
                                    <div class="col menu-col">
                                        <h6>Organizers</h6>
                                        <ul class="menu-list">
                                            <li><a href="#">Tissue Boxes</a></li>
                                            <li><a href="#">Jewellery Boxes</a></li>
                                            <li><a href="#">Wine Holders</a></li>
                                            <li><a href="#">Bathroom Sets</a></li>
                                            <li><a href="#">Cutlery & Cutlery Holder</a></li>
                                        </ul>
                                    </div>
                                    <div class="col menu-col">
                                        <h6>Art & Decor</h6>
                                        <ul class="menu-list">
                                            <li><a href="#">Photo Frames</a></li>
                                            <li><a href="#">Gift Boxes</a></li>
                                            <li><a href="#">Wall Art</a></li>
                                            <li><a href="#">Planters</a></li>
                                            <li><a href="#">Vases</a></li>
                                            <li><a href="#">Cross</a></li>
                                        </ul>
                                    </div>
                                    <div class="col menu-col">
                                        <h6>Mirrors</h6>
                                        <ul class="menu-list">
                                            <li><a href="#">Wall Mirrors</a></li>
                                            <li><a href="#">Floor Mirrors</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-3">
                                        <div class="featured-product">
                                            <div class="product-img-placeholder"> <img
                                                    src="assets\images\furniturecategories\dining-chair.jpg" alt="">
                                            </div>
                                            <span class="sale-badge">SALE</span>
                                            <div class="product-details">
                                                <div class="product-title">Jodha Nature's Touch<br>Tissue Holder In
                                                    Mother<br>Of Pearl</div>
                                                <div class="price-row">
                                                    <span class="current-price">₹2,999.00</span>
                                                    <span class="old-price">₹8,999.00</span>
                                                </div>
                                                <div class="discount-text">Save 67%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item"><a class="nav-link" href="#">Gifting <i class="fa-solid fa-angle-down"></i></a>
                    </li>
                    <li class="nav-item dropdown standard-dropdown">
                        <a class="nav-link active-link" href="#">Shop By Material <i
                                class="fa-solid fa-angle-down"></i></a>

                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item" href="#">Mother Of Pearl</a></li>
                            <li><a class="dropdown-item" href="#">Bone Inlay</a></li>
                            <li><a class="dropdown-item" href="#">Wood</a></li>
                            <li><a class="dropdown-item" href="#">Brass</a></li>
                            <li><a class="dropdown-item" href="#">Imported</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#">Customization</a></li>
                </ul>
            </div>
        </nav>
    </header>




    <!-- offcanvas-header -->
    <div class="offcanvas offcanvas-start border-0" tabindex="-1" id="mobileMenu"
        style="background-color: #f8f6f2; width: 340px;">

        <div class="offcanvas-header align-items-center pt-4 pb-2 px-4">
            <img src="assets/images/logo/brand-logo-nobg.png" alt="" style="width: 150px;">
            <button type="button" class="btn-close shadow-none p-0 m-0" data-bs-dismiss="offcanvas" aria-label="Close"
                style="filter: grayscale(1); opacity: 0.6; font-size: 14px;"></button>
        </div>

        <div class="offcanvas-body custom-scrollbar px-4 pt-3 pb-5">

            <a href="login.html"
                class="d-flex align-items-center justify-content-between p-3 mb-4 text-decoration-none login-card-mobile"
                style="background-color: var(--c-linen);">
                <div class="d-flex align-items-center gap-3">

                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 38px; height: 38px; border: 1px solid rgba(0, 0, 0, 0.363); color: var(--c-gold);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-gold)"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-black font-heading"
                            style="font-size: 15px; letter-spacing: 0.5px;">Login</span>
                        <span
                            style="color: rgba(0,0,0,0.5); font-size: 11px; font-family: var(--f-body); letter-spacing: 0.5px; text-transform: uppercase;">View
                            orders & wishlist</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-black" style="font-size: 12px; opacity: 0.5;"></i>
            </a>


            <div class="mobile-highlight-box p-3 mb-4 position-relative overflow-hidden"
                style="background-color: var(--c-linen); min-height: 380px;">

                <div class="nav-panel main-panel transition-transform w-100">
                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading"
                        style="font-size: 17px;">
                        Home <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>

                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading drilldown-trigger"
                        data-target="submenu-furniture" style="font-size: 17px;">
                        <div class="d-flex align-items-center gap-3">
                            <img src="assets/images/banner/living-room.jpg" alt="Furniture"
                                class="rounded object-fit-cover" style="width: 40px; height: 40px;">
                            Furniture
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>

                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading drilldown-trigger"
                        data-target="submenu-decor" style="font-size: 17px;">
                        <div class="d-flex align-items-center gap-3">
                            <img src="assets/images/banner/living-room2.jpg" alt="Home Decor"
                                class="rounded object-fit-cover" style="width: 40px; height: 40px;">
                            Home Decor
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>

                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading"
                        style="font-size: 17px;">
                        <div class="d-flex align-items-center gap-3">
                            <img src="assets/images/furniturecategories/dining-chair.jpg" alt="Gifting"
                                class="rounded object-fit-cover" style="width: 40px; height: 40px;">
                            Gifting
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>

                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 mb-2 font-heading"
                        style="font-size: 17px;">
                        Shop By Material <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>

                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-2 font-heading"
                        style="font-size: 17px;">
                        Customization <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>
                </div>

                <div class="nav-panel sub-panel transition-transform w-100 position-absolute top-0 start-0 p-3 h-100"
                    id="submenu-furniture">

                    <a href="#"
                        class="d-flex align-items-center mb-4 text-dark text-decoration-none font-heading drilldown-back"
                        style="font-size: 17px;">
                        <i class="fa-solid fa-chevron-left me-3" style="font-size: 14px;"></i>
                        <span style="text-decoration: underline; text-underline-offset: 6px;">Furniture</span>
                    </a>

                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                        style="font-size: 16px;">
                        View all <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>
                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                        style="font-size: 16px;">
                        Tables <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>
                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                        style="font-size: 16px;">
                        Seating <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>
                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                        style="font-size: 16px;">
                        Storage <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>
                </div>

                <div class="nav-panel sub-panel transition-transform w-100 position-absolute top-0 start-0 p-3 h-100"
                    id="submenu-decor">
                    <a href="#"
                        class="d-flex align-items-center mb-4 text-dark text-decoration-none font-heading drilldown-back"
                        style="font-size: 17px;">
                        <i class="fa-solid fa-chevron-left me-3" style="font-size: 14px;"></i>
                        <span style="text-decoration: underline; text-underline-offset: 6px;">Home Decor</span>
                    </a>
                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                        style="font-size: 16px;">
                        View all <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>
                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                        style="font-size: 16px;">
                        Mirrors <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>
                    <a href="#"
                        class="d-flex justify-content-between align-items-center text-dark text-decoration-none py-3 font-heading"
                        style="font-size: 16px;">
                        Lighting <i class="fa-solid fa-chevron-right text-muted" style="font-size: 12px;"></i>
                    </a>
                </div>

            </div>

            <div class="mobile-nav-section pt-3 mb-4">
                <h6 class="font-heading mb-3" style="font-size: 15px; font-weight: 600;">Top Categories</h6>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Coffee
                            Tables</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Side
                            Tables</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading"
                            style="font-size: 16px;">Sideboards & Cabinets</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Chest
                            Of Drawer</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Bone
                            Inlay</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Tissue
                            Boxes</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading"
                            style="font-size: 16px;">Trays</a></li>
                </ul>
            </div>

            <div class="mobile-nav-section pt-3 mb-4">
                <h6 class="font-heading mb-3 mt-4" style="font-size: 15px; font-weight: 600;">About Us</h6>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li><a href="about-us.html" class="text-dark text-decoration-none font-heading"
                            style="font-size: 16px;">Our Story</a></li>
                    <li><a href="journal.html" class="text-dark text-decoration-none font-heading"
                            style="font-size: 16px;">Blog</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading"
                            style="font-size: 16px;">Sitemap</a></li>
                </ul>
            </div>

            <div class="mobile-nav-section pt-3 mb-5">
                <h6 class="font-heading mb-3 mt-4" style="font-size: 15px; font-weight: 600;">Help & Information</h6>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Privacy
                            Policy</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Return
                            Policy</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading"
                            style="font-size: 16px;">Shipping Policy</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Terms &
                            Conditions</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading" style="font-size: 16px;">Contact
                            Us</a></li>
                    <li><a href="#" class="text-dark text-decoration-none font-heading"
                            style="font-size: 16px;">FAQs</a></li>
                </ul>
            </div>

            <div class="mobile-footer mt-5 pt-4 border-top" style="border-color: rgba(0,0,0,0.08) !important;">

                <h6 class="font-heading mb-4"
                    style="font-size: 13px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase;">Get in
                    touch</h6>

                <div class="d-flex flex-column gap-3 mb-5">
                    <a href="tel:+919746005500"
                        class="text-dark text-decoration-none font-heading d-flex align-items-center gap-3 touch-target-44">
                        <div class="d-flex align-items-center justify-content-center rounded-circle border border-dark-subtle transition-icon"
                            style="width: 44px; height: 44px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-gold)"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg>
                        </div>
                        <span style="font-size: 16px;">+91 97460 05500</span>
                    </a>

                    <a href="mailto:support@jodha.in"
                        class="text-dark text-decoration-none font-heading d-flex align-items-center gap-3 touch-target-44">
                        <div class="d-flex align-items-center justify-content-center rounded-circle border border-dark-subtle transition-icon"
                            style="width: 44px; height: 44px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-gold)"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                </path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <span style="font-size: 16px;">support@jodha.in</span>
                    </a>
                </div>

                <h6 class="font-heading mb-4"
                    style="font-size: 13px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase;">Follow
                    us</h6>
                <div class="d-flex gap-3 mb-5">
                    <a href="#"
                        class="d-flex align-items-center justify-content-center rounded-circle border border-dark-subtle text-dark transition-icon"
                        style="width: 44px; height: 44px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="#"
                        class="d-flex align-items-center justify-content-center rounded-circle border border-dark-subtle text-dark transition-icon"
                        style="width: 44px; height: 44px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                </div>

                <div class="d-flex flex-wrap gap-3 align-items-center" style="filter: grayscale(1); opacity: 0.4;">
                    <i class="fa-brands fa-cc-visa fs-3"></i>
                    <i class="fa-brands fa-cc-mastercard fs-3"></i>
                    <i class="fa-brands fa-cc-amex fs-3"></i>
                    <i class="fa-brands fa-cc-paypal fs-3"></i>
                    <i class="fa-brands fa-apple-pay fs-3"></i>
                    <i class="fa-brands fa-google-pay fs-3"></i>
                </div>

            </div>

        </div>
    </div>









    <!-- banner -->
    <section class="journal-hero py-5" style="background-color: var(--c-linen);">
        <div class="container text-center">
            <span class="text-gold text-uppercase letter-spacing-5 mb-3 d-block animate-reveal"
                style="font-size: 12px; font-weight: 700;">
                Insights & Inspiration
            </span>
            <h1 class="font-heading mb-4 animate-reveal delay-1" style="color: var(--c-primary); font-size: 52px;">The
                Jodha Journal</h1>
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
                    <button class="nav-link journal-cat-link active bg-transparent rounded-0 p-0 pb-2" id="all-tab"
                        data-bs-toggle="pill" data-bs-target="#all-stories" type="button" role="tab"
                        aria-controls="all-stories" aria-selected="true">
                        All Stories
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link journal-cat-link bg-transparent rounded-0 p-0 pb-2" id="care-tab"
                        data-bs-toggle="pill" data-bs-target="#furniture-care" type="button" role="tab"
                        aria-controls="furniture-care" aria-selected="false">
                        Furniture Care
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link journal-cat-link bg-transparent rounded-0 p-0 pb-2" id="design-tab"
                        data-bs-toggle="pill" data-bs-target="#interior-design" type="button" role="tab"
                        aria-controls="interior-design" aria-selected="false">
                        Interior Design
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link journal-cat-link bg-transparent rounded-0 p-0 pb-2" id="lifestyle-tab"
                        data-bs-toggle="pill" data-bs-target="#lifestyle" type="button" role="tab"
                        aria-controls="lifestyle" aria-selected="false">
                        Lifestyle
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link journal-cat-link bg-transparent rounded-0 p-0 pb-2" id="heritage-tab"
                        data-bs-toggle="pill" data-bs-target="#heritage" type="button" role="tab"
                        aria-controls="heritage" aria-selected="false">
                        Heritage
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="journalTabsContent">

                <div class="tab-pane fade show active" id="all-stories" role="tabpanel" aria-labelledby="all-tab">
                    <div class="row g-5">

                        <div class="col-lg-4 col-md-6">
                            <article class="blog-card h-100">
                                <div class="blog-img-wrapper overflow-hidden position-relative">
                                    <span
                                        class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">Oct
                                        12</span>
                                    <a href="journal-detail.html">
                                        <img src="assets/images/banner/living-room.jpg" alt="Teak Care"
                                            class="blog-img w-100 object-fit-cover" style="height: 300px;">
                                    </a>
                                </div>
                                <div class="blog-content pt-4 pb-2">
                                    <a href="#"
                                        class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">Furniture
                                        Care</a>
                                    <h4 class="font-heading mt-2 mb-3"><a href="journal-detail.html"
                                            class="text-dark text-decoration-none">Preserving Heritage: How to Care for
                                            Solid Teak</a></h4>
                                    <p class="text-muted small mb-3">Solid wood furniture is a living entity. Learn the
                                        ancient oiling techniques that keep your heirlooms looking royal for
                                        generations.</p>
                                    <a href="journal-detail.html"
                                        class="btn-link text-dark fw-bold text-decoration-none small">Read Article <svg
                                            width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="var(--c-gold)" stroke-width="2" class="ms-1 align-middle">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg></a>
                                </div>
                            </article>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <article class="blog-card h-100">
                                <div class="blog-img-wrapper overflow-hidden position-relative">
                                    <span
                                        class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">Sep
                                        28</span>
                                    <a href="journal-detail.html">
                                        <img src="assets/images/banner/living-room2.jpg" alt="Interior Styling"
                                            class="blog-img w-100 object-fit-cover" style="height: 300px;">
                                    </a>
                                </div>
                                <div class="blog-content pt-4 pb-2">
                                    <a href="#"
                                        class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">Interior
                                        Design</a>
                                    <h4 class="font-heading mt-2 mb-3"><a href="journal-detail.html"
                                            class="text-dark text-decoration-none">5 Ways to Style a Royal Living
                                            Room</a></h4>
                                    <p class="text-muted small mb-3">You don't need a palace to live like royalty.
                                        Discover how to balance heavy ornate furniture with modern minimalism.</p>
                                    <a href="journal-detail.html"
                                        class="btn-link text-dark fw-bold text-decoration-none small">Read Article <svg
                                            width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="var(--c-gold)" stroke-width="2" class="ms-1 align-middle">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg></a>
                                </div>
                            </article>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <article class="blog-card h-100">
                                <div class="blog-img-wrapper overflow-hidden position-relative">
                                    <span
                                        class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">Sep
                                        15</span>
                                    <a href="journal-detail.html">
                                        <img src="assets/images/furniturecategories/dining-chair.jpg"
                                            alt="Dining Etiquette" class="blog-img w-100 object-fit-cover"
                                            style="height: 300px;">
                                    </a>
                                </div>
                                <div class="blog-content pt-4 pb-2">
                                    <a href="#"
                                        class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">Lifestyle</a>
                                    <h4 class="font-heading mt-2 mb-3"><a href="journal-detail.html"
                                            class="text-dark text-decoration-none">The Art of the Indian Dinner
                                            Party</a></h4>
                                    <p class="text-muted small mb-3">From setting the table with brassware to arranging
                                        the seating for conversation, here is your guide to hosting with grace.</p>
                                    <a href="journal-detail.html"
                                        class="btn-link text-dark fw-bold text-decoration-none small">Read Article <svg
                                            width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="var(--c-gold)" stroke-width="2" class="ms-1 align-middle">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg></a>
                                </div>
                            </article>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <article class="blog-card h-100">
                                <div class="blog-img-wrapper overflow-hidden position-relative">
                                    <span
                                        class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">Aug
                                        30</span>
                                    <a href="journal-detail.html">
                                        <img src="assets/images/categories/office/office-desk.jpg" alt="Bone Inlay"
                                            class="blog-img w-100 object-fit-cover" style="height: 300px;">
                                    </a>
                                </div>
                                <div class="blog-content pt-4 pb-2">
                                    <a href="#"
                                        class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">Heritage</a>
                                    <h4 class="font-heading mt-2 mb-3"><a href="journal-detail.html"
                                            class="text-dark text-decoration-none">The Secret History of Bone Inlay</a>
                                    </h4>
                                    <p class="text-muted small mb-3">Uncover the origins of Jodhpur's finest craft. A
                                        look at how artisans have mastered the art of inlay for over four centuries.</p>
                                    <a href="journal-detail.html"
                                        class="btn-link text-dark fw-bold text-decoration-none small">Read Article <svg
                                            width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="var(--c-gold)" stroke-width="2" class="ms-1 align-middle">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg></a>
                                </div>
                            </article>
                        </div>

                    </div>
                </div>

                <div class="tab-pane fade" id="furniture-care" role="tabpanel" aria-labelledby="care-tab">
                    <div class="row g-5">
                        <div class="col-lg-4 col-md-6">
                            <article class="blog-card h-100">
                                <div class="blog-img-wrapper overflow-hidden position-relative">
                                    <span
                                        class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">Oct
                                        12</span>
                                    <a href="journal-detail.html">
                                        <img src="assets/images/banner/living-room.jpg" alt="Teak Care"
                                            class="blog-img w-100 object-fit-cover" style="height: 300px;">
                                    </a>
                                </div>
                                <div class="blog-content pt-4 pb-2">
                                    <a href="#"
                                        class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">Furniture
                                        Care</a>
                                    <h4 class="font-heading mt-2 mb-3"><a href="journal-detail.html"
                                            class="text-dark text-decoration-none">Preserving Heritage: How to Care for
                                            Solid Teak</a></h4>
                                    <p class="text-muted small mb-3">Solid wood furniture is a living entity. Learn the
                                        ancient oiling techniques that keep your heirlooms looking royal for
                                        generations.</p>
                                    <a href="journal-detail.html"
                                        class="btn-link text-dark fw-bold text-decoration-none small">Read Article <svg
                                            width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="var(--c-gold)" stroke-width="2" class="ms-1 align-middle">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg></a>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="interior-design" role="tabpanel" aria-labelledby="design-tab">
                    <div class="row g-5">
                        <div class="col-lg-4 col-md-6">
                            <article class="blog-card h-100">
                                <div class="blog-img-wrapper overflow-hidden position-relative">
                                    <span
                                        class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">Sep
                                        28</span>
                                    <a href="journal-detail.html">
                                        <img src="assets/images/banner/living-room2.jpg" alt="Interior Styling"
                                            class="blog-img w-100 object-fit-cover" style="height: 300px;">
                                    </a>
                                </div>
                                <div class="blog-content pt-4 pb-2">
                                    <a href="#"
                                        class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">Interior
                                        Design</a>
                                    <h4 class="font-heading mt-2 mb-3"><a href="journal-detail.html"
                                            class="text-dark text-decoration-none">5 Ways to Style a Royal Living
                                            Room</a></h4>
                                    <p class="text-muted small mb-3">You don't need a palace to live like royalty.
                                        Discover how to balance heavy ornate furniture with modern minimalism.</p>
                                    <a href="journal-detail.html"
                                        class="btn-link text-dark fw-bold text-decoration-none small">Read Article <svg
                                            width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="var(--c-gold)" stroke-width="2" class="ms-1 align-middle">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg></a>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="lifestyle" role="tabpanel" aria-labelledby="lifestyle-tab">
                    <div class="row g-5">
                        <div class="col-lg-4 col-md-6">
                            <article class="blog-card h-100">
                                <div class="blog-img-wrapper overflow-hidden position-relative">
                                    <span
                                        class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">Sep
                                        15</span>
                                    <a href="journal-detail.html">
                                        <img src="assets/images/furniturecategories/dining-chair.jpg"
                                            alt="Dining Etiquette" class="blog-img w-100 object-fit-cover"
                                            style="height: 300px;">
                                    </a>
                                </div>
                                <div class="blog-content pt-4 pb-2">
                                    <a href="#"
                                        class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">Lifestyle</a>
                                    <h4 class="font-heading mt-2 mb-3"><a href="journal-detail.html"
                                            class="text-dark text-decoration-none">The Art of the Indian Dinner
                                            Party</a></h4>
                                    <p class="text-muted small mb-3">From setting the table with brassware to arranging
                                        the seating for conversation, here is your guide to hosting with grace.</p>
                                    <a href="journal-detail.html"
                                        class="btn-link text-dark fw-bold text-decoration-none small">Read Article <svg
                                            width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="var(--c-gold)" stroke-width="2" class="ms-1 align-middle">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg></a>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="heritage" role="tabpanel" aria-labelledby="heritage-tab">
                    <div class="row g-5">
                        <div class="col-lg-4 col-md-6">
                            <article class="blog-card h-100">
                                <div class="blog-img-wrapper overflow-hidden position-relative">
                                    <span
                                        class="badge bg-white text-dark position-absolute top-0 start-0 m-3 z-2 rounded-0 shadow-sm text-uppercase x-small px-3 py-2">Aug
                                        30</span>
                                    <a href="journal-detail.html">
                                        <img src="assets/images/categories/office/office-desk.jpg" alt="Bone Inlay"
                                            class="blog-img w-100 object-fit-cover" style="height: 300px;">
                                    </a>
                                </div>
                                <div class="blog-content pt-4 pb-2">
                                    <a href="#"
                                        class="text-decoration-none text-gold text-uppercase x-small fw-bold letter-spacing-1">Heritage</a>
                                    <h4 class="font-heading mt-2 mb-3"><a href="journal-detail.html"
                                            class="text-dark text-decoration-none">The Secret History of Bone Inlay</a>
                                    </h4>
                                    <p class="text-muted small mb-3">Uncover the origins of Jodhpur's finest craft. A
                                        look at how artisans have mastered the art of inlay for over four centuries.</p>
                                    <a href="journal-detail.html"
                                        class="btn-link text-dark fw-bold text-decoration-none small">Read Article <svg
                                            width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="var(--c-gold)" stroke-width="2" class="ms-1 align-middle">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg></a>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-5 pt-5">
                <div class="col-12 d-flex justify-content-center align-items-center gap-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-luxury gap-2">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1"><i class="fa-solid fa-chevron-left"></i></a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#"><i class="fa-solid fa-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </section>











    <!-- Footer -->
    <footer class="footer-section pt-5 pb-3">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row gy-4">

                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="footer-heading mb-4">Top Categories</h5>
                    <ul class="footer-list p-0">
                        <li><a href="#">Coffee Tables</a></li>
                        <li><a href="#">Side Tables</a></li>
                        <li><a href="#">Sideboards & Cabinets</a></li>
                        <li><a href="#">Chest Of Drawer</a></li>
                        <li><a href="#">Bone Inlay</a></li>
                        <li><a href="#">Tissue Boxes</a></li>
                        <li><a href="#">Trays</a></li>
                        <li><a href="#">Gift Boxes</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="footer-heading mb-4">About Us</h5>
                    <ul class="footer-list p-0">
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Sitemap</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="footer-heading mb-4">Help & Information</h5>
                    <ul class="footer-list p-0">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Return Policy</a></li>
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">FAQs</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="footer-heading mb-4">Get in touch</h5>
                    <div class="contact-info mb-4">
                        <p class="font-jost"><i class="fa-solid fa-phone me-2"></i> 9746605500</p>
                    </div>

                    <h5 class="footer-heading mb-3">Follow us</h5>
                    <div class="social-links d-flex gap-3">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>

                <div class="col-12 col-lg-4 d-flex justify-content-lg-end align-items-start">
                    <div class="payment-methods d-flex gap-2 flex-wrap justify-content-center">
                        <i class="fa-brands fa-cc-visa"></i>
                        <i class="fa-brands fa-cc-mastercard"></i>
                        <i class="fa-brands fa-cc-amex"></i>
                        <i class="fa-brands fa-cc-paypal"></i>
                        <i class="fa-brands fa-cc-discover"></i>
                        <i class="fa-brands fa-google-pay"></i>
                        <i class="fa-brands fa-apple-pay"></i>
                    </div>
                </div>
            </div>

            <hr class="footer-divider mt-5 mb-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="copyright-text font-jost mb-0">
                        Copyright &copy; 2026 <a href="#" class="ms-2">Techsoft Web Solutions</a> | All Rights
                        Reserved
                    </p>
                </div>
            </div>
        </div>
    </footer>









    <!-------------------------------------------------------- SCRIPT--------------------------->


    <!--Offcanvas Menu Script-->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Grab all trigger links, back buttons, and the main panel
            const drilldownTriggers = document.querySelectorAll('.drilldown-trigger');
            const drilldownBacks = document.querySelectorAll('.drilldown-back');
            const mainPanel = document.querySelector('.main-panel');

            if (mainPanel && drilldownTriggers.length > 0) {
                // Logic to slide IN the sub-menu
                drilldownTriggers.forEach(trigger => {
                    trigger.addEventListener('click', function (e) {
                        e.preventDefault(); // Prevent page jump

                        // Get the ID of the sub-menu from 'data-target'
                        const targetId = this.getAttribute('data-target');
                        const targetPanel = document.getElementById(targetId);

                        if (targetPanel) {
                            // Uses the exact classes defined in your stylenew.css
                            mainPanel.classList.add('slide-out');
                            targetPanel.classList.add('slide-in');
                        }
                    });
                });

                // Logic to slide OUT the sub-menu (Back button)
                drilldownBacks.forEach(back => {
                    back.addEventListener('click', function (e) {
                        e.preventDefault(); // Prevent page jump

                        // Find the specific sub-menu we are currently inside
                        const currentPanel = this.closest('.sub-panel');

                        if (currentPanel) {
                            // Uses the exact classes defined in your stylenew.css
                            mainPanel.classList.remove('slide-out');
                            currentPanel.classList.remove('slide-in');
                        }
                    });
                });
            }
        });
    </script>

    <!-- Search Dropdown -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ==========================================
            // 1. DESKTOP SEARCH LOGIC
            // ==========================================
            const deskInput = document.querySelector('.desktop-search-input');
            const deskSuggestions = document.querySelector('.desktop-suggestions');
            const deskForm = document.getElementById('desktopSearchForm');

            if (deskInput && deskSuggestions && deskForm) {
                // Show suggestions on click or focus
                deskInput.addEventListener('focus', function () {
                    deskSuggestions.classList.remove('d-none');
                    deskSuggestions.classList.add('d-block');
                });

                // Hide when clicking anywhere outside the search form
                document.addEventListener('click', function (event) {
                    if (!deskForm.contains(event.target)) {
                        deskSuggestions.classList.remove('d-block');
                        deskSuggestions.classList.add('d-none');
                    }
                });
            }

            // ==========================================
            // 2. MOBILE SEARCH LOGIC
            // ==========================================
            const mobileSearchCollapse = document.getElementById('mobileSearchBox');
            const mobileInput = document.getElementById('mobileSearchInput');
            const mobileSuggestions = document.querySelector('.mobile-suggestions');

            if (mobileSearchCollapse && mobileInput) {
                mobileSearchCollapse.addEventListener('shown.bs.collapse', function () {
                    mobileInput.focus();
                });
            }

            if (mobileInput && mobileSuggestions) {
                mobileInput.addEventListener('focus', () => {
                    mobileSuggestions.classList.remove('d-none');
                    mobileSuggestions.classList.add('d-block');
                });

                document.addEventListener('click', (e) => {
                    if (!mobileInput.contains(e.target) && !mobileSuggestions.contains(e.target) && !e.target.closest('[data-bs-target="#mobileSearchBox"]')) {
                        mobileSuggestions.classList.remove('d-block');
                        mobileSuggestions.classList.add('d-none');
                    }
                });
            }
        });
    </script>







    <!-- Curated Collections slider -->
    <script>
        function scrollCategories(direction) {
            const container = document.getElementById('catSlider');
            // Scroll amount: Width of one item (240px) + Gap (24px) * 2 items
            const scrollAmount = 500;

            if (direction === 1) {
                container.scrollLeft += scrollAmount;
            } else {
                container.scrollLeft -= scrollAmount;
            }
        }
    </script>



    <!-- Deals of the Day -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const container = document.getElementById('dealContainer');
            const track = document.getElementById('dealTrack');
            const originalCards = Array.from(track.children);
            const cardWidth = originalCards[0].offsetWidth + 30; // Card width + gap (30px)

            // 1. CLONE CONTENT FOR INFINITE LOOP
            // We clone the items twice to ensure smooth scrolling in both directions
            originalCards.forEach(card => {
                let clone = card.cloneNode(true);
                track.appendChild(clone);
            });

            // 2. SETUP PAGINATION
            const paginationContainer = document.getElementById('dealPagination');
            originalCards.forEach((_, index) => {
                let dash = document.createElement('div');
                dash.className = index === 0 ? 'pagination-dash active' : 'pagination-dash';
                paginationContainer.appendChild(dash);
            });
            const dots = document.querySelectorAll('.pagination-dash');

            // 3. ANIMATION VARIABLES
            let scrollPos = 0;
            let isDragging = false;
            let startX = 0;
            let currentTranslate = 0;
            let prevTranslate = 0;
            let animationID;
            const speed = 0.8; // Auto-scroll speed

            // Calculate reset point (width of one full set of original cards)
            const resetThreshold = originalCards.length * cardWidth;

            // 4. MAIN LOOP FUNCTION
            function animate() {
                if (!isDragging) {
                    scrollPos += speed; // Auto move right-to-left
                }

                // Infinite Loop Logic: Reset instantly when we reach the end of set 1
                if (scrollPos >= resetThreshold) {
                    scrollPos = 0;
                    // Adjust drag offset if dragging occurred during reset
                    prevTranslate += resetThreshold;
                } else if (scrollPos < 0) {
                    scrollPos = resetThreshold;
                    prevTranslate -= resetThreshold;
                }

                // Apply Transform
                // If dragging, we use user input. If auto, we use scrollPos.
                const finalPos = isDragging ? currentTranslate : -(scrollPos);
                track.style.transform = `translateX(${finalPos}px)`;

                // Update Sync for next frame if not dragging
                if (!isDragging) {
                    prevTranslate = -scrollPos;
                    updateIndicators(Math.abs(scrollPos));
                }

                animationID = requestAnimationFrame(animate);
            }

            // Start Animation
            animate();

            // 5. DRAG EVENTS (Mouse & Touch)

            // Touch Start
            container.addEventListener('touchstart', touchStart);
            container.addEventListener('mousedown', touchStart);

            function touchStart(index) {
                isDragging = true;
                startX = getPositionX(index);
                container.style.cursor = 'grabbing';
                // Cancel any inertia/auto movement momentarily
                cancelAnimationFrame(animationID);
            }

            // Touch Move
            container.addEventListener('touchmove', touchMove);
            container.addEventListener('mousemove', touchMove);

            function touchMove(event) {
                if (isDragging) {
                    const currentX = getPositionX(event);
                    const diff = currentX - startX;
                    currentTranslate = prevTranslate + diff;
                    track.style.transform = `translateX(${currentTranslate}px)`;
                }
            }

            // Touch End
            container.addEventListener('touchend', touchEnd);
            container.addEventListener('mouseup', touchEnd);
            container.addEventListener('mouseleave', () => { if (isDragging) touchEnd() });

            function touchEnd() {
                isDragging = false;
                container.style.cursor = 'grab';

                // Sync the auto-scroll position to where the user left it
                scrollPos = -currentTranslate;
                prevTranslate = currentTranslate;

                // Resume Loop
                requestAnimationFrame(animate);
            }

            function getPositionX(event) {
                return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
            }

            // 6. PAUSE ON HOVER (Only if not dragging)
            container.addEventListener('mouseenter', () => {
                if (!isDragging) cancelAnimationFrame(animationID);
            });

            container.addEventListener('mouseleave', () => {
                if (!isDragging) requestAnimationFrame(animate);
            });

            // 7. INDICATOR LOGIC
            function updateIndicators(position) {
                // Calculate which "original" card is currently roughly in the center
                let activeIndex = Math.floor((position + (window.innerWidth / 2)) / cardWidth) % originalCards.length;

                dots.forEach(dot => dot.classList.remove('active'));
                if (dots[activeIndex]) dots[activeIndex].classList.add('active');
            }
        });
    </script>


    <!-- REVIEWS SECTION -->
    <script>
        function scrollReviews(direction) {
            const container = document.getElementById('reviewSlider');
            const itemWidth = container.querySelector('.review-item').offsetWidth + 30; // Item width + gap

            if (direction === 1) {
                container.scrollLeft += itemWidth;
            } else {
                container.scrollLeft -= itemWidth;
            }
        }
    </script>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>