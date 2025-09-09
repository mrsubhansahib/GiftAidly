@extends('layouts.vertical', ['subtitle' => 'Donors'])

@section('css')
    @vite(['node_modules/gridjs/dist/theme/mermaid.min.css'])
@endsection

@section('content')
    @include('layouts.partials.page-title', [
        'title' => 'Donors',
        'subtitle' => 'List'
    ])

    <livewire:admin.donors />
@endsection

@section('scripts')
    @vite(['resources/js/pages/table-gridjs.js'])
@endsection
