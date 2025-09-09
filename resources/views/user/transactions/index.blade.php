@extends('layouts.vertical', ['subtitle' => 'Transactions'])

@section('css')
    @vite(['node_modules/gridjs/dist/theme/mermaid.min.css'])
@endsection

@section('content')
    @include('layouts.partials.page-title', [
        'title' => 'Transactions',
        'subtitle' => 'List'
    ])

    <livewire:user.transactions />
@endsection

@section('scripts')
    @vite(['resources/js/pages/table-gridjs.js'])
@endsection
