@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-3">
        <div class="col-sm-6">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Profil Pengguna</h4>
                <p class="mb-0 small text-muted">Informasi Akun dan Pengaturan Keamanan Sistem</p>
            </div>
        </div>
        <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
            <ol class="breadcrumb d-inline-flex mb-0 float-sm-end">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Pengguna</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Profile</a></li>
            </ol>
        </div>
    </div>

    <!-- ALERT MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Gagal memperbarui:</strong>
            <ul class="mb-0 mt-1 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                    <a class="nav-link text-muted py-2 border-0 fw-bold" data-bs-toggle="tab" href="#settings">
                        <i class="fa-solid fa-key me-1"></i> Pengaturan Keamanan
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Grid Konten Utama -->
    <div class="tab-content">
        <!-- TAB 1: RINGKASAN -->
        <div class="tab-pane fade show active" id="overview">
            <div class="row g-3">
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

        <!-- TAB 2: PENGATURAN KEAMANAN (UBAH PASSWORD) -->
        <div class="tab-pane fade" id="settings">
            <div class="row">
                <div class="col-xl-8 col-lg-10 mx-auto">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="card-title text-success mb-0 fw-bold">
                                <i class="fa-solid fa-lock me-2"></i> Ubah Password Akun
                            </h6>
                            <small class="text-muted">Pastikan password baru Anda kuat dan tidak mudah ditebak.</small>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('profile.update-password') }}" method="POST" id="formChangePassword">
                                @csrf
                                @method('PUT')

                                <!-- Password Lama -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">Password Saat Ini <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Masukkan password lama" required>
                                        <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="current_password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Password Baru -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">Password Baru <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Masukkan password baru" required>
                                        <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="new_password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>

                                    <!-- REALTIME VALIDATOR BOX -->
                                    <div class="p-3 bg-light rounded border mt-2" id="pwdValidationBox">
                                        <small class="fw-bold d-block mb-1 text-secondary">Kriteria Password Baru:</small>
                                        <ul class="list-unstyled mb-0 small" style="font-size: 0.8rem;">
                                            <li id="ruleLength" class="text-muted"><i class="fa-solid fa-circle-xmark me-1 text-danger"></i> Minimal 8 Karakter</li>
                                            <li id="ruleDiff" class="text-muted"><i class="fa-solid fa-circle-xmark me-1 text-danger"></i> Berbeda dengan Password Lama</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Konfirmasi Password Baru -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ketik ulang password baru" required>
                                        <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="confirm_password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    <small id="confirmFeedback" class="d-block mt-1 fw-semibold"></small>
                                </div>

                                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                    <button type="reset" class="btn btn-secondary btn-sm px-4">Reset</button>
                                    <button type="submit" id="btnSubmitPassword" class="btn btn-success btn-sm text-white px-4" style="background-color: #0b6e39;" disabled>
                                        <i class="fa-solid fa-shield-halved me-1"></i> Perbarui Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT PROFIL -->
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

<!-- SCRIPT VALIDATOR REAL-TIME & TOGGLE EYE -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const currentPasswordInput = document.getElementById("current_password");
    const newPasswordInput     = document.getElementById("new_password");
    const confirmPasswordInput = document.getElementById("confirm_password");
    const btnSubmit            = document.getElementById("btnSubmitPassword");

    const ruleLength      = document.getElementById("ruleLength");
    const ruleDiff        = document.getElementById("ruleDiff");
    const confirmFeedback = document.getElementById("confirmFeedback");

    function validateForm() {
        const currentVal = currentPasswordInput.value;
        const newVal     = newPasswordInput.value;
        const confirmVal = confirmPasswordInput.value;

        let isLengthValid = newVal.length >= 8;
        let isDiffValid   = newVal !== "" && newVal !== currentVal;
        let isMatchValid  = newVal !== "" && newVal === confirmVal;

        // Validasi Rule 1: Minimal 8 Karakter
        if (isLengthValid) {
            ruleLength.className = "text-success fw-bold";
            ruleLength.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Minimal 8 Karakter';
        } else {
            ruleLength.className = "text-muted";
            ruleLength.innerHTML = '<i class="fa-solid fa-circle-xmark me-1 text-danger"></i> Minimal 8 Karakter';
        }

        // Validasi Rule 2: Berbeda dari Password Lama
        if (isDiffValid) {
            ruleDiff.className = "text-success fw-bold";
            ruleDiff.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Berbeda dengan Password Lama';
        } else {
            ruleDiff.className = "text-muted";
            ruleDiff.innerHTML = '<i class="fa-solid fa-circle-xmark me-1 text-danger"></i> Berbeda dengan Password Lama';
        }

        // Validasi Rule 3: Konfirmasi Password Cocok
        if (confirmVal.length > 0) {
            if (isMatchValid) {
                confirmFeedback.className = "d-block mt-1 small text-success";
                confirmFeedback.innerHTML = '<i class="fa-solid fa-check me-1"></i> Konfirmasi password cocok.';
            } else {
                confirmFeedback.className = "d-block mt-1 small text-danger";
                confirmFeedback.innerHTML = '<i class="fa-solid fa-xmark me-1"></i> Konfirmasi password tidak cocok!';
            }
        } else {
            confirmFeedback.innerHTML = "";
        }

        // Enable / Disable Tombol Submit
        if (currentVal.length > 0 && isLengthValid && isDiffValid && isMatchValid) {
            btnSubmit.removeAttribute("disabled");
        } else {
            btnSubmit.setAttribute("disabled", "disabled");
        }
    }

    // Input Listeners Real-time
    currentPasswordInput.addEventListener("input", validateForm);
    newPasswordInput.addEventListener("input", validateForm);
    confirmPasswordInput.addEventListener("input", validateForm);

    // Toggle Show/Hide Password Eye
    document.querySelectorAll(".toggle-pwd").forEach(button => {
        button.addEventListener("click", function () {
            const targetId = this.getAttribute("data-target");
            const inputField = document.getElementById(targetId);
            const icon = this.querySelector("i");

            if (inputField.type === "password") {
                inputField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                inputField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. NOTIFIKASI WARNING (Contoh: Peringatan Ganti Password Default)
    @if(session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan Keamanan!',
            text: "{{ session('warning') }}",
            confirmButtonColor: '#0b6e39',
            confirmButtonText: 'Ubah Password Sekarang'
        }).then((result) => {
            if (result.isConfirmed) {
                // Otomatis pindah/pilih tab Pengaturan Keamanan
                const settingsTab = new bootstrap.Tab(document.querySelector('a[href="#settings"]'));
                settingsTab.show();
            }
        });
    @endif

    // 2. NOTIFIKASI SUCCESS (Contoh: Berhasil Update Password/Profil)
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // 3. NOTIFIKASI ERROR
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#d33'
        });
    @endif
});
</script>
@endsection 