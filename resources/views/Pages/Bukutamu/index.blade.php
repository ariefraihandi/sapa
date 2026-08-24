<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SAPA MS ACEH - Sistem Layanan & Informasi PTSP Se-Aceh</title>

        <!-- FAVICON ICON (Inisial S + Tangan Menyapa dalam SVG/Data URI) -->
        <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/sapa.png') }}">

        <!-- Google Fonts: Inter & Plus Jakarta Sans -->
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- FontAwesome 6 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            /* === VARIABLES === */
            :root {
                --primary: #047857;
                --primary-light: #10b981;
                --primary-dark: #065f46;
                --bg-accent: #f0fdf4;
                --text-main: #0f172a;
                --text-muted: #64748b;
                --card-shadow: 0 10px 25px -5px rgba(4, 120, 87, 0.08), 0 8px 10px -6px rgba(4, 120, 87, 0.04);
                --card-shadow-hover: 0 20px 30px -10px rgba(4, 120, 87, 0.2);
            }

            /* === RESET === */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Plus Jakarta Sans', sans-serif;
            }

            body {
                background-color: #f8fafc;
                color: var(--text-main);
                min-height: 100vh;
            }

            /* === NAVBAR TOP === */
            .navbar {
                background: #ffffff;
                border-bottom: 1px solid #e2e8f0;
                padding: 0.85rem 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 50;
                box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            }

            .brand-logo {
                display: flex;
                align-items: center;
                gap: 12px;
                text-decoration: none;
            }

            .brand-img {
                width: 42px;
                height: 42px;
                object-fit: contain;
                flex-shrink: 0;
            }

            .brand-text h1 {
                font-size: 1.25rem;
                font-weight: 800;
                color: var(--primary-dark);
                letter-spacing: -0.5px;
                line-height: 1.1;
                margin: 0;
            }

            .brand-text h1 span {
                color: var(--primary-light);
            }

            .brand-text span {
                font-size: 0.725rem;
                color: var(--text-muted);
                font-weight: 600;
                display: block;
            }

            /* === NAV MENU & DROPDOWN === */
            .nav-menu {
                display: flex;
                align-items: center;
                gap: 1.25rem;
            }

            .nav-item {
                color: var(--text-main);
                text-decoration: none;
                font-size: 0.875rem;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 6px;
                transition: color 0.2s ease;
            }

            .nav-item:hover {
                color: var(--primary);
            }

            .nav-item i {
                color: var(--primary-light);
            }

            /* Nav Dropdown */
            .nav-dropdown {
                position: relative;
                display: inline-block;
            }

            .nav-dropdown-btn {
                background: none;
                border: none;
                color: var(--text-main);
                font-size: 0.875rem;
                font-weight: 600;
                font-family: inherit;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 0.4rem 0;
            }

            .nav-dropdown-btn:hover {
                color: var(--primary);
            }

            .dropdown-content {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                background-color: #ffffff;
                min-width: 210px;
                box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
                border-radius: 12px;
                border: 1px solid #e2e8f0;
                padding: 0.5rem 0;
                z-index: 100;
            }

            .dropdown-content a {
                color: var(--text-main);
                padding: 0.6rem 1rem;
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.825rem;
                font-weight: 600;
                transition: background 0.2s;
            }

            .dropdown-content a:hover {
                background-color: var(--bg-accent);
                color: var(--primary);
            }

            .dropdown-divider {
                height: 1px;
                background-color: #e2e8f0;
                margin: 0.4rem 0;
            }

            .nav-dropdown:hover .dropdown-content {
                display: block;
            }

            /* === NAV RIGHT & AUTH === */
            .nav-right {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .domain-badge {
                background: var(--bg-accent);
                color: var(--primary);
                padding: 0.35rem 0.85rem;
                border-radius: 50px;
                font-size: 0.8rem;
                font-weight: 600;
                border: 1px solid rgba(16, 185, 129, 0.2);
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .btn-auth {
                text-decoration: none;
                padding: 0.45rem 1rem;
                border-radius: 50px;
                font-size: 0.825rem;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.25s ease;
            }

            .btn-login {
                border: 1.5px solid var(--primary);
                color: var(--primary);
                background: transparent;
            }

            .btn-login:hover {
                background: var(--primary);
                color: #ffffff;
            }

            .btn-profile {
                background: var(--primary);
                color: #ffffff;
                box-shadow: 0 4px 10px rgba(4, 120, 87, 0.2);
            }

            .btn-profile:hover {
                background: var(--primary-dark);
            }

            .user-avatar {
                width: 22px;
                height: 22px;
                border-radius: 50%;
                object-fit: cover;
            }

            .text-xs { font-size: 0.65rem; }
            .text-primary { color: var(--primary); }
            .text-warning { color: #f59e0b; }
            .text-info { color: #06b6d4; }

            /* === HERO SECTION === */
            .hero {
                background: linear-gradient(180deg, #ffffff 0%, var(--bg-accent) 100%);
                padding: 3rem 1.5rem 2.5rem;
                text-align: center;
                border-bottom: 1px solid #e2e8f0;
            }

            .hero-title {
                font-size: 2.25rem;
                font-weight: 800;
                color: var(--text-main);
                margin-bottom: 0.5rem;
            }

            .hero-title span {
                color: var(--primary);
                background: linear-gradient(135deg, var(--primary), var(--primary-light));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .hero-subtitle {
                color: var(--text-muted);
                font-size: 1rem;
                max-width: 600px;
                margin: 0 auto 1.75rem;
                line-height: 1.5;
            }

            .search-box {
                max-width: 480px;
                margin: 0 auto;
                position: relative;
            }

            .search-box input {
                width: 100%;
                padding: 0.9rem 1.25rem 0.9rem 3rem;
                border-radius: 50px;
                border: 1.5px solid #cbd5e1;
                outline: none;
                font-size: 0.95rem;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .search-box input:focus {
                border-color: var(--primary-light);
                box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
            }

            .search-box i {
                position: absolute;
                left: 1.25rem;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                font-size: 1.1rem;
            }

            /* === CONTAINER & GRID === */
            .main-container {
                max-width: 1200px;
                margin: 2.5rem auto 4rem;
                padding: 0 1.5rem;
            }

            .grid-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 1.75rem;
            }

            /* === CARD STYLING === */
            .satker-card {
                background: #ffffff;
                border-radius: 20px;
                padding: 1.75rem;
                border: 1px solid #f1f5f9;
                box-shadow: var(--card-shadow);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: relative;
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .satker-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 5px;
                background: linear-gradient(90deg, var(--primary), var(--primary-light));
                opacity: 0.8;
                transition: opacity 0.3s ease;
            }

            .satker-card:hover {
                transform: translateY(-6px);
                box-shadow: var(--card-shadow-hover);
            }

            .satker-card:hover::before {
                opacity: 1;
            }

            .card-header-icon {
                width: 52px;
                height: 52px;
                background: var(--bg-accent);
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--primary);
                font-size: 1.4rem;
                margin-bottom: 1.25rem;
                transition: all 0.3s ease;
            }

            .satker-card:hover .card-header-icon {
                background: var(--primary);
                color: #ffffff;
            }

            .satker-title {
                font-size: 1.2rem;
                font-weight: 700;
                color: var(--text-main);
                margin-bottom: 0.35rem;
                line-height: 1.3;
            }

            .satker-region {
                font-size: 0.85rem;
                color: var(--text-muted);
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 6px;
                margin-bottom: 1.5rem;
            }

            .satker-region i {
                color: var(--primary-light);
            }

            .btn-wa {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
                color: white;
                text-decoration: none;
                padding: 0.8rem 1.25rem;
                border-radius: 12px;
                font-weight: 600;
                font-size: 0.925rem;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                box-shadow: 0 4px 12px rgba(4, 120, 87, 0.2);
                transition: all 0.25s ease;
            }

            .btn-wa:hover {
                background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
                box-shadow: 0 6px 16px rgba(4, 120, 87, 0.35);
                transform: scale(1.02);
            }

            .btn-wa i {
                font-size: 1.15rem;
            }

            /* === FOOTER === */
            footer {
                text-align: center;
                padding: 2rem 1rem;
                color: var(--text-muted);
                font-size: 0.85rem;
                border-top: 1px solid #e2e8f0;
                background: #ffffff;
            }

            @media (max-width: 992px) {
                .nav-menu { display: none; }
            }

            @media (max-width: 640px) {
                .navbar { padding: 0.85rem 1rem; }
                .domain-badge { display: none; }
                .hero-title { font-size: 1.75rem; }
            }
        </style>
    </head>
    <body>

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

        <!-- HERO SECTION -->
        <section class="hero">
            <h1 class="hero-title">Pusat Layanan PTSP <span>Mahkamah Syar'iyah</span> se-Aceh</h1>
        <p class="hero-subtitle">Terhubung langsung dengan petugas Pelayanan Terpadu Satu Pintu (PTSP) Mahkamah Syar'iyah se-Provinsi Aceh via WhatsApp.</p>
            
            <!-- Search Bar -->
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" onkeyup="filterSatker()" placeholder="Cari nama MS atau Kabupaten/Kota...">
            </div>
        </section>

        <!-- MAIN DIRECTORY GRID -->
        <main class="main-container">
            <div class="grid-container" id="satkerGrid">
                
                @foreach(collect($daftarSatker)->sortBy('nama_satker') as $item)
                    @php
                        // Membersihkan nomor telepon untuk link WhatsApp
                        $no_ptsp_clean = preg_replace('/[^0-9]/', '', $item['no_ptsp']);
                        if (str_starts_with($no_ptsp_clean, '0')) {
                            $no_ptsp_clean = '62' . substr($no_ptsp_clean, 1);
                        }
                        $link_wa = !empty($no_ptsp_clean) ? "https://wa.me/" . $no_ptsp_clean : "#";
                    @endphp

                    <div class="satker-card" data-title="{{ strtolower($item['nama_satker']) }} {{ strtolower($item['wilayah_kerja'] ?? '') }}">
                        <div>
                            <div class="card-header-icon">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            
                            <!-- Nama Satker -->
                            <h2 class="satker-title">{{ $item['nama_satker'] }}</h2>
                            
                            <!-- Wilayah Kerja / Kabupaten -->
                            <p class="satker-region">
                                <i class="fa-solid fa-location-dot"></i> 
                                {{ $item['wilayah_kerja'] ?? 'Provinsi Aceh' }}
                            </p>
                        </div>
                        
                        <!-- Tombol WA dengan Konfirmasi SweetAlert -->
                        <a href="javascript:void(0);" 
                        data-link="{{ $link_wa }}" 
                        data-satker="{{ $item['nama_satker'] }}"
                        class="btn-wa btn-wa-confirm">
                            <i class="fa-brands fa-whatsapp"></i> Hubungi PTSP
                        </a>
                    </div>
                @endforeach

            </div>
        </main>

        <!-- FOOTER -->
        <footer>
            <p>&copy; {{ date('Y') }} Mahkamah Syar'iyah Aceh. All rights reserved.</p>
            <p style="margin-top: 4px; opacity: 0.85;">
                Portal SAPA - <a href="https://sapa.ms-aceh.go.id" target="_blank" style="color: inherit; text-decoration: none; font-weight: 600;">sapa.ms-aceh.go.id</a>
            </p>
            <p style="margin-top: 4px; font-size: 0.6rem; color: var(--text-muted); opacity: 0.75;">
                Developed by <a href="https://bilikmedia.com" target="_blank" style="color: inherit; text-decoration: none; font-weight: 600;">bilikmedia.com</a>
            </p>
        </footer>

        <!-- Script Pencarian Real-time -->
        <script>
            function filterSatker() {
                let input = document.getElementById('searchInput').value.toLowerCase();
                let cards = document.getElementsByClassName('satker-card');

                for (let i = 0; i < cards.length; i++) {
                    let titleData = cards[i].getAttribute('data-title');
                    if (titleData.includes(input)) {
                        cards[i].style.display = "flex";
                    } else {
                        cards[i].style.display = "none";
                    }
                }
            }
        </script>

        <script>
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-wa-confirm');
                
                if (btn) {
                    e.preventDefault();

                    const linkWa = btn.getAttribute('data-link');
                    const namaSatker = btn.getAttribute('data-satker'); // Mengambil nama MS

                    if (typeof Swal === 'undefined') {
                        window.open(linkWa, '_blank');
                        return;
                    }

                    if (!linkWa || linkWa === '#') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nomor Tidak Tersedia',
                            text: 'Layanan WhatsApp ' + (namaSatker || '') + ' belum dikonfigurasi.',
                            confirmButtonColor: '#0b6e39'
                        });
                        return;
                    }

                    // SweetAlert dengan Judul Dinamis dan Pesan Tanggung Jawab
                    Swal.fire({
                        title: 'Hubungi ' + namaSatker + '?',
                        html: 'Anda akan diarahkan ke layanan komunikasi WhatsApp PTSP.<br><br><span style="font-size: 0.9em; color: #6c757d;"><b>Perhatian:</b> Harap gunakan layanan komunikasi ini dengan bijak dan bertanggung jawab.</span>',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#25D366',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa-brands fa-whatsapp me-1"></i> Lanjutkan ke WA',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(linkWa, '_blank');
                        }
                    });
                }
            });
            </script>
    </body>
</html>