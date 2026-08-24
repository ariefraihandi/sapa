<!DOCTYPE html>
<html lang="id">

<head>
    <base href="/">
    
    <!-- Title -->
    <title>503 - Layanan Dalam Pemeliharaan | SAPA MS ACEH</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="SAPA MS ACEH">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/sapa.png') }}">
    
    <!-- Start - Basic CSS -->
    <link href="{{ asset('assets/vendor/metismenu/dist/metisMenu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}">
    <!-- End - Basic CSS -->
    
    <!-- Start - Switcher CSS -->
    <link class="main-switcher" href="{{ asset('assets/css/switcher.css') }}" rel="stylesheet">
    <!-- End - Switcher CSS -->

    <!-- Start - Style Css -->
    <link class="main-plugins" href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('assets/css/style.css') }}" rel="stylesheet">    
    <!-- End - Style Css -->
</head>
<body>
    
    <!-- Start - Error Section -->
    <div class="clearfix min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-xl-6 error-page">
                    <div class="error-inner text-center">
						<div class="dz-error text-success fw-bold mb-2" data-text="Dalam Pengerjaan" style="color: #0b6e39 !important; font-size: 4rem; line-height: 1.2;">
							Dalam Pengerjaan
						</div>
						<h2 class="error-head"><i class="fa fa-wrench text-warning me-2"></i> Sedang Dikembangkan</h2>
						<p class="text-muted fs-16">Layanan ini masih dalam tahap pengerjaan dan akan segera hadir.</p>
						<div>
							<a class="btn btn-success text-white fs-16 px-4 py-2" href="{{ url('/') }}" style="background-color: #0b6e39; border-color: #0b6e39;">
								<i class="fa-solid fa-house me-1"></i> Kembali ke Beranda
							</a>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
    <!-- End - Error Section -->

    <!-- Start - Script -->
    <script src="{{ asset('assets/vendor/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/@yaireo/tagify/dist/tagify.js') }}"></script>
    <script src="{{ asset('assets/vendor/metismenu/dist/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart-js/chart.bundle.min.js') }}"></script>
        
    <!-- Script For Custom JS -->
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
</body>
</html>