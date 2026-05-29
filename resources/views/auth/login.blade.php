@extends('layouts.app')

@section('content')

    <!-- Login Form -->
    <section class="auth-section">
        <div class="container-fluid h-100 p-0">
            <div class="row g-0 h-100">

                <div
                    class="col-lg-7 col-xl-6 d-flex flex-column align-items-center justify-content-center px-4 py-4 px-md-5 bg-white shadow-sm z-2">

                    <div class="mb-4 mt-4">
                        <a href="{{route('home')}}">
                            <img src="{{asset('images/logo/brand-logo-nobg.png')}}" height="40" alt="Jodha Logo">
                        </a>
                    </div>

                    <div class="auth-card w-100 text-center" style="max-width: 400px;">
                        <h2 class="font-heading mb-2" style="color: var(--c-primary);">Welcome back</h2>
                        <p class="text-muted mb-3" style="font-family: var(--f-body); font-size: 14px;">Enter your
                            details to sign in or create an account</p>

                        <a href="{{ url('auth/google') }}"
                            class="btn btn-outline-dark rounded-0 w-100 py-3 mb-3 d-flex align-items-center justify-content-center transition-all"
                            style="font-family: var(--f-body); font-size: 13px; letter-spacing: 1px; font-weight: 500; border-color: rgba(0,0,0,0.15);">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg"
                                height="18" class="me-3">
                            Continue with Google
                        </a>

                        <div class="d-flex align-items-center mb-4">
                            <hr class="flex-grow-1 opacity-10">
                            <span class="mx-3 text-muted"
                                style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px;">or</span>
                            <hr class="flex-grow-1 opacity-10">
                        </div>

                        <form id="loginForm">
                            @csrf
                            <div class="mb-4">
                                <input type="email" name="email" class="form-control luxury-input-minimal w-100 py-3"
                                    placeholder="Email address" required>
                            </div>

                            <div class="mb-4">
                                <input type="password" name="password" class="form-control luxury-input-minimal w-100 py-3" 
                                    placeholder="Password" required>
                                <div class="text-end mt-2">
                                    <a href="{{ route('password.request') }}" class="text-muted small text-decoration-none hover-gold transition-all">Forgot your password?</a>
                                </div>
                            </div>

                            <button type="submit" class="btn-luxury-solid w-100 py-3 mb-3" id="loginBtn">
                                Login
                            </button>
                            
                        </form>

                        <p class="text-center mt-3" style="font-size: 13px;">
                            Don't have an account? <a href="{{ route('register') }}" class="text-dark fw-bold">Register here</a>
                        </p>

                        <p class="text-center mt-4" style="font-size: 12px; color: #999; font-family: var(--f-body);">
                            By continuing, you agree to our
                            <a href="{{ route('policy.terms') }}" class="text-decoration-underline text-dark">Terms of Service</a> and
                            <a href="{{ route('policy.privacy') }}" class="text-decoration-underline text-dark">Privacy Policy</a>.
                        </p>
                    </div>

                    <div class="mt-auto pb-2 pt-2">
                        <p class="mb-0 text-muted small letter-spacing-1">© {{ date('Y') }} JODHA FURNITURE</p>
                    </div>
                </div>

                <div class="col-lg-5 col-xl-6 position-relative d-none d-lg-block overflow-hidden">

                    <div class="video-background-wrapper h-100 w-100">
                        <video autoplay muted loop playsinline class="h-100 w-100 object-fit-cover">
                            <source src="{{asset('videos/banner/v2.mp4')}}" type="video/mp4">
                        </video>
                        <div class="video-overlay position-absolute top-0 start-0 w-100 h-100"></div>
                    </div>

                    <div class="position-absolute top-50 start-50 translate-middle text-center text-white w-75">
                        <span class="text-gold text-uppercase letter-spacing-3 mb-3 d-block"
                            style="font-family: var(--f-body); font-weight: 600; font-size: 12px;">The Privilege
                            Club</span>
                        <h1 class="display-4 font-heading mb-4">Exclusive Offers <br> Available Now</h1>
                        <div class="offer-code-badge d-inline-block py-3 px-5 border border-white-50">
                            <p class="mb-1 small text-uppercase opacity-75">Use Code At Checkout</p>
                            <h3 class="mb-0 letter-spacing-3 text-gold">JODHA20</h3>
                        </div>
                        <p class="mt-4 opacity-75"
                            style="font-family: var(--f-body); font-size: 15px; font-weight: 300;">Enjoy 20% Off our
                            Royal Collections </p>
                    </div>

                </div>

            </div>
        </div>
    </section>

@endsection

@section('footer_extras')
<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('loginBtn');
        const originalText = btn.innerText;
        btn.innerText = 'Logging in...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch('/customer/login', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alertify.success(data.message);
                setTimeout(() => {
                    window.location.href = data.redirect || '/';
                }, 1500);
            } else {
                alertify.error(data.message || 'Something went wrong');
                btn.innerText = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertify.error('An error occurred. Please try again.');
            btn.innerText = originalText;
            btn.disabled = false;
        });
    });
</script>
@endsection
