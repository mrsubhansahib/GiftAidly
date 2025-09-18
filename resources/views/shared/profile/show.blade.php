@extends('layouts.vertical', ['subtitle' => 'Profile'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Profile', 'subtitle' => 'View'])
    @include('layouts.partials.alert')
    <livewire:shared.profile />
@endsection

@section('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('profile-updated', (e) => {
                const modalEl = document.getElementById('editProfileModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
                // ✅ Toast show directly from JS
                if (window.AppToast) {
                    AppToast.show('success', e.message);
                }
            });
        });
    </script>
@endsection
