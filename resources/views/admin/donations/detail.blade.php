@extends('layouts.vertical', ['subtitle' => 'Donation'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donation', 'subtitle' => 'Detail'])
    <livewire:admin.donation.detail :id="$id" />
@endsection


