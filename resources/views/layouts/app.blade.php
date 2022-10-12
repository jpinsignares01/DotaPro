<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DotaPro') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?v={{ env('APP_VERSION',1.10) }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}?v={{ env('APP_VERSION',1.10) }}" rel="stylesheet">

    <!-- Additional per-page css -->
    @yield('css')
</head>
<body>
    <div id="app">

        <main>
            @yield('content')
        </main>

    </div>

    <!-- Include per-page JS -->
    @yield('js')
</body>
</html>