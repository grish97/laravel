<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/png" href="{{asset('/images/laravel.png')}}"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <link rel="stylesheet" href="{{asset('/css/toastr.css')}}">
        <link rel="stylesheet" href="{{asset('/css/main.css')}}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('/css/bootstrap.min.css')}}">
        @stack('header-post-scripts')
    </head>
    <body>
        {{--NAVBAR--}}
        <nav class="navbar navbar-dark bg-dark">
            <a href="/" class="navbar-brand">Home</a>
        </nav>
        {{--SECTION--}}
        @yield('content')
        {{--SCRIPTS--}}
        <script src="{{asset('/js/app.js')}}"></script>
        <script src="{{asset('/js/main.js')}}"></script>
    </body>
</html>
