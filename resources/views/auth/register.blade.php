@extends('layouts.app')

@section('content')

    <!-- Register Form -->
    <section class="auth-section">
        <div class="container-fluid h-100 p-0">
            <div class="row g-0 h-100">

                <div
                    class="col-lg-7 col-xl-6 d-flex flex-column align-items-center justify-content-center px-4 py-4 px-md-5 bg-white shadow-sm z-2">

                    <div class="mb-4 mt-4">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('images/logo/brand-logo-nobg.png') }}" height="40" alt="Jodha Logo">
                        </a>
                    </div>

                    <div class="auth-card w-100 text-center" style="max-width: 400px;">
                        <h2 class="font-heading mb-2" style="color: var(--c-primary);">Join the Club</h2>
                        <p class="text-muted mb-3" style="font-family: var(--f-body); font-size: 14px;">Create your account to enjoy exclusive benefits</p>

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

                        <!-- Registration Form -->
                        <form id="registerForm">
                            @csrf
                            <div class="mb-4">
                                <input type="text" name="name" class="form-control luxury-input-minimal w-100 py-3" placeholder="Full Name" required>
                            </div>

                            <div class="mb-4">
                                <input type="email" name="email" class="form-control luxury-input-minimal w-100 py-3"
                                    placeholder="Email address" required>
                            </div>

                            <div class="mb-4">
                                <input type="text" name="phone" class="form-control luxury-input-minimal w-100 py-3"
                                    placeholder="Phone" required>
                            </div>

                            <div class="mb-4">
                                <input type="password" name="password" class="form-control luxury-input-minimal w-100 py-3" placeholder="Password" required>
                            </div>

                            <button type="submit" class="btn-luxury-solid w-100 py-3 mb-3" id="registerBtn">
                                Register
                            </button>
                        </form>

                        <!-- OTP Form (Hidden) -->
                        <form id="otpForm" style="display: none;">
                            @csrf
                            <p class="text-muted mb-4">We've sent a 6-digit code to your email. Please enter it below to verify.</p>
                            <div class="mb-4">
                                <input type="text" name="otp" class="form-control luxury-input-minimal w-100 py-3 text-center" placeholder="Enter 6-digit OTP" maxlength="6" required>
                            </div>

                            <button type="submit" class="btn-luxury-solid w-100 py-3 mb-3" id="otpBtn">
                                Verify & Create Account
                            </button>

                            <button type="button" class="btn btn-link text-dark text-decoration-none" id="changeEmailBtn">
                                Change Email
                            </button>
                        </form>

                        <p class="text-center mt-3" style="font-size: 13px;">
                            Already have an account? <a href="{{ route('login') }}" class="text-dark fw-bold">Login here</a>
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
                            <source src="{{ asset('videos/banner/v2.mp4') }}" type="video/mp4">
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
    const registerForm = document.getElementById('registerForm');
    const otpForm = document.getElementById('otpForm');
    const registerBtn = document.getElementById('registerBtn');
    const otpBtn = document.getElementById('otpBtn');
    const changeEmailBtn = document.getElementById('changeEmailBtn');

    // Handle Register Submit (Send OTP)
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        registerBtn.innerText = 'Sending OTP...';
        registerBtn.disabled = true;

        const formData = new FormData(this);

        fetch('/customer/send-otp', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'otp_sent') {
                alertify.success('OTP sent to your email');
                registerForm.style.display = 'none';
                otpForm.style.display = 'block';
            } else {
                let msg = data.message || 'Validation failed';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('<br>');
                }
                alertify.error(msg);
                registerBtn.innerText = 'Register';
                registerBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertify.error('An error occurred');
            registerBtn.innerText = 'Register';
            registerBtn.disabled = false;
        });
    });

    // Handle OTP Submit
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        otpBtn.innerText = 'Verifying...';
        otpBtn.disabled = true;

        const formData = new FormData(this);

        fetch('/customer/verify-otp', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'registered') {
                alertify.success('Account verified successfully!');
                setTimeout(() => {
                    window.location.href = '/';
                }, 1500);
            } else {
                alertify.error(data.message || 'Invalid OTP');
                otpBtn.innerText = 'Verify & Create Account';
                otpBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertify.error('An error occurred');
            otpBtn.innerText = 'Verify & Create Account';
            otpBtn.disabled = false;
        });
    });

    changeEmailBtn.addEventListener('click', () => {
        otpForm.style.display = 'none';
        registerForm.style.display = 'block';
        registerBtn.innerText = 'Register';
        registerBtn.disabled = false;
    });
</script>
@endsection
