@extends('layouts.base', ['subtitle' => 'Reset Password'])

@section('body-attribuet')
    class="authentication-bg"
@endsection

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
                                <h4 class="fw-bold text-dark mb-2">Reset Password</h3>
                                    <p class="text-muted">Your password must be at least 8 characters and should include a combination of numbers, letters and special characters (!$@%).</p>
                            </div>
                            <form action="{{ route('password.update') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                <input type="hidden" name="email" value="{{ $request->email }}">
                               <div class="mb-3">
                                    <label class="form-label" for="example-password">New Password</label>
                                    <input type="password" id="example-password" name="password" class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Enter your password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="example-password-confirm">Confirm Password</label>
                                    <input type="password" id="example-password-confirm" class="form-control"
                                        name="password_confirmation" placeholder="Confirm your password">
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-dark btn-lg fw-medium" type="submit">Reset
                                        Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <p class="text-center mt-4 text-white text-opacity-50">Back to
                        <a href="{{ route('signin') }}" class="text-decoration-none text-white fw-bold">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
