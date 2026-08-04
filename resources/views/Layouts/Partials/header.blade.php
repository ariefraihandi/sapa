<div class="nav-header">
    <a href="{{ url('/') }}" class="brand-logo d-flex align-items-center text-decoration-none" aria-label="SAPA-MS">
        <img src="{{ asset('assets/images/logo-sapa.png') }}" alt="Logo SAPA-MS" width="45" height="45" class="img-fluid">

        <span class="brand-title ms-2 fs-4 fw-bold" style="color: #0B2A97; font-family: 'Poppins', sans-serif;">
            SAPA<span style="color: #0066FF;">-MS</span>
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
                        @yield('page_title', 'Dashboard') 
                    </div>
                </div>

                <ul class="navbar-nav header-right">
                                        
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link  ai-icon" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                            <i class="flaticon-381-ring"></i>
                            <div class="pulse-css"></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3" style="height:380px;">
                                <ul class="timeline">
                                    <li>
                                        <div class="timeline-panel">
                                            <div class="media me-2 media-info">
                                                KH
                                            </div>
                                            <div class="media-body">
                                                <h6 class="mb-1">Tiket baru dari MS Lhokseumawe</h6>
                                                <small class="d-block">2 menit yang lalu</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <a class="all-notification" href="javascript:void(0)">Lihat semua notifikasi <i class="ti-arrow-right"></i></a>
                        </div>
                    </li>

                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                            <img src="{{ asset('assets/images/profile/17.jpg') }}" width="20" alt=""/>
                            <div class="header-info">
                                <span class="text-black"><strong>{{ Auth::user()->name ?? 'Petugas MS' }}</strong></span>
                                <p class="fs-12 mb-0">{{ Auth::user()->role ?? 'Administrator' }}</p>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0)" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span class="ms-2">Profil Saya</span>
                            </a>
                            
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item ai-icon border-0 bg-transparent w-100 text-start">
                                    <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    <span class="ms-2 text-danger">Logout</span>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>