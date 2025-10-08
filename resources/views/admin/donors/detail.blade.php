@extends('layouts.vertical', ['subtitle' => 'Donor'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donor', 'subtitle' => 'Detail'])
    <livewire:admin.donor.detail :id="$id" />
@endsection

