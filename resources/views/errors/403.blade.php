<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>uTeach Admin Section</title>

    <!-- Scripts -->
    <script src="{{ asset('public/js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('public'.mix('css/app.css')) }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
</head>
<body class="gray-bg">
    <div class="middle-box text-center animated fadeInDown">
        <h1>403</h1>
        <h3 class="font-bold">Access Denied</h3>

        <div class="error-desc">
            You don't have permission to access this page.
            Kindly contact with your administrator.
        </div>
        <br>
        <br>
        <p>Redirecting to home page</p>
    </div>
</body>
</html>

@php
    $redirectUrl = \URL::to('/');
    $user_role = @\Auth::user()->get_role();
    if($user_role)
    {
        $redirectUrl = \URL::to($user_role->login_redirect_path);
    }
@endphp

<script>
window.redirectUrl = '{{ $redirectUrl }}'
window.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        window.location.href = redirectUrl;
    }, 2000);
});
</script>
