<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('messages.uteach') }} @yield('title', __('messages.login'))</title>

    <!-- Scripts -->
    <script src="{{ asset('public/js/app.js') }}" defer></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.4.0/dist/min/dropzone.min.js"></script>
    <!-- <script src="{{ asset('/js/lang.js') }}"></script> -->
    <script src="{{ route('assets.lang')  }}"></script>
    @stack('scripts')

    <!-- Styles -->
    <link rel="shortcut icon" href="{{ \Storage::disk('public')->url('favicon.ico') }}">
    <link href="{{ asset('public'.mix('css/app.css')) }}" rel="stylesheet">
    <link href="{{ asset('public/css/space.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.4.0/dist/min/dropzone.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
    @stack('styles')
</head>
<body class="gray-bg">
    @yield('content')
</body>
</html>
