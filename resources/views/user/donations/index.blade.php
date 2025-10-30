@extends('layouts.vertical', ['subtitle' => 'Donations', 'reference_id' => $reference_id])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donations', 'subtitle' => 'List'])
    <livewire:user.donations :reference_id="$reference_id" />
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