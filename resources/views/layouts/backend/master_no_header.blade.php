<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link href="{{ URL::asset('public/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/css/metisMenu.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    @yield('css')
    <style>
        #screen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 99, 71, 0.5);
            /* background: #ff6347; */
            display: flex;
            align-items: center;
            z-index: 100;
            visibility: hidden;
        }

        #loader {
            width: 100%;
            height: 15px;
            text-align: center;
            /* visibility: hidden; */
        }

        .dot {
            position: relative;
            width: 15px;
            height: 15px;
            margin: 0 2px;
            display: inline-block;
        }

        .dot:first-child:before {
            animation-delay: 0ms;
        }

        .dot:first-child:after {
            animation-delay: 0ms;
        }

        .dot:last-child:before {
            animation-delay: 200ms;
        }

        .dot:last-child:after {
            animation-delay: 200ms;
        }

        .dot:before {
            content: "";
            position: absolute;
            left: 0;
            width: 15px;
            height: 15px;
            background-color: blue;
            animation-name: dotHover;
            animation-duration: 900ms;
            animation-timing-function: cubic-bezier(.82, 0, .26, 1);
            animation-iteration-count: infinite;
            animation-delay: 100ms;
            background: white;
            border-radius: 100%;
        }

        .dot:after {
            content: "";
            position: absolute;
            z-index: -1;
            background: black;
            box-shadow: 0px 0px 1px black;
            opacity: .20;
            width: 100%;
            height: 3px;
            left: 0;
            bottom: -2px;
            border-radius: 100%;
            animation-name: dotShadow;
            animation-duration: 900ms;
            animation-timing-function: cubic-bezier(.82, 0, .26, 1);
            animation-iteration-count: infinite;
            animation-delay: 100ms;
        }

        @keyframes dotShadow {
            0% {
                transform: scaleX(1);
            }

            50% {
                opacity: 0;
                transform: scaleX(.6);
            }

            100% {
                transform: scaleX(1);
            }
        }

        @keyframes dotHover {
            0% {
                top: 0px;
            }

            50% {
                top: -50px;
                transform: scale(1.1);
            }

            100% {
                top: 0;
            }
        }
    </style>
</head>
<body class="dark-sidenav">
    <div class="modal fade modalLoading" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-body text-center">
                <div style="align-items: center">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @yield('content')
    </div>
    {{-- @include('layouts.backend.footer') --}}
    <script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/waves.js') }}"></script>
    <script src="{{ asset('public/assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/moment.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('public/assets/js/app.js') }}"></script>
    @yield('script')
</body>
</html>