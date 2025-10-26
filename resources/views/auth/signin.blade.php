@extends('layouts.base', ['subtitle' => 'Sign In'])

@section('body-attribuet')
class="authentication-bg"
@endsection

<!-- style -->
<style>
    /* Container for password input to position the icon */
    .password-input-container {
        position: relative;
    }

    /* Styling for the eye icon */
    .password-toggle-icon {
        position: absolute;
        top: 50%;
        margin-top: -5px;
        /* Half the icon height to center it vertically */
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        /* Muted icon color */
        transition: color 0.2s ease-in-out;
        z-index: 100;
    }

    .password-toggle-icon:hover {
        color: #343a40;
        /* Darker color on hover */
    }

    /* Add padding to the input to prevent text from overlapping the icon */
    .form-control-password {
        padding-right: 45px !important;
    }
</style>


@section('content')
<div class="account-pages py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center">
                            <div class="mx-auto mb-4 text-center auth-logo">
                                <a href="{{ route('any', 'index') }}" class="logo-dark">
                                    <img src="/images/logo-dark.png" height="32" alt="logo dark">
                                </a>

                                <a href="{{ route('any', 'index') }}" class="logo-light">
                                    <img src="/images/logo-light.png" height="28" alt="logo light">
                                </a>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Welcome Back!</h3>
                                <p class="text-muted">Sign in to your account to continue</p>
                        </div>
                        <form method="POST" action="{{ route('signin') }}" class="mt-4">

                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="user@demo.com" value="{{ old('email') }}"
                                    placeholder="Enter your email">
                                @error('email')
                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="password" class="form-label">Password</label>
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small text-muted">Forgot password?</a>
                                </div>
                                <div class="password-input-container">
                                    <input type="password" class="form-control form-control-password" id="password" name="password" placeholder="Enter your password" required>
                                    <span class="password-toggle-icon" onclick="togglePassword('password', this)">
                                        <iconify-icon icon="mdi:eye-outline" width="24" height="24"></iconify-icon>
                                    </span>
                                </div>
                            </div>
                            <!-- <div class="form-check mb-3">
                                <input type="checkbox" required class="form-check-input" id="remember-me">
                                <label class="form-check-label" for="remember-me">Remember me</label>
                            </div> -->
                            <div class="d-grid">
                                <button style="background: linear-gradient(45deg, #1d43ab, #94740dff);" class="btn btn-lg fw-medium text-light" type="submit">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- <p class="text-center mt-4 text-white text-opacity-50">Don't have an account?
                    <a href="{{ route('signup') }}"
                        class="text-decoration-none text-white fw-bold">Sign Up</a>
                </p> -->
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<!-- <script>
    function togglePassword(fieldId, iconContainer) {
        const field = document.getElementById(fieldId);
        const icon = iconContainer.querySelector("iconify-icon");

        if (field.type === "password") {
            field.type = "text";
            icon.setAttribute("icon", "mdi:eye-off-outline");
        } else {
            field.type = "password";
            icon.setAttribute("icon", "mdi:eye-outline");
        }
    }
</script> -->