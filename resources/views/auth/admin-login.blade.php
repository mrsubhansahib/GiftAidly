@extends('layouts.base', ['subtitle' => 'Lock Screen'])

@section('body-attribute')
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
                                @php
                                    use App\Models\User;
                                    $admin = User::where('role', 'admin')->first();
                                @endphp
                                <h4 class="fw-bold text-dark mb-2">Hi ! {{ $admin->name }}</h4>
                                <p class="text-muted">Enter your password to access the admin.</p>
                            </div>

                            <form action="{{ route('admin.signin') }}" method="POST" class="mt-4">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="example-password">Password</label>
                                    <input type="password" name="password" required id="example-password"
                                        class="form-control" placeholder="Enter your password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-1 text-center d-grid">
                                    <button class="btn btn-dark btn-lg fw-medium" type="submit">Sign In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
