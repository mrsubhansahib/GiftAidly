@extends('layouts.vertical', ['subtitle' => 'Transactions'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Transactions', 'subtitle' => 'List'])
    <livewire:user.transactions />
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>
@endsection
