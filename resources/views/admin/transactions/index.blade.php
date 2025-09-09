@extends('layouts.vertical', ['subtitle' => 'Transactions'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Transactions', 'subtitle' => 'List'])
    <livewire:admin.transactions />
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>
@endsection
