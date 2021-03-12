<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BC Ministry of Education - ECAS</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
</head>
<body class="d-flex flex-column h-100">
    <main class="flex-shrink-0">
        <div class="row bg-primary pt-2">
            <div class="col offset-1">
                <img src="/logo-banner.svg" height="80px">
            </div>
            <div class="col text-right text-light">
                <h1>Ministry of Education</h1>
                <p>Electronic Contract Administration System (ECAS)</p>
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
                <li><a href="https://www2.gov.bc.ca/">Home</a></li>
                <li><a href="https://www2.gov.bc.ca/gov/content/home/disclaimer">Disclaimer</a></li>
                <li><a href="https://www2.gov.bc.ca/gov/content/home/privacy">Privacy</a></li>
                <li><a href="https://www2.gov.bc.ca/gov/content/home/accessibility">Accessibility</a></li>
                <li><a href="https://www2.gov.bc.ca/gov/content/home/copyright">Copyright</a></li>
                <li><a href="https://www2.gov.bc.ca/gov/content/home/contact-us">Contact Us</a></li>
           </ul>
        </div>
    </footer>

<script src="/js/app.js"></script>

</body>
</html>
