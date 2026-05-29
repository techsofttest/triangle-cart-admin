@extends('layouts.app')

@section('content')

    <!-- Forgot Password Form -->
    <section class="auth-section">
        <div class="container-fluid h-100 p-0">
            <div class="row g-0 h-100">

                <div class="col-lg-7 col-xl-6 d-flex flex-column align-items-center justify-content-center px-4 py-4 px-md-5 bg-white shadow-sm z-2">

                    <div class="mb-4 mt-4">
                        <a href="{{route('home')}}">
                            <img src="{{asset('images/logo/brand-logo-nobg.png')}}" height="40" alt="Jodha Logo">
                        </a>
                    </div>

                    <div class="auth-card w-100 text-center" style="max-width: 400px;" id="forgotStep1">
                        <h2 class="font-heading mb-2" style="color: var(--c-primary);">Reset Password</h2>
                        <p class="text-muted mb-4" style="font-family: var(--f-body); font-size: 14px;">Enter your email address and we'll send you an OTP to reset your password.</p>

                        <form id="forgotForm">
                            @csrf
                            <div class="mb-4 text-start">
                                <label class="small text-muted mb-2 uppercase-tracking">Email Address</label>
                                <input type="email" name="email" class="form-control luxury-input-minimal w-100 py-3"
                                    placeholder="Enter your registered email" required>
                            </div>

                            <button type="submit" class="btn-luxury-solid w-100 py-3 mb-3" id="forgotBtn">
                                Send OTP
                            </button>
                        </form>

                        <p class="text-center mt-3" style="font-size: 13px;">
                            Remembered your password? <a href="{{ route('login') }}" class="text-dark fw-bold">Login here</a>
                        </p>
                    </div>

                    <!-- Step 2: Verify OTP -->
                    <div class="auth-card w-100 text-center d-none" style="max-width: 400px;" id="forgotStep2">
                        <h2 class="font-heading mb-2" style="color: var(--c-primary);">Verify OTP</h2>
                        <p class="text-muted mb-4" style="font-family: var(--f-body); font-size: 14px;">We've sent a 6-digit code to your email. Please enter it below.</p>

                        <form id="verifyForgotOTPForm">
                            @csrf
                            <div class="mb-4">
                                <input type="text" name="otp" class="form-control text-center luxury-input-minimal w-100 py-3" 
                                    placeholder="000000" maxlength="6" required style="letter-spacing: 10px; font-size: 24px;">
                            </div>

                            <button type="submit" class="btn-luxury-solid w-100 py-3 mb-3" id="verifyForgotBtn">
                                Verify OTP
                            </button>
                        </form>
                    </div>

                    <!-- Step 3: New Password -->
                    <div class="auth-card w-100 text-center d-none" style="max-width: 400px;" id="forgotStep3">
                        <h2 class="font-heading mb-2" style="color: var(--c-primary);">New Password</h2>
                        <p class="text-muted mb-4" style="font-family: var(--f-body); font-size: 14px;">Create a secure password for your account.</p>

                        <form id="resetPasswordForm">
                            @csrf
                            <div class="mb-4 text-start">
                                <label class="small text-muted mb-2 uppercase-tracking">New Password</label>
                                <input type="password" name="password" class="form-control luxury-input-minimal w-100 py-3" 
                                    required>
                            </div>
                            <div class="mb-4 text-start">
                                <label class="small text-muted mb-2 uppercase-tracking">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control luxury-input-minimal w-100 py-3" 
                                    required>
                            </div>

                            <button type="submit" class="btn-luxury-solid w-100 py-3 mb-3" id="resetBtn">
                                Reset Password
                            </button>
                        </form>
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
                </div>

            </div>
        </div>
    </section>

@endsection

@section('footer_extras')
<script>
    // Step 1: Send OTP
    document.getElementById('forgotForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('forgotBtn');
        btn.disabled = true;
        btn.innerText = 'Sending...';

        fetch('{{ route("password.send-otp") }}', {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'otp_sent') {
                alertify.success('OTP sent to your email');
                document.getElementById('forgotStep1').classList.add('d-none');
                document.getElementById('forgotStep2').classList.remove('d-none');
            } else {
                alertify.error(data.message);
                btn.disabled = false;
                btn.innerText = 'Send OTP';
            }
        });
    });

    // Step 2: Verify OTP
    document.getElementById('verifyForgotOTPForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('verifyForgotBtn');
        btn.disabled = true;

        fetch('{{ route("password.verify-otp") }}', {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'otp_verified') {
                alertify.success('OTP Verified');
                document.getElementById('forgotStep2').classList.add('d-none');
                document.getElementById('forgotStep3').classList.remove('d-none');
            } else {
                alertify.error(data.message);
                btn.disabled = false;
            }
        });
    });

    // Step 3: Reset Password
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('resetBtn');
        btn.disabled = true;

        fetch('{{ route("password.update") }}', {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alertify.success(data.message);
                setTimeout(() => window.location.href = data.redirect, 2000);
            } else {
                alertify.error(data.message || 'Validation failed');
                btn.disabled = false;
            }
        });
    });
</script>
@endsection
