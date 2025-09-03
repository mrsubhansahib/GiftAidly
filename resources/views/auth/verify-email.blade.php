@extends('layouts.base', ['subtitle' => 'Verify Email'])

@section('body-attribuet')
class="authentication-bg"
@endsection

@section('content')
<div class="account-pages py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
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

                            <h4 class="fw-bold text-dark mb-2">Verify your email</h4>
                            <p class="text-muted mb-0">
                                We sent a verification link to <span class="fw-semibold">{{ auth()->user()->email }}</span>.
                                Please click the link to activate your account.
                            </p>
                        </div>

                        {{-- Info box --}}
                        <div class="alert alert-info alert-icon mt-4 mb-0" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded bg-info d-flex justify-content-center align-items-center fs-18 me-2 flex-shrink-0">
                                    <i class="bx bx-info-circle text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    Didn’t get the email? Check your spam folder or resend a new link.
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4">
                            <form method="POST" action="{{ route('verification.send') }}" class="d-grid gap-2">
                                @csrf
                                <button type="submit" class="btn btn-dark btn-lg fw-medium">
                                    Resend Verification Email
                                </button>
                            </form>

                            <div class="d-flex justify-content-end align-items-center mt-3">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary text-decoration-none  ">
                                        Logout
                                    </button>
                                </form>

                            </div>
                        </div>

                        {{-- Help --}}
                        <div class="mt-4 text-muted small">
                            <span class="d-inline-block">The verification link expires after 60 minutes for security reasons.</span>
                        </div>
                    </div>
                </div>

                <p class="text-center mt-4 text-white text-opacity-50">
                    Already clicked the link?
                    <a href="{{ route('signin') }}" class="text-decoration-none text-white fw-bold">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection