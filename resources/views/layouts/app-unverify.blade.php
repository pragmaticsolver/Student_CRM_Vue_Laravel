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
    <script src="{{ asset('/js/lang.js') }}"></script>
    @stack('scripts')

    <!-- Styles -->
    <link rel="shortcut icon" href="{{ \Storage::disk('public')->url('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" />

    <link href="{{ asset('public'.mix('css/app.css')) }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    @stack('styles')
</head>
<body class="top-navigation fixed-nav pace-done">
<div id="wrapper">
    <header>
        <nav class="navbar navbar-expand-lg fixed-top">
            @php $role = Auth::user()->get_role(); @endphp
            <a class="navbar-brand"
                    href="{{ isset($role) ? url($role->login_redirect_path) : url('/') }}">{{ App\Settings::get_value('school_name') }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <i class="fa fa-reorder"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav mr-auto" id="main-menus">
                </ul>
                <!-- Right Side Of Navbar -->
                @include('partials.user-profile')
            </div>
        </nav>
    </header>

    <div id="page-wrapper" class="gray-bg">
        <div class="wrapper wrapper-content animated fadeIn">
            @yield('content')
        </div>
    </div>
    <footer>
        <div class="pull-right">
            <strong>100ピー</strong>
        </div>
        <div class="col-sm-3 pull-right">
            <select class="form-control" data-url="{{route('change-language')}}" id="changeLanguage">
                <option value="en" <?php if(app()->getLocale() == 'en') echo 'selected'; ?>>English</option>
                <option value="ja" <?php if(app()->getLocale() == 'ja') echo 'selected'; ?>>Japanese</option>
            </select>
        </div>
        <div>
            <strong>Copyright</strong> uTeach &copy; 2018-<?php echo \Carbon\carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('Y'); ?>
        </div>
    </footer>
</div>
@stack('modals')
</body>
</html>
