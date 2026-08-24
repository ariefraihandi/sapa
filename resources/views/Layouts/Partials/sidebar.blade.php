<div class="deznav">
    <div class="deznav-scroll">
        @php
            $userRole = Auth::user()->role;

            // Ambil menu utama yang diizinkan untuk role user ini
            $allowedMenus = \App\Models\Menu::whereHas('accesses', function($q) use ($userRole) {
                                $q->where('role_id', $userRole->id);
                            })
                            ->where('is_active', true)
                            ->orderBy('order', 'asc')
                            ->get();
        @endphp

        <ul class="metismenu" id="menu">
            @foreach ($allowedMenus as $menu)
                @if ($menu->is_dropdown)
                    @php
                        // Ambil submenu yang diizinkan untuk role user ini
                        $allowedSubmenus = $menu->submenus()->whereHas('accesses', function($q) use ($userRole) {
                                            $q->where('role_id', $userRole->id);
                                        })->orderBy('order', 'asc')->get();
                    @endphp

                    @if($allowedSubmenus->count() > 0)
                        <li>
                            <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                                <i class="{{ $menu->icon }}"></i>
                                <span class="nav-text">{{ $menu->name }}</span>
                            </a>
                            <ul aria-expanded="false">
                                @foreach ($allowedSubmenus as $sub)
                                    <li><a href="{{ url($sub->url) }}">{{ $sub->submenu }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @else
                    <li>
                        <a href="{{ url($menu->url) }}">
                            <i class="{{ $menu->icon }}"></i>
                            <span class="nav-text">{{ $menu->name }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>

        {{-- <ul class="metismenu" id="menu">
            
            <!-- 1. DASHBOARD -->
            <li><a href="{{ url('/dashboard') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-networking"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- 2. LAYANAN INFORMASI & PRA-PERKARA -->
            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-notepad"></i>
                    <span class="nav-text">Layanan & Informasi</span>
                </a>
                <ul aria-expanded="false">
                    <!-- Diselaraskan ke Route::get('/layanan') -->
                    <li><a href="{{ url('/layanan') }}">Persyaratan Perkara</a></li>
                    <!-- Diselaraskan ke Route::get('/cekberkas') -->
                    <li><a href="{{ url('/cekberkas') }}">Cek Berkas Mandiri</a></li>
                    <!-- Diselaraskan ke Route::get('/chat') dan dipindah ke klaster Layanan -->
                    <li><a href="{{ url('/chat') }}">Helpdesk Lintas Satker</a></li>
                </ul>
            </li>

            <!-- 3. DOKUMEN & INSIDENTIL -->
            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-file"></i>
                    <span class="nav-text">Dokumen & Insidentil</span>
                </a>
                <ul aria-expanded="false">
                    <!-- Diselaraskan ke Route::get('/dokumen') -->
                    <li><a href="{{ url('/dokumen') }}">Akta Cerai & Putusan</a></li>
                </ul>
            </li>

            <!-- Parent Menu Pengguna -->
            <li>
                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-user-7"></i>
                    <span class="nav-text">Pengguna</span>
                </a>
                <ul aria-expanded="false">
                    <!-- Sub-menu 1: Menampilkan daftar seluruh Satker -->
                    <li><a href="{{ url('/pengguna/satker') }}">Satker</a></li>
                    
                    <!-- Sub-menu 2: Menampilkan daftar Pengguna/Admin dari setiap Satker -->
                    <li><a href="{{ url('/pengguna/admin') }}">Pengguna Satker</a></li>
                </ul>
            </li>

            <!-- 4. MANAJEMEN SISTEM (Khusus Admin) -->
            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-settings-2"></i>
                    <span class="nav-text">Manajemen Sistem</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('/sistem/laporan') }}">Laporan Pelayanan</a></li>
                    <li><a href="{{ url('/sistem/akun') }}">Manajemen Akun</a></li>
                </ul>
            </li>

        </ul> --}}

        <div class="copyright text-center mt-4">
            <p class="fs-12"><strong>SAPA-MS</strong> <br/> Mahkamah Syar'iyah se-Aceh</p>
            <p class="fs-12">Developed by <a href="#" class="text-primary">BILIKMEDIA</a> &copy; {{ date('Y') }}</p>
        </div>
        <div class="deznav-footer">
				<a href="https://drive.google.com/drive/folders/1r5FQZfQdTHXM3xsZ7f66N52X3CBsE-Lo?usp=drive_link" target="_blank" class="btn btn-docs btn-success w-100 mb-2">
					<span>Panduan Penggunaan</span>
					<i class="fa-solid fa-arrow-up rotate-x"></i>
				</a>
			</div>
    </div>
</div>
