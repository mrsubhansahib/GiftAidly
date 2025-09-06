@extends('layouts.vertical', ['subtitle' => 'Subscriptions'])

@section('css')
    @vite(['node_modules/gridjs/dist/theme/mermaid.min.css'])
@endsection

@section('content')
    @include('layouts.partials.page-title', [
        'title' => 'Subscriptions',
        'subtitle' => 'List'
    ])

    <livewire:admin.subscriptions />
@endsection

@section('scripts')
    @vite(['resources/js/pages/table-gridjs.js'])
@endsection
