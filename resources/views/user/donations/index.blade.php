@extends('layouts.vertical', ['subtitle' => 'Donations'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donations', 'subtitle' => 'List'])
    <livewire:user.donations />
@endsection

@section('scripts')
    <script>
        function confirmCancel(url) {
            if (confirm('Are you sure you want to cancel this donation?')) {
                window.location.href = url;
            }
        }
    </script>
@endsection