<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=to-fit">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="origin-when-cross-origin">

    <!-- === TITLE & BASE URL === -->
    <title>{{ $seo['title'] ?? ($title ?? 'Dashboard Admin - SAPA MS ACEH') }}</title>
    <base href="/">

    <!-- === PRIMARY META SEO (ADMIN DASHBOARD DEFAULTS) === -->
    <meta name="title" content="{{ $seo['title'] ?? ($title ?? 'Dashboard Admin - SAPA MS ACEH') }}">
    <meta name="description" content="{{ $seo['description'] ?? 'Panel Kontrol Administrasi & Sistem Komunikasi Pelayanan Terpadu Satu Pintu (PTSP) Mahkamah Syar\'iyah se-Aceh.' }}">
    <meta name="keywords" content="{{ $seo['keywords'] ?? 'sapa ms aceh, admin dashboard, ptsp aceh, mahkamah syariyah' }}">
    <meta name="author" content="{{ $seo['author'] ?? 'Mahkamah Syar\'iyah Aceh' }}">
    
    <!-- Privacy Guard: Default noindex untuk Dashboard Admin -->
    <meta name="robots" content="{{ $seo['robots'] ?? 'noindex, nofollow' }}">

    <!-- === OPEN GRAPH / FACEBOOK / WHATSAPP / TELEGRAM === -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="SAPA MS ACEH Admin">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $seo['title'] ?? ($title ?? 'Dashboard Admin - SAPA MS ACEH') }}">
    <meta property="og:description" content="{{ $seo['description'] ?? 'Panel Kontrol Administrasi & Sistem Komunikasi PTSP Mahkamah Syar\'iyah se-Aceh.' }}">
    <meta property="og:image" content="{{ $seo['image'] ?? asset('assets/images/logo/sapa.png') }}">

    <!-- === TWITTER CARD === -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $seo['title'] ?? ($title ?? 'Dashboard Admin - SAPA MS ACEH') }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? 'Panel Kontrol Administrasi & Sistem Komunikasi PTSP Mahkamah Syar\'iyah se-Aceh.' }}">
    <meta name="twitter:image" content="{{ $seo['image'] ?? asset('assets/images/logo/sapa.png') }}">

    <!-- === FAVICON ICON === -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/sapa.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo/sapa.png') }}">

    <!-- === THIRD-PARTY CSS LIBRARIES === -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/metismenu/dist/metisMenu.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}">

    <!-- === CUSTOM SYSTEM STYLESHEETS === -->
    <link rel="stylesheet" class="main-switcher" href="{{ asset('assets/css/switcher.css') }}">
    <link rel="stylesheet" class="main-plugins" href="{{ asset('assets/css/plugins.css') }}">
    <link rel="stylesheet" class="main-css" href="{{ asset('assets/css/style.css') }}">

    <!-- === CUSTOM PAGE OVERRIDES === -->
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