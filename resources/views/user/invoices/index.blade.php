@extends('layouts.vertical', ['subtitle' => 'Invoices'])

@section('css')
    @vite(['node_modules/gridjs/dist/theme/mermaid.min.css'])
@endsection

@section('content')
    @include('layouts.partials.page-title', ['title' => 'Invoices', 'subtitle' => 'List'])

    <livewire:user.invoices />
@endsection


@section('scripts')
    @vite(['resources/js/pages/table-gridjs.js'])
@endsection
