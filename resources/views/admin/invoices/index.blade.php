@extends('layouts.vertical', ['subtitle' => 'Invoices'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Invoices', 'subtitle' => 'List'])
    <livewire:admin.invoices />
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>
@endsection
