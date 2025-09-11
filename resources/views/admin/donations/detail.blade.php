@extends('layouts.vertical', ['subtitle' => 'Donation'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donation', 'subtitle' => 'Detail'])
    <livewire:admin.donation.detail :id="$id" />
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#invoices-table').DataTable();
        $('#transactions-table').DataTable();
    });
</script>
@endsection
