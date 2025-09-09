@extends('layouts.vertical', ['subtitle' => 'Subscriptions'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Subscriptions', 'subtitle' => 'List'])
    <livewire:admin.subscriptions/>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>
@endsection
