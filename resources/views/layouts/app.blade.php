<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('images/logo/favicon.png')}}" type="image/x-icon">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Jodha Furnitures | Shop Premium Furniture and Home Decor In Kochi</title>

    @yield('head_metas')


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{asset('css/stylenew.css?v1.1')}}">

    
        
    

    <style>
        @keyframes slideInToast {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutToast {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        .lazy-image{
            opacity:0;
            transform: translateY(20px);
            transition: opacity .6s ease, transform .6s ease;
        }

        .lazy-image.loaded{
            opacity:1;
            transform: translateY(0);
        }

        @media(max-width: 767px) {
            .mobile-swipe-row {
                flex-wrap: nowrap !important;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }
            .mobile-swipe-row::-webkit-scrollbar {
                display: none;
            }
            .mobile-swipe-row > .col {
                flex: 0 0 75%;
                max-width: 75%;
            }
        }
    </style>

    @yield('head_extras')


</head>

<body>




    @include('partials.header')

    @include('partials.cart-drawer')



    @yield('content')



    @include('partials.footer')

   


    <!-------------------------------------------------------- SCRIPT--------------------------->

    <!--Offcanvas Menu Script-->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mainPanel = document.querySelector('.main-panel');
            const triggers = document.querySelectorAll('.drilldown-trigger');
            const backs = document.querySelectorAll('.drilldown-back');

            // Slide In
            triggers.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    const targetPanel = document.getElementById(targetId);

                    mainPanel.classList.add('slide-out');
                    targetPanel.classList.add('slide-in');
                });
            });

            // Slide Out (Back Button)
            backs.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const currentPanel = this.closest('.sub-panel');

                    mainPanel.classList.remove('slide-out');
                    currentPanel.classList.remove('slide-in');
                });
            });
        });
    </script>



    <!-- Testmonial -->
    <script>
        const track = document.getElementById('reviewTrack');
        const container = document.getElementById('scrollContainer');
        let isDown = false;
        let startX;
        let scrollLeft;
        let scrollPos = 0;
        let isPaused = false;

        // 1. Automatic Smooth Scroll Logic
        function step() {
            if (!isPaused && !isDown) {
                scrollPos += 0.5; // Adjust speed here
                if (scrollPos >= track.scrollWidth / 2) scrollPos = 0;
                container.scrollLeft = scrollPos;
            }
            requestAnimationFrame(step);
        }

        // 2. Drag Logic
        container.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        container.addEventListener('mouseleave', () => { isDown = false; isPaused = false; });
        container.addEventListener('mouseup', () => { isDown = false; isPaused = false; });

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            isPaused = true; // Stop auto-scroll while dragging
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // Scroll speed multiplier
            container.scrollLeft = scrollLeft - walk;
            scrollPos = container.scrollLeft; // Sync auto-scroll with manual drag position
        });

        // 3. Pause on Hover
        container.addEventListener('mouseenter', () => isPaused = true);
        container.addEventListener('mouseleave', () => isPaused = false);

        // Initialize
        requestAnimationFrame(step);
    </script>






    <!-- Product Image Changer -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const thumbs = document.querySelectorAll('.thumbnail-wrapper');
            const swipeWrapper = document.querySelector('.mobile-swipe-wrapper');
            const mainImages = document.querySelectorAll('.main-swipe-img');

            // 1. Thumbnail Click Logic (Desktop & Mobile)
            thumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', function () {
                    // Update Active Class
                    thumbs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    if (window.innerWidth < 992) {
                        // Mobile: Scroll the swipe wrapper to the correct image
                        const scrollAmount = swipeWrapper.offsetWidth * index;
                        swipeWrapper.scrollTo({ left: scrollAmount, behavior: 'smooth' });
                    } else {
                        // Desktop: Swap the first image source
                        mainImages[0].src = this.querySelector('img').src;
                        mainImages[0].style.display = 'block';
                    }
                });
            });

            // 2. Swipe-to-Thumbnail Sync (Mobile Only)
            if (swipeWrapper) {
                swipeWrapper.addEventListener('scroll', function () {
                    const index = Math.round(swipeWrapper.scrollLeft / swipeWrapper.offsetWidth);
                    thumbs.forEach(t => t.classList.remove('active'));
                    if (thumbs[index]) {
                        thumbs[index].classList.add('active');
                    }
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/alertify.min.css" integrity="sha512-IXuoq1aFd2wXs4NqGskwX2Vb+I8UJ+tGJEu/Dc0zwLNKeQ7CW3Sr6v0yU3z5OQWe3eScVIkER4J9L7byrgR/fA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/themes/default.min.css" integrity="sha512-RgUjDpwjEDzAb7nkShizCCJ+QTSLIiJO1ldtuxzs0UIBRH4QpOjUU9w47AF9ZlviqV/dOFGWF6o7l3lttEFb6g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js" integrity="sha512-JnjG+Wt53GspUQXQhc+c4j8SBERsgJAoHeehagKHlxQN+MtCCmFDghX9/AcbkkNRZptyZU4zC8utK59M5L45Iw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



    @yield('footer_extras')




    <script>

        let lazyObserver;
        function initLazyLoad() {
            const images = document.querySelectorAll(".lazy-image:not(.observed)");

            if (!lazyObserver) {
                lazyObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            const realSrc = img.getAttribute("data-src");
                            if (realSrc) {
                                img.src = realSrc;
                                img.onload = () => {
                                    img.classList.add("loaded");
                                };
                            }
                            observer.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: "0px 0px 100px 0px",
                    threshold: 0.1
                });
            }

            images.forEach(img => {
                img.classList.add('observed');
                lazyObserver.observe(img);
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            initLazyLoad();
        });



function equalizeHeights(selector) {
    const elements = document.querySelectorAll(selector);

    let maxHeight = 0;

    // Reset height first
    elements.forEach(el => {
        el.style.height = 'auto';
    });

    // Find tallest element
    elements.forEach(el => {
        if (el.offsetHeight > maxHeight) {
            maxHeight = el.offsetHeight;
        }
    });

    // Apply tallest height to all
    elements.forEach(el => {
        el.style.height = maxHeight + 'px';
    });
}

// Run after page load
window.addEventListener('load', function () {
    equalizeHeights('.product-card');
});





    </script>




</body>

</html>