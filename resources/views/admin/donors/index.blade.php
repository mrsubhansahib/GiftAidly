@extends('layouts.vertical', ['subtitle' => 'Donors'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donors', 'subtitle' => 'List'])
    <livewire:admin.donors />
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>
@endsection
