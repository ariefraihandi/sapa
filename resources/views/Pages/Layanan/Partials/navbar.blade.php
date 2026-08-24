<nav class="navbar">
    <!-- Brand Logo & Judul -->
    <a href="{{ url('/') }}" class="brand-logo">
        <img src="{{ asset('assets/images/logo/sapa.png') }}" alt="SAPA MS ACEH Logo" class="brand-img">
        <div class="brand-text">
            <h1>SAPA-MS ACEH<span>Sistem Aplikasi Pelayanan & Informasi</span></h1>
        </div>
    </a>

    <!-- Menu Navigasi Layanan Utama -->
    <div class="nav-menu">
        <a href="{{ url('/layanan/validasi-akta') }}" class="nav-item">
            <i class="fa-solid fa-file-circle-check"></i> Validasi Akta
        </a>
        <a href="{{ url('/layanan/legalisir-akta') }}" class="nav-item">
            <i class="fa-solid fa-stamp"></i> Legalisir
        </a>
        <a href="{{ url('/layanan/persyaratan-perkara') }}" class="nav-item">
            <i class="fa-solid fa-list-check"></i> Persyaratan Perkara
        </a>

        <!-- Dropdown Menu Aplikasi Lainnya -->
        <div class="nav-dropdown">
            <button class="nav-dropdown-btn">
                <i class="fa-solid fa-cubes"></i> Lainnya <i class="fa-solid fa-chevron-down text-xs"></i>
            </button>
            <div class="dropdown-content">
                <a href="{{ url('/layanan/cek-berkas') }}">
                    <i class="fa-solid fa-magnifying-glass text-primary"></i> Cek Berkas Mandiri
                </a>
                <a href="{{ url('/layanan/helpdesk') }}">
                    <i class="fa-solid fa-headset text-warning"></i> Helpdesk Lintas Satker
                </a>
                <div class="dropdown-divider"></div>
                <a href="https://ecourt.mahkamahagung.go.id" target="_blank">
                    <i class="fa-solid fa-globe text-info"></i> e-Court MA RI
                </a>
            </div>
        </div>
    </div>

    <!-- Area Kanan: Badge & Auth -->
    <div class="nav-right">
        <div class="domain-badge">
            <i class="fa-solid fa-globe"></i> sapa.ms-aceh.go.id
        </div>

        @auth
            <!-- Jika User Sudah Login -->
            <a href="{{ url('/pengguna/profile') }}" class="btn-auth btn-profile">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('assets/images/profile/' . Auth::user()->avatar) }}" class="user-avatar" alt="Avatar">
                @else
                    <i class="fa-solid fa-user-circle"></i>
                @endif
                <span>{{ Str::words(Auth::user()->name, 1, '') }}</span>
            </a>
        @else
            <!-- Jika Pengunjung Belum Login -->
            <a href="{{ route('login') }}" class="btn-auth btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Log In
            </a>
        @endauth
    </div>
</nav>