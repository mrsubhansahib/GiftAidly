@extends('layouts.vertical', ['subtitle' => 'Donors'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donors', 'subtitle' => 'List'])
    <livewire:admin.donors />
@endsection


