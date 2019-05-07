<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ECAS</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
</head>
<body class="d-flex flex-column h-100">
    <main class="flex-shrink-0">

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
                <img src="/logo-banner.svg" height="80px">
            </div>
            <div class="col text-right text-light">
                <h1>Ministry of Education</h1>
                <p>Electronic Contract Administration System</p>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
    <footer class="footer mt-auto">
        <div class="container">
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/disclaimer">Disclaimer</a></li>
                <li><a href="/privacy">Privacy</a></li>
                <li><a href="/accessibility">Accessibility</a></li>
                <li><a href="/copyright">Copyright</a></li>
                <li><a href="/contact">Contact Us</a></li>
            </ul>
        </div>
    </footer>

<script src="/js/app.js"></script>
{{--<script src="{{ asset('js/app.js') }}"></script>--}}

</body>
</html>
