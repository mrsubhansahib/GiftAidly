<!DOCTYPE html>
<html lang="en" @yield('html-attribute')>

<head>
    @include('layouts.partials/title-meta')

    @include('layouts.partials/head-css')
</head>

<body>

    @include('layouts.partials.loader')

    <div class="app-wrapper">

        @include('layouts.partials/sidebar')

        @include('layouts.partials/topbar')

        <div class="page-content">

            <div class="container-fluid">

                @yield('content')

            </div>

            @include('layouts.partials/footer')
        </div>

    </div>

    @include('layouts.partials/vendor-scripts')

    @include('layouts.partials.alert')
</body>

</html>