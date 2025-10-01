@extends('layouts.vertical', ['subtitle' => 'Create Special'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Special Donations', 'subtitle' => 'List'])
    <!-- @include('layouts.partials.alert'); -->
    <livewire:admin.special-donations />
@endsection
@push('scripts')
<script>
    window.addEventListener('show-donation-modal', () => {
        var myModal = new bootstrap.Modal(document.getElementById('donationModal'));
        myModal.show();
    });

    window.addEventListener('hide-donation-modal', () => {
        var myModalEl = document.getElementById('donationModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        if (modal) {
            modal.hide();
        }
    });
</script>
@endpush
