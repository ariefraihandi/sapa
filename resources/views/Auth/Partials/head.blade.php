<head>
    <!-- Title -->
    <title>{{ $title ?? 'SAPA-MS - Sistem Komunikasi & Pelayanan Lintas Mahkamah Syar\'iyah' }}</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="BILIKMEDIA">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.avif') }}">
    
    <!-- Start - Basic CSS -->
    <link href="{{ asset('assets/vendor/metismenu/dist/metisMenu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}">
    
    <!-- Start - Switcher CSS -->
    <link class="main-switcher" href="{{ asset('assets/css/switcher.css') }}" rel="stylesheet">

    <!-- Start - Style Css -->
    <link class="main-plugins" href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('assets/css/style.css') }}" rel="stylesheet">    
</head>