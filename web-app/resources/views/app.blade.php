<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
</head>
<body>
<div class="flex-center position-ref full-height">

    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    @endif

    <div class="row bg-primary">
        <div class="col offset-1">
            <img src="{{ asset('logo-banner.svg') }}" height="80px">
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
