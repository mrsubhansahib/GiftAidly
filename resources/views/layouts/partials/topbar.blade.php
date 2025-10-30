<header class="app-topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center gap-2">
                <!-- Menu Toggle Button -->
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu topbar-button">
                        <iconify-icon icon="solar:hamburger-menu-outline" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- App Search-->
                <form class="app-search d-none d-md-block me-auto">
                    <div class="position-relative">
                        <input type="search" class="form-control" placeholder="admin,widgets..." autocomplete="off"
                            value="">
                        <iconify-icon icon="solar:magnifer-outline" class="search-widget-icon"></iconify-icon>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if (auth()->check() && auth()->user()->role === 'donor')
                <div class="topbar-item">
                    <a type="button" href="{{ route('second', ['donation', 'index']) }}"
                        style="background: linear-gradient(45deg, #1d43ab, #f9c001); 
                        background-size: 200% 200%;
                        background-position: left center;
                        border: none; 
                        color: white;
                        font-weight: 600; 
                        padding: 6px 18px; 
                        border-radius: 25px; 
                        display: flex; 
                        align-items: center; 
                        cursor: pointer;
                        transition: background-position 0.5s ease-in-out, transform 0.3s ease;
                        "
                        onmouseover="this.style.backgroundPosition='right center'; this.style.transform='scale(1.05)';"
                        onmouseout="this.style.backgroundPosition='left center'; this.style.transform='scale(1)';">

                        <iconify-icon icon="ph:hand-heart" class="fs-20 align-middle"
                            style="margin-right: 6px;"></iconify-icon>
                        Donate Now
                    </a>
                </div>
                @endif



                <!-- Theme Color (Light/Dark) -->
                <div class="topbar-item">
                    <button type="button" class="topbar-button" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-outline" class="fs-22 align-middle light-mode"></iconify-icon>
                        <iconify-icon icon="solar:sun-2-outline" class="fs-22 align-middle dark-mode"></iconify-icon>
                    </button>
                </div>

                <!-- Notification -->
                <div class="dropdown topbar-item">
                    <button type="button" class="topbar-button position-relative"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <iconify-icon icon="solar:bell-bing-outline" class="fs-22 align-middle"></iconify-icon>

                        @php
                        use App\Models\User;
                        if (auth()->check() && auth()->user()->role === 'admin') {
                        $notificationCount = auth()->user()
                        ->unreadNotifications()
                        ->whereJsonContains('data->type', 'admin')
                        ->count();
                        } else {
                        $user = User::where('reference_id', $reference_id)->first();
                        $notificationCount = $user?->unreadNotifications()
                            ->whereJsonContains('data->type', 'user')
                            ->count() ?? 0;
                        }
                        @endphp

                        @if($notificationCount > 0)
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">
                            {{ $notificationCount }}
                        </span>
                        @endif
                    </button>

                    <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end"
                        aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-2 border-bottom bg-light bg-opacity-50">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold">
                                        Notifications ({{ $notificationCount }})
                                    </h6>
                                </div>
                                <div class="col-auto">
                                    <form action="{{ route('notifications.clear') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-dark p-0 text-decoration-underline">
                                            <small>Clear All</small>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div data-simplebar style="max-height: 250px;">
                            @php
                            if (auth()->check() && auth()->user()->role === 'admin') {
                            // Admin ko sirf admin-type notifications dikhani hain
                            $notifications = auth()
                            ->user()
                            ->notifications()
                            ->whereJsonContains('data->type', 'admin')
                            ->latest()
                            ->get();
                            }  else {
                            // Donor notifications (fetched via reference_id)
                            $user = User::where('reference_id', $reference_id)->first();
                            $notifications = $user?->notifications()
                                ->whereJsonContains('data->type', 'user')
                                ->latest()
                                ->get() ?? collect();
                            }
                            @endphp

                            @forelse($notifications as $notification)
                            @php
                            $data = is_array($notification->data ?? null)
                            ? $notification->data
                            : json_decode($notification->data ?? "{}", true);
                            @endphp

                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item w-100 text-start p-2 border-bottom text-wrap {{ isset($notification->read_at) && $notification->read_at ? '' : 'bg-light' }}">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm me-2">
                                                <span class="avatar-title bg-soft-primary text-primary fs-20 rounded-circle">
                                                    <i class="bx bx-bell"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0 fw-medium">{{ $data['title'] ?? 'Notification' }}</p>
                                            <p class="mb-0 text-wrap">{{ $data['message'] ?? '' }}</p>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </button>
                            </form>
                            @empty
                            <div class="text-center p-3 text-muted">No notifications found</div>
                            @endforelse
                        </div>
                    </div>
                </div>



                <!-- User -->
                <div class="dropdown topbar-item">
                    <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle" width="32" src="/images/users/avatar-1.jpg"
                                alt="avatar-3">
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome!</h6>

                        <a class="dropdown-item" href="{{ route('third', ['shared', 'profile', 'show']) }}">
                            <iconify-icon icon="solar:user-outline"
                                class="align-middle me-2 fs-18"></iconify-icon><span class="align-middle">My
                                Account</span>
                        </a>

                        @if (auth()->check() && Auth::user()->role === 'admin')
                        <a class="dropdown-item" href="{{ route('third', ['admin', 'change-password', 'index']) }}">
                            <iconify-icon icon="mdi:form-textbox-password" class="align-middle me-2 fs-18"></iconify-icon>

                            <span class="align-middle">Change Password</span>
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf

                            <button class="dropdown-item text-danger" href="#" type="submit">
                                <iconify-icon icon="solar:logout-3-outline"
                                class="align-middle me-2 fs-18"></iconify-icon><span
                                class="align-middle">Logout</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>