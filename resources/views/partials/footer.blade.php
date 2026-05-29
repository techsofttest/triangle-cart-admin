 <footer class="footer-section pt-5 pb-3">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row gy-4">

                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="footer-heading mb-4">Top Categories</h5>
                    <ul class="footer-list p-0">
                        @foreach($full_categories->take(8) as $category)
                            <li><a href="{{ route('category.show', ['slug' => $category->slug]) }}">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="footer-heading mb-4">About Us</h5>
                    <ul class="footer-list p-0">
                        <li><a href="{{ route('about') }}">Our Story</a></li>
                        <li><a href="{{ route('article.index') }}">Article</a></li>
                        <li><a href="{{ route('product.index') }}">All Products</a></li>
                        <!--<li><a href="{{ route('sitemap') }}">Sitemap</a></li>-->
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="footer-heading mb-4">Help & Information</h5>
                    <ul class="footer-list p-0">
                        <li><a href="{{ route('policy.refund') }}">Return Policy</a></li>
                        <!--<li><a href="{{ route('policy.privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('policy.shipping') }}">Shipping Policy</a></li>
                        <li><a href="{{ route('policy.terms') }}">Terms & Conditions</a></li>-->
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                        <li><a href="{{ route('faq') }}">FAQs</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="footer-heading mb-4">Get in touch</h5>
                    <div class="contact-info mb-4">
                        <p class="font-jost"><i class="fa-solid fa-phone me-2"></i> +91 9895599002</p>
                         <p class="font-jost"><i class="fa-solid fa-phone me-2"></i> +91 9847300077</p>
                    </div>

                    <h5 class="footer-heading mb-3">Follow us</h5>
                    <div class="social-links d-flex gap-3">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/ashborn_furniture?igsh=MWZvZGx4dTQxMGlocw%3D%3D&utm_source=qr
https://www.instagram.com/jodhafurniture.official_store?igsh=MWxjcTd0a2VpNTI2aw==
" target="_blank"><i class="fa-brands fa-instagram"></i></a>
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
                        Copyright &copy; @php echo date('Y') @endphp <a href="#" class="ms-2" style="text-decoration: none;">Techsoft Web
                            Solutions</a> | All Rights
                        Reserved
                    </p>
                </div>
            </div>
        </div>
    </footer>
