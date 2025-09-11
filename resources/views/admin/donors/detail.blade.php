@extends('layouts.vertical', ['subtitle' => 'Donor'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donor', 'subtitle' => 'Detail'])
    <livewire:admin.donor.detail :id="$id" />
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#subscriptions-table').DataTable();
            $('#invoices-table').DataTable();
            $('#transactions-table').DataTable();
        });
    </script>
@endsection
