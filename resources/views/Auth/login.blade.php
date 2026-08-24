<!DOCTYPE html>
<html lang="id">

@include('Auth.Partials.head', ['title' => 'Login & Registrasi - SAPA MS ACEH'])

<body>

    <div id="container" class="container-auth">
        <!-- FORM SECTION -->
        <div class="row">
            
            <!-- SIGN UP FORM -->
            <div class="col align-items-center flex-col sign-up">
                <div class="form-wrapper align-items-center">
                    <div class="form sign-up">
                        
                        <!-- Header Logo -->
                        <div class="text-center mb-3">
                            <h3 class="fw-bold text-success mb-0">Daftar Akun</h3>
                            <small class="text-muted">Buat akun baru SAPA MS ACEH</small>
                        </div>

                        <form action="{{ url('/register') }}" method="POST">
                            @csrf
                            <div class="input-group-custom">
                                <i class='bx bxs-user'></i>
                                <input type="text" name="name" placeholder="Nama Lengkap" required>
                            </div>
                            <div class="input-group-custom">
                                <i class='bx bxs-id-card'></i>
                                <input type="text" name="username" placeholder="Username / NIP" required>
                            </div>
                            <div class="input-group-custom">
                                <i class='bx bx-mail-send'></i>
                                <input type="email" name="email" placeholder="Alamat Email" required>
                            </div>
                            <div class="input-group-custom">
                                <i class='bx bxs-lock-alt'></i>
                                <input type="password" name="password" placeholder="Password" required>
                            </div>
                            <button type="submit" class="btn-auth">
                                Daftar Sekarang
                            </button>
                        </form>

                        <p>
                            <span>Sudah memiliki akun?</span>
                            <b onclick="toggle()" class="pointer">Masuk di sini</b>
                        </p>
                    </div>
                </div>
            </div>
            <!-- END SIGN UP FORM -->

            <!-- SIGN IN FORM -->
            <div class="col align-items-center flex-col sign-in">
                <div class="form-wrapper align-items-center">
                    <div class="form sign-in">
                        
                        <!-- Header Brand Logo -->
                        <div class="d-flex align-items-center justify-content-center mb-3">                        
                            <div class="text-start">
                                <h2 class="h5 fw-bold mb-0" style="color: #0b6e39;">SAPA <span style="color: #10b981;">MS ACEH</span></h2>
                            </div>
                        </div>

                        <h5 class="text-secondary fw-normal mb-3" style="font-size: 0.95rem;">Masuk ke Akun Anda</h5>

                        <!-- Alert Error Validation -->
                        @if ($errors->any())
                            <div class="alert alert-danger p-2 text-start mb-3" style="font-size: 0.75rem; border-radius: 0.5rem;">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login.process') }}" method="POST">
                            @csrf
                            <div class="input-group-custom">
                                <i class='bx bxs-user'></i>
                                <input type="text" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus>
                            </div>
                            <div class="input-group-custom">
                                <i class='bx bxs-lock-alt'></i>
                                <input type="password" name="password" placeholder="Password" required>
                            </div>

                            <!-- Tombol Masuk -->
                            <button type="submit" class="btn-auth mt-2">
                                Masuk Sekarang
                            </button>

                            <!-- Link Lupa Password -->
                            <div class="text-center mt-2">
                                <a href="#" class="text-decoration-none" style="color: #0b6e39; font-size: 0.8rem;">Lupa password?</a>
                            </div>
                        </form>

                        <p class="mt-3">
                            <span>Belum punya akun?</span>
                            <b onclick="toggle()" class="pointer">Daftar di sini</b>
                        </p>

                    </div>
                </div>
            </div>
            <!-- END SIGN IN FORM -->

        </div>
        <!-- END FORM SECTION -->

        <!-- CONTENT SECTION OVERLAY -->
        <div class="row content-row">
            <!-- SIGN IN CONTENT OVERLAY -->
            <div class="col align-items-center flex-col">
                <div class="text sign-in">
                    <h2>Selamat Datang!</h2>
                    <p>Sistem Aplikasi Pelayanan & Informasi<br>Mahkamah Syar'iyah Aceh</p>
                </div>
            </div>

            <!-- SIGN UP CONTENT OVERLAY -->
            <div class="col align-items-center flex-col">
                <div class="text sign-up">
                    <h2>Buat Akun Anda</h2>
                    <p>Layanan terintegrasi komunikasi lintas<br>Mahkamah Syar'iyah</p>
                </div>
            </div>
        </div>
        <!-- END CONTENT SECTION OVERLAY -->
    </div>

    <!-- Script Assets -->
    @include('Auth.Partials.script')

    <!-- Custom Script Toggle CodePen -->
    <script>
        let container = document.getElementById('container')

        toggle = () => {
            container.classList.toggle('sign-in')
            container.classList.toggle('sign-up')
        }

        setTimeout(() => {
            container.classList.add('sign-in')
        }, 200)
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- 3. Listener Session Laravel -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}"                                    
                });
            @endif
        });
    </script>
</body>
</html>