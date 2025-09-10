@extends('layouts.vertical', ['subtitle' => 'Donations'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donations', 'subtitle' => 'List'])
    <livewire:admin.donations />
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>
@endsection
