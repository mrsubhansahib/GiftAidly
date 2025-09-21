@extends('layouts.vertical', ['subtitle' => 'Edit Password'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Password', 'subtitle' => 'Edit'])
    <livewire:admin.change-password.index />
@endsection