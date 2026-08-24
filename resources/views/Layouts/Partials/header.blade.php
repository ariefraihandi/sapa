<div class="nav-header">
    <a href="{{ url('/') }}" class="brand-logo d-flex align-items-center text-decoration-none" aria-label="SAPA-MS">
        <img src="{{ asset('assets/images/logo/sapa.png') }}" alt="Logo SAPA-MS" width="45" height="45" class="img-fluid me-2" style="object-fit: contain;">

        <span class="brand-title fs-4 fw-bold" style="color: #0B6E39; font-family: 'Poppins', sans-serif;">
            SAPA<span style="color: #10B981;"><br>MS Se-Aceh</span>
        </span>
    </a>
    
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>

<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        {{ $title ?? 'Dashboard' }}
                    </div>
                </div>

                <ul class="navbar-nav header-right">
                    <!-- Notification Dropdown (Pulse/Titik Kuning Dimatikan) -->
                    <li class="nav-item dropdown notification_dropdown position-relative">
                        <a class="nav-link ai-icon" href="javascript:void(0)" role="button" data-bs-toggle="dropdown" data-bs-display="static">
                            <i class="flaticon-381-ring"></i>
                            {{-- <div class="pulse-css"></div> --}} <!-- Titik kuning dinonaktifkan -->
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0">
                            <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3" style="height:380px;">
                                <div class="text-center py-5">
                                    <i class="fa-solid fa-bell-slash fa-3x text-muted mb-3 d-block"></i>
                                    <h6 class="text-muted fw-bold">Anda belum memiliki pesan</h6>
                                    <small class="text-muted">Notifikasi terbaru akan muncul di sini.</small>
                                </div>
                            </div>
                            <a class="all-notification d-none" href="javascript:void(0)">Lihat semua notifikasi <i class="ti-arrow-right"></i></a>
                        </div>
                    </li>

                    <!-- Profile Dropdown -->
                    <li class="nav-item dropdown header-profile position-relative">
                        <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown" data-bs-display="static">
                            <img src="{{ asset('assets/images/' . (Auth::user()->avatar ? 'profile/' . Auth::user()->avatar : 'logo/sapa.png')) }}" width="35" height="35" class="rounded-circle border me-2" style="object-fit: cover;" alt="{{ Auth::user()->name ?? 'User Avatar' }}"/>
                            <div class="header-info">
                                <span class="text-black"><strong>{{ Auth::user()->name ?? 'Petugas MS' }}</strong></span>
                                <p class="fs-12 mb-0 text-capitalize">{{ Auth::user()->role->role_name ?? 'Administrator' }}</p>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="right: 0; left: auto;">
                            <a href="javascript:void(0)" class="dropdown-item ai-icon py-2">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary me-2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>Profil Saya</span>
                            </a>
                            <div class="dropdown-divider my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item ai-icon border-0 bg-transparent w-100 text-start py-2">
                                    <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger me-2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    <span class="text-danger">Logout</span>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>