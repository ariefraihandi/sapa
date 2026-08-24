<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title & Base URL -->
    <title>{{ $title ?? 'SAPA-MS - Sistem Komunikasi & Pelayanan Lintas Mahkamah Syar\'iyah' }}</title>
    <base href="/">

    <!-- SEO & Metadata -->
    <meta name="author" content="BILIKMEDIA">
    <meta name="robots" content="noindex, nofollow">
    <meta name="keywords" content="sapa ms, mahkamah syariyah aceh, pelayanan hukum, akta cerai">
    <meta name="description" content="SAPA-MS adalah Sistem Komunikasi & Pelayanan Lintas Mahkamah Syar'iyah se-Aceh.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/sapa.png') }}">

    <!-- Third-Party CSS Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/metismenu/dist/metisMenu.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}">

    <!-- Custom System Stylesheets -->
    <link rel="stylesheet" class="main-switcher" href="{{ asset('assets/css/switcher.css') }}">
    <link rel="stylesheet" class="main-plugins" href="{{ asset('assets/css/plugins.css') }}">
    <link rel="stylesheet" class="main-css" href="{{ asset('assets/css/style.css') }}">
    <style>
        .swal2-container .bootstrap-select {
            display: none !important;
        }

        /* Override CSS Bawaan Template untuk Profile & Notification Dropdown */
        .header-profile .dropdown-menu,
        .notification_dropdown .dropdown-menu {
            top: 100% !important;
            transform: none !important;
            margin-top: 10px !important;
            right: 0 !important;
            left: auto !important;
        }

        /* Pastikan container li menjadi acuan posisi absolut */
        .header-profile,
        .notification_dropdown {
            position: relative !important;
        }
    </style>
</head>