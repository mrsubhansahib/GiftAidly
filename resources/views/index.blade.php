@extends('layouts.vertical', ['subtitle' => 'Dashboard'])

@section('content')
    @if (auth()->check() && auth()->user()->role === 'donor')
        <script>
            window.location.href = "{{ route('third', ['user', 'donations', 'index']) }}";
        </script>
    @endif
    @include('layouts.partials.page-title', ['title' => 'GiftAidly', 'subtitle' => 'Dashboard'])
    <div class="row">
        <!-- Card 1 -->
        @if (auth()->check() && auth()->user()->role === 'admin')
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('third', ['admin', 'donors', 'index']) }}" class="text-decoration-none">
                    <div class="card hover-shadow" style="cursor: pointer;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded-circle">
                                        <iconify-icon icon="solar:users-group-rounded-outline"
                                            class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0 text-truncate">Donors</p>
                                    <h3 class="text-dark mt-2 mb-0">
                                        {{ \App\Models\User::where('role', 'donor')->count() }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-xl-4">
                <a href="{{ route('third', ['admin', 'donations', 'index']) }}" class="text-decoration-none">
                    <div class="card hover-shadow" style="cursor: pointer;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded-circle">
                                        <iconify-icon icon="solar:hand-heart-outline"
                                            class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0 text-truncate">Donations</p>
                                    <h3 class="text-dark mt-2 mb-0">
                                        {{ \App\Models\Subscription::count() }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>


            <div class="col-md-6 col-xl-4">
                <a href="{{ route('third', ['admin', 'transactions', 'index']) }}" class="text-decoration-none">
                    <div class="card hover-shadow" style="cursor: pointer;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded-circle">
                                        <iconify-icon icon="solar:transfer-horizontal-outline"
                                            class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0 text-truncate">Transactions</p>
                                    <h3 class="text-dark mt-2 mb-0">{{ \App\Models\Transaction::count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>

@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard.js'])
@endsection
