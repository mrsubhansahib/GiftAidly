<!DOCTYPE html>
<html @yield('html-attribute')>

<head>
    @include('layouts.partials/title-meta')

    @include('layouts.partials/head-css')
</head>

<body @yield('body-attribuet')>

    @include('layouts.partials.loader')

    @yield('content')

    @include('layouts.partials/vendor-scripts')
    @include('layouts.partials.alert') {{-- <-- added --}}

</body>

</html>
