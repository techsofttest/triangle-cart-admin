<!-- LOGIN/REGISTER MODAL -->
<style>
    .auth-modal { border-radius: 14px; padding: 10px; }
    .auth-modal .form-control { height: 45px; border-radius: 8px; }
    .auth-modal .btn { height: 45px; border-radius: 8px; }
    .error-msg { font-size: 0.85rem; min-height: 20px; }
</style>

<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content auth-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Welcome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Social Login -->
                <div id="socialAuth">
                    <a href="/auth/google" class="btn btn-light border w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
                        <img src="https://developers.google.com/identity/images/g-logo.png" width="20">
                        Continue with Google
                    </a>
                    <div class="text-center small text-muted mb-3">or</div>
                </div>

                <!-- Login Form -->
                <form id="loginForm">
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email address" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                    <button type="submit" class="btn btn-dark w-100">Login</button>
                    <div class="login-error text-danger mt-2 error-msg"></div>
                    <div class="text-center mt-3">
                        <a href="javascript:void(0)" onclick="toggleAuth('register')">Create account</a>
                    </div>
                </form>

                <!-- Register Form -->
                <form id="registerForm" style="display:none">
                    <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                    <button type="submit" id="sendOTP" class="btn btn-dark w-100">Send OTP</button>
                    <div class="register-error text-danger mt-2 error-msg"></div>
                    <div class="text-center mt-3">
                        <a href="javascript:void(0)" onclick="toggleAuth('login')">Already have an account?</a>
                    </div>
                </form>

                <!-- OTP Verification -->
                <form id="otpForm" style="display:none">
                    <p class="text-center mb-2">Enter OTP sent to your email</p>
                    <input type="text" name="otp" class="form-control mb-2 text-center" placeholder="6 digit OTP" maxlength="6" required>
                    <div class="text-center small text-muted mb-2">
                        OTP expires in <span id="otpTimer">05:00</span>
                    </div>
                    <div class="text-center mb-3">
                        <button type="button" id="resendOTP" class="btn btn-link p-0 text-decoration-none" disabled>
                            Resend OTP (30)
                        </button>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Verify & Create Account</button>
                    <div class="otp-error text-danger mt-2 error-msg"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Utility: Loading Spinner
    function setLoading(btn, isLoading) {
        if (isLoading) {
            btn.dataset.originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;
        } else {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.originalHtml;
        }
    }

    // Toggle between Login and Register
    function toggleAuth(view) {
        const isReg = view === 'register';
        document.getElementById('loginForm').style.display = isReg ? 'none' : 'block';
        document.getElementById('registerForm').style.display = isReg ? 'block' : 'none';
        document.getElementById('socialAuth').style.display = isReg ? 'none' : 'block';
        document.getElementById('otpForm').style.display = 'none';
    }

    // Global Fetch Headers
    const headers = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
    };

    // 1. LOGIN
    document.getElementById('loginForm').onsubmit = async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const err = this.querySelector('.login-error');
        err.innerHTML = '';
        setLoading(btn, true);

        try {
            const response = await fetch('/customer/login', {
                method: 'POST',
                headers: headers,
                body: new FormData(this)
            });
            const data = await response.json();
            if (data.status === 'success') {
                window.location.reload();
            } else {
                err.innerHTML = data.message || 'Login failed';
            }
        } catch (e) { err.innerHTML = 'Server error. Try again.'; }
        setLoading(btn, false);
    };

    // 2. REGISTER (SEND OTP)
    document.getElementById('registerForm').onsubmit = async function(e) {
        e.preventDefault();
        const btn = document.getElementById('sendOTP');
        const err = this.querySelector('.register-error');
        err.innerHTML = '';
        setLoading(btn, true);

        try {
            const response = await fetch('/customer/send-otp', {
                method: 'POST',
                headers: headers,
                body: new FormData(this)
            });
            const data = await response.json();
            if (data.status === 'otp_sent') {
                document.getElementById('registerForm').style.display = 'none';
                document.getElementById('otpForm').style.display = 'block';
                startOTPTimer(300);
                startResendTimer(30);
            } else {
                err.innerHTML = data.message || Object.values(data.errors).flat()[0];
            }
        } catch (e) { err.innerHTML = 'Server error.'; }
        setLoading(btn, false);
    };

    // 3. VERIFY OTP
    document.getElementById('otpForm').onsubmit = async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const err = this.querySelector('.otp-error');
        err.innerHTML = '';
        setLoading(btn, true);

        try {
            const response = await fetch('/customer/verify-otp', {
                method: 'POST',
                headers: headers,
                body: new FormData(this)
            });
            const data = await response.json();
            if (data.status === 'registered') {
                window.location.reload();
            } else {
                err.innerHTML = data.message;
            }
        } catch (e) { err.innerHTML = 'Server error.'; }
        setLoading(btn, false);
    };

    // 4. RESEND OTP
    document.getElementById('resendOTP').onclick = async function() {
        setLoading(this, true);
        const regFormData = new FormData(document.getElementById('registerForm'));
        try {
            await fetch('/customer/send-otp', { method: 'POST', headers: headers, body: regFormData });
            startResendTimer(30);
        } catch (e) { console.error(e); }
        setLoading(this, false);
    };

    // Timers
    function startOTPTimer(duration) {
        let timer = duration, minutes, seconds;
        const display = document.getElementById('otpTimer');
        const interval = setInterval(() => {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
            display.textContent = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
            if (--timer < 0) {
                clearInterval(interval);
                document.querySelector('.otp-error').innerHTML = 'OTP expired. Please resend.';
            }
        }, 1000);
    }

    function startResendTimer(duration) {
        const btn = document.getElementById('resendOTP');
        let timer = duration;
        btn.disabled = true;
        const interval = setInterval(() => {
            btn.innerText = `Resend OTP (${timer})`;
            if (--timer < 0) {
                clearInterval(interval);
                btn.disabled = false;
                btn.innerText = "Resend OTP";
            }
        }, 1000);
    }
</script>