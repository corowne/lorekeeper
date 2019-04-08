<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Lorekeeper') }} - @yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/site.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/lorekeeper.css') }}" rel="stylesheet">

    {{-- Font Awesome --}}
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">

    {{-- jQuery UI --}}
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <div class="site-header-image" id="header" style="background-image: url('{{ asset('images/header.png') }}');"></div>
        @include('layouts._nav')

        <main class="container-fluid">
            <div class="row">
            
                <div class="sidebar col-lg-2">
                    @yield('sidebar')
                </div>
                <div class="main-content col-lg-8 p-4">
                    @include('flash::message')
                    <div class="container">
                        @yield('content')
                    </div>
                    
                    <div class="site-footer mt-4" id="footer">
                            @include('layouts._footer')
                    </div>
                </div>
            </div>
        
        </main>

        
        <div class="modal fade" id="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>

        @yield('scripts')
        <script>$('[data-toggle="tooltip"]').tooltip();</script>
    </div>
</body>
</html>
