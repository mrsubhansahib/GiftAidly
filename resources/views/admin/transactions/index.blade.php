@extends('layouts.vertical', ['subtitle' => 'Transactions'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Transactions', 'subtitle' => 'List'])
    <livewire:admin.transactions />
@endsection

