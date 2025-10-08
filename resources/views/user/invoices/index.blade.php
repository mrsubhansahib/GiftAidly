@extends('layouts.vertical', ['subtitle' => 'Invoices'])

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Invoices', 'subtitle' => 'List'])
    <livewire:user.invoices />
@endsection

