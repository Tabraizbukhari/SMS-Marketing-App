<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'title') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- Bootstrap core CSS     -->
        <link href="{{  asset('admin/css/bootstrap.min.css') }}" rel="stylesheet" />
        <!-- Animation library for notifications   -->
        <link href="{{  asset('admin/css/animate.min.css') }}" rel="stylesheet"/>
        <!--  Light Bootstrap Table core CSS    -->
        <link href="{{  asset('admin/css/light-bootstrap-dashboard.css?v=1.4.0') }}" rel="stylesheet"/>
        <!--  CSS for Demo Purpose, don't include it in your project     -->
        <link href="{{  asset('admin/css/demo.css') }}" rel="stylesheet" />
        <!--     Fonts and icons     -->
        <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
        <link href="{{ asset('admin/css/pe-icon-7-stroke.css')}}" rel="stylesheet" />
    </head>
    <body class="font-sans antialiased">
    @auth
        @include('dashboard.includes.navigation')
    @endauth
        <div class="content">
            <div class="container-fluid">
                {{ $slot }}
            </div>
        </div>
    </body>
    @auth
        @include('dashboard.includes.script')
    @endauth
</html>
