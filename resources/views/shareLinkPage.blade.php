<!DOCTYPE html>
<html lang="en" dir="ltr" class="light" data-bs-theme="light" data-layout-mode="fluid"
    data-sidenav-size="fullscreen" data-sidenav-size="full" data-layout="topnav">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        {{ $title }}
    </title>

    <meta property="og:title" content="{{ $title }}" />

    <meta property="og:image" content="{{ $thumbUrl }}" />

    <link rel="icon" type="image/x-icon" href="{{ $thumbUrl }}">

    <!-- App favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">

    <link href="{{ asset('assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />

    <!-- Vendor css -->
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/hyper-config.js') }}"></script>

    <!-- App css -->
    <link href="{{ asset('assets/css/app-saas.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <style>
        img.download-bg-img {
            position: absolute;
            opacity: 0.1;
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: blur(20px);
        }

        .download-app-content {
            position: relative;
            z-index: 9;
        }

        .download-section {
            background-image: url("{{ asset('assets/img/download.png') }}");
            width: 100%;
            height: 100vh;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }

        .download-mockup-img {
            width: 100%;
            margin: 0 auto;
            padding-top: 100%;
            position: relative;
            display: block;
        }

        .download-mockup-img img {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        @media (max-width: 991px) {
            .navbar-custom {
                background: #000;
            }

            .navbar-custom .navbar {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            .slider-content {
                width: 100%;
            }

            .slider-img {
                margin: 0 auto 20px;
            }

            #drama-list .grid-slide {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 768px) {
            .slider-img {
                margin: 0 auto 20px;
            }

            .drama-episode-section {
                height: auto;
                margin-bottom: 20px;
            }

            #drama-list .grid-slide {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 640px) {

            .content-img,
            .trending-img {
                height: 230px;
            }

            #drama-list .grid-slide {
                grid-template-columns: repeat(2, 1fr);
            }

            .slider-bg-img {
                display: none;
            }
        }
    </style>

</head>


<body class="download-section">
    <div class="wrapper">
        <div class="content-page p-0">
            <div class="content">
                <div class="container-fluid p-0">
                    <div class="page-title-box">
                        <div class="">
                            <div class="container h-100">
                                <div class="row align-items-center py-5 h-100">
                                    <div class="col-12 col-lg-6 mb-4">
                                        <div class="logo-lg mb-3">
                                            <img src="{{ asset('assets/img/logo.png') }}" alt="logo"
                                                class="img-fluid">
                                        </div>
                                        <h1 class="display-5 fw-bold mb-2 text-white">{{ __('Download the App Now') }}
                                        </h1>
                                        <h4 class="display-7 fw-normal mb-3 text-white">
                                            {{ __('Unlock Endless Surprises Inside!') }}</h4>
                                        <div class="d-flex d-md-flex justify-content-md-start">
                                            <a href="{{ $setting->play_store_download_link }}" class="me-2"
                                                target="_blank">
                                                <img src="{{ asset('assets/img/playstore.png') }}" alt="playstore"
                                                    class="img-fluid border rounded-5 px-2 py-1"
                                                    style="background-color: #000;">
                                            </a>
                                            <a href="{{ $setting->app_store_download_link }}" target="_blank">
                                                <img src="{{ asset('assets/img/appstore.png') }}" alt="appstore"
                                                    class="img-fluid border rounded-5 px-2 py-1"
                                                    style="background-color: #000;">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-8 col-lg-6">
                                        <div class="download-mockup-img">
                                            <img src="{{ $thumbUrl }}"
                                                class="d-block mx-lg-auto img-fluid rounded-5" alt="download-app-ss"
                                                loading="lazy">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var appUrl = "{{ $setting->uri_scheme }}://s/{{ $encryptedId }}";
            var fallbackUrl = window.location.href;

            window.location.href = appUrl;

        });
    </script>

</body>

</html>
