@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-3">
        <div class="col-sm-6">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Profil Pengguna</h4>
                <p class="mb-0 small text-muted">Informasi Akun dan Aktivitas Pengguna Sistem</p>
            </div>
        </div>
        <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
            <ol class="breadcrumb d-inline-flex mb-0 float-sm-end">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Pengguna</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Profile</a></li>
            </ol>
        </div>
    </div>

    <!-- Card Utama Profil -->
    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
        <div class="card-body p-3 p-md-4">
            <div class="row g-3 align-items-center">
                <!-- Avatar -->
                <div class="col-auto">
                    <div class="position-relative">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('assets/images/profile/' . Auth::user()->avatar) }}" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;" alt="{{ Auth::user()->name }}">
                        @else
                            <div class="rounded-circle bg-light text-success fw-bold border d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 1.8rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                            
                        <span class="fa fa-circle border border-2 border-white text-success position-absolute bottom-0 end-0"></span>
                    </div>
                </div>

                <!-- Detail Singkat -->
                <div class="col">
                    <h4 class="fw-bold mb-1 text-dark">{{ Auth::user()->name }}</h4>
                    <div class="d-flex flex-wrap gap-3 text-muted small mb-2">
                        <span><i class="fa-solid fa-briefcase me-1 text-success"></i>{{ Auth::user()->jabatan ?? 'Petugas PTSP' }}</span>
                        <span><i class="fa-solid fa-building me-1 text-success"></i>{{ Auth::user()->satker->satker_name ?? 'MS Aceh' }}</span>
                        <span><i class="fa-solid fa-envelope me-1 text-success"></i>{{ Auth::user()->email }}</span>
                    </div>

                    <!-- Badges Ringkas -->
                    <div class="d-flex flex-wrap gap-2">
                        
                        <div class="border rounded px-2 py-1 bg-light d-flex align-items-center">
                            <i class="fa-solid fa-user-shield text-primary me-2"></i>
                            <div>
                                <div class="fw-bold text-dark small leading-tight">{{ Auth::user()->role->role_name ?? 'User' }}</div>
                                <span class="text-muted" style="font-size: 0.65rem;">Role Akses</span>
                            </div>
                        </div>
                        <div class="border rounded px-2 py-1 bg-light d-flex align-items-center">
                            <i class="fa-solid fa-phone text-info me-2"></i>
                            <div>
                                <div class="fw-bold text-dark small leading-tight">{{ Auth::user()->phone ?? '-' }}</div>
                                <span class="text-muted" style="font-size: 0.65rem;">WhatsApp</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Edit -->
                <div class="col-12 col-md-auto text-md-end">
                    <button type="button" class="btn btn-sm btn-success text-white w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#modalEditProfile" style="background-color: #0b6e39; border-color: #0b6e39;">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Profil
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Filter -->
        <div class="card-footer py-0 px-3 bg-white border-top">
            <ul class="nav nav-tabs border-0 gap-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active text-success fw-bold py-2 border-0 border-bottom border-3 border-success" data-bs-toggle="tab" href="#overview">Ringkasan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-muted py-2 border-0" data-bs-toggle="tab" href="#activity">Aktivitas Saya</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Grid Konten Utama -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="overview">
            <div class="row g-3">
                <!-- Details Card -->
                <div class="col-xl-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-2 px-3 border-bottom">
                            <h6 class="card-title text-success mb-0 fw-bold">
                                <i class="fa-solid fa-user-gear me-2"></i> Detail Informasi Pengguna
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" style="width: 35%;">Nama Lengkap</td>
                                        <td class="fw-bold text-dark">: {{ Auth::user()->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Username</td>
                                        <td class="fw-bold text-primary">: {{ Auth::user()->username ?? '-' }}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td class="text-muted">Alamat Email</td>
                                        <td class="fw-bold text-dark">: {{ Auth::user()->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jabatan / Posisi</td>
                                        <td class="fw-bold text-dark">: {{ Auth::user()->jabatan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Satuan Kerja</td>
                                        <td class="fw-bold text-dark">: {{ Auth::user()->satker->satker_name ?? 'Mahkamah Syar\'iyah Aceh' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status Akun</td>
                                        <td>: 
                                            @if(Auth::user()->is_active)
                                                <span class="badge bg-success text-white px-2 py-1">Aktif</span>
                                            @else
                                                <span class="badge bg-danger text-white px-2 py-1">Non-Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Satker Card -->
                <div class="col-xl-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-2 px-3 border-bottom">
                            <h6 class="card-title text-success mb-0 fw-bold">
                                <i class="fa-solid fa-building me-2"></i> Satuan Kerja Terkait
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            @if(Auth::user()->satker)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-md me-3 bg-light rounded p-2 border flex-shrink-0" style="width: 48px; height: 48px;">
                                        @if(Auth::user()->satker->logo && Auth::user()->satker->logo != 'logo.png')
                                            <img src="{{ asset('storage/' . Auth::user()->satker->logo) }}" class="w-100 h-100" style="object-fit: contain;">
                                        @else
                                            <i class="fa-solid fa-landmark text-success fs-5"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ Auth::user()->satker->satker_name }}</h6>
                                        <span class="badge bg-light text-success border">{{ Auth::user()->satker->satker_short_name }}</span>
                                    </div>
                                </div>
                                <p class="small text-muted mb-2"><i class="fa-solid fa-location-dot me-2 text-danger"></i> {{ Auth::user()->satker->alamat ?? 'Alamat belum diatur' }}</p>
                                <p class="small text-muted mb-2"><i class="fa-solid fa-phone me-2 text-primary"></i> {{ Auth::user()->satker->telepon ?? Auth::user()->satker->whatsapp ?? '-' }}</p>
                                <p class="small text-muted mb-0"><i class="fa-solid fa-envelope me-2 text-warning"></i> {{ Auth::user()->satker->email ?? '-' }}</p>
                            @else
                                <p class="text-muted mb-0">Tidak ada data Satuan Kerja khusus (Pengguna Pusat / MS Aceh).</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT PROFIL -->
    <div class="modal fade" id="modalEditProfile" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #0b6e39;">
                    <h5 class="modal-title text-white mb-0"><i class="fa-solid fa-user-pen me-2"></i> Edit Informasi Profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('/profile/update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 text-dark">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm" value="{{ Auth::user()->name }}" required>
                            </div>
                          
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control form-control-sm" value="{{ Auth::user()->jabatan }}" placeholder="Contoh: Admin PTSP">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Nomor WhatsApp / HP</label>
                                <input type="text" name="phone" class="form-control form-control-sm" value="{{ Auth::user()->phone }}" placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label font-weight-bold">Foto Avatar (Opsional)</label>
                                <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection