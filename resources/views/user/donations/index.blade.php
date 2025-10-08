@extends('layouts.vertical', ['subtitle' => 'Donations'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Donations', 'subtitle' => 'List'])
    <livewire:user.donations />
@endsection

