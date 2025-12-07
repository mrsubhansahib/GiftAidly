<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('components.layouts.head')

    {{-- 🔥 REQUIRED FOR LIVEWIRE --}}
    @livewireStyles
</head>

<body>
    @include('layouts.partials.loader')
    {{-- MAIN CONTENT --}}
    {{ $slot }}

    @include('components.layouts.foot')

    {{-- 🔥 REQUIRED FOR LIVEWIRE --}}
    @livewireScripts

</body>

</html>
