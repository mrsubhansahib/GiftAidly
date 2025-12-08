<div class="app-sidebar">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="" class="logo-dark">
            <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
            <img src="/images/logo-dark.png" style="width: 140px; height: 40px;" class="logo-lg" alt="logo dark">
        </a>

        <a href="" class="logo-light">
            <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
            <img src="/images/logo-light.png" style="width: 135px; height: 40px;" class="logo-lg" alt="logo light">
        </a>
    </div>

    <div class="scrollbar" data-simplebar>

        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">Menu...</li>

            @php
                $isAdmin = auth()->check() && optional(Auth::user())->role === 'admin';
                $hasReferenceId = !empty($reference_id);
            @endphp
            @if ($isAdmin)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('any', 'index') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:widget-2-outline"></iconify-icon>
                        </span>
                        <span class="nav-text"> Dashboard </span>
                        <span class="badge bg-primary badge-pill text-end">New</span>
                    </a>
                </li>
                {{-- Donors --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('third', ['admin', 'donors', 'index']) }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:user-heart-outline"></iconify-icon>
                        </span>
                        <span class="nav-text"> Donors </span>
                    </a>
                </li>
                {{-- Donations --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('third', ['admin', 'donations', 'index']) }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:hand-money-outline"></iconify-icon>
                        </span>
                        <span class="nav-text"> Donations </span>
                    </a>
                </li>
                {{-- Transactions --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('third', ['admin', 'transactions', 'index']) }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:card-outline"></iconify-icon>
                        </span>
                        <span class="nav-text"> Transactions </span>
                    </a>
                </li>

                {{-- Special Donations --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('third', ['admin', 'special-donations', 'index']) }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:wallet-money-linear"></iconify-icon>
                        </span>
                        <span class="nav-text"> Special Donations </span>
                    </a>
                </li>
            @elseif ($hasReferenceId)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('user.donations', ['reference_id' => $reference_id]) }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:hand-money-outline"></iconify-icon>
                        </span>
                        <span class="nav-text"> My Donations </span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
