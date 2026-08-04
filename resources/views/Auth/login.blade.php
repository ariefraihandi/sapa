<!DOCTYPE html>
<html lang="en">

@include('Auth.Partials.head', ['title' => 'Login - SAPA-MS'])

<body class="vh-100">

    <!-- Start - Authincation Section -->
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="card p-5 shadow-lg">
                        <div class="text-center mb-3">
                            <a href="#" class="brand-logo d-flex align-items-center justify-content-center text-decoration-none" aria-label="SAPA-MS">
                                <!-- SVG LOGO: Balon Chat + Lambaian Tangan (Sapa) -->
                                <svg class="logo-abbr" width="53" height="53" viewBox="0 0 53 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Background Balon Chat -->
                                    <path d="M0.730591 20.2078C0.730591 9.16207 9.6849 0.207764 20.7306 0.207764H32.7306C43.7763 0.207764 52.7306 9.16207 52.7306 20.2078V36.2078C52.7306 42.2078 46.7306 47.2078 40.7306 47.2078H28.7306L14.7306 52.2078V47.2078C5.73059 46.2078 0.730591 40.2078 0.730591 32.2078V20.2078Z" fill="url(#blue_gradient_sapa)"/>
                                    
                                    <!-- Ikon Tangan Melambai (Waving Hand) -->
                                    <g fill="white">
                                        <!-- Telapak Tangan & Jari-jari -->
                                        <path d="M22 36c-1.5 0-3-1.2-3.5-2.7L16 25c-.3-.9.2-1.9 1.1-2.2.9-.3 1.9.2 2.2 1.1l2.1 6.3c.1.3.5.4.8.2.3-.2.4-.5.2-.8l-4.7-12c-.3-.9.1-2 1-2.3.9-.3 2 .1 2.3 1l4.1 10.2c.1.3.5.4.8.2.3-.2.4-.5.2-.8L22 14.5c-.3-.9.2-1.9 1.1-2.2.9-.3 1.9.2 2.2 1.1l3.8 11.4c.1.3.5.4.8.2.3-.2.4-.5.2-.8l-2.1-7.3c-.3-.9.3-1.9 1.2-2.1.9-.2 1.9.3 2.1 1.2l3.4 12c.1.4.5.6.9.4.3-.1.5-.5.4-.9l-.8-4.3c-.2-.9.4-1.8 1.4-2 .9-.2 1.8.4 2 1.4l1.7 8.5c.6 3.1-1 6.2-3.9 7.4l-7.4 3c-2.1.9-4.3 1.2-6.4 1.2z"/>
                                        <!-- Garis Efek Lambaian/Sapaan (Motion Lines) -->
                                        <path d="M39 12c1.5 1.5 2.5 3.5 2.5 5.5M43 9c2.5 2.5 4 5.5 4 9" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                    </g>
                                    
                                    <!-- Definisi Gradasi Biru -->
                                    <defs>
                                        <linearGradient id="blue_gradient_sapa" x1="26.7306" y1="0.207765" x2="55.2306" y2="56.2078" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#0B2A97"/> <!-- Biru Tua Utama -->
                                            <stop offset="1" stop-color="#0066FF"/> <!-- Biru Muda Terang -->
                                        </linearGradient>
                                    </defs>
                                </svg>

                                <!-- Teks Nama Aplikasi (Warna Biru Instansi `#0B2A97`) -->
                                <span class="fs-3 fw-extrabold ms-3" style="color: #0B2A97; font-family: 'Poppins', sans-serif; letter-spacing: 1px;">
                                    SAPA<span style="color: #0066FF;">-MS</span>
                                </span>
                            </a>
                        </div>
                        
                        <h4 class="text-center mb-4">Sign in your account</h4>

                        <!-- Menampilkan Pesan Error Global Validasi -->
                        @if ($errors->any())
                            <div class="alert alert-danger p-2 small">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ url('/login') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Username / NIP</strong></label>
                                <input type="text" name="username" class="form-control form-control-lg" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Password</strong></label>
                                <div class="position-relative">
                                    <input type="password" name="password" autocomplete="current-password" class="form-control form-control-lg dz-password" placeholder="Enter your password" required>
                                    <span class="show-pass position-absolute top-50 end-0 me-2 translate-middle">
                                        <span class="show"><i class="fa fa-eye-slash"></i></span>
                                        <span class="hide"><i class="fa fa-eye"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="form-row d-flex justify-content-between mt-4 mb-2 flex-wrap">
                                <div class="form-group mb-3">
                                   <div class="custom-control custom-checkbox ms-1">
                                        <input type="checkbox" name="remember" class="form-check-input" id="basic_checkbox_1">
                                        <label class="form-check-label" for="basic_checkbox_1">Remember my preference</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <a href="#">Forgot Password?</a>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Sign Me In</button>
                            </div>
                        </form>

                        <!-- Menampilkan Footer Developer BILIKMEDIA -->
                        @include('Auth.Partials.footer')

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End - Authincation Section -->
    
    @include('Auth.Partials.script')
    
</body>
</html>