<!DOCTYPE html>
<html lang="zxx" dir="ltr" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('termsOfUses')}}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

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
