<!DOCTYPE html>
<html lang="en" dir="ltr" class="light">

<head>
    <meta charset="utf-8" />
    <title>{{ __('privacyPolicy')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="RetryTech" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png')}}">
    <!-- Quill css -->
    <link href="{{ asset('assets/vendor/quill/quill.core.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet" type="text/css" />
    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/hyper-config.js')}}"></script>
    <!-- Vendor css -->
    <link href="{{ asset('assets/css/vendor.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Toast css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-toast-plugin/jquery.toast.min.css')}}">
    <!-- App css -->
    <link href="{{ asset('assets/css/app-saas.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />
    <!-- Icons css -->
    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="p-3">
        <?php
        echo $data;
        ?>
    </div>
</body>

</html>