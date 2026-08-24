@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="row page-titles mx-0 py-2 mb-3">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Manajemen Hak Akses</h4>
                <p class="mb-0 small text-muted">Atur izin akses Menu & Submenu untuk setiap Role Pengguna</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">System</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Access</a></li>
            </ol>
        </div>
    </div>

    <!-- Pilih Role -->
    <div class="row mb-3">
        <div class="col-md-6 col-12">
            <div class="card shadow-sm border-0 mb-0">
                <div class="card-body py-2 px-3">
                    <form action="{{ route('system.access') }}" method="GET" class="d-flex align-items-center">
                        <label class="form-label fw-bold me-3 text-nowrap mb-0"><i class="fa-solid fa-user-shield me-1 text-success"></i> Pilih Role:</label>
                        <select name="role_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $selectedRoleId == $role->id ? 'selected' : '' }}>
                                    {{ $role->role_name }} ({{ $role->satker->satker_short_name ?? $role->satker->satker_name ?? 'Global' }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- TABEL 1: HAK AKSES MENU UTAMA -->
        <div class="col-lg-6 col-12 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title text-success font-weight-bold mb-0">
                        <i class="fa-solid fa-folder me-1"></i> Akses Menu Utama
                    </h6>
                    <span class="badge bg-light text-dark fs-12 border">Tabel Atas</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%" class="text-center">No</th>
                                    <th>Nama Menu</th>
                                    <th>URL / Target</th>
                                    <th width="20%" class="text-center">Akses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menus as $index => $menu)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <i class="{{ $menu->icon ?? 'fa-solid fa-circle' }} me-2 text-muted"></i>
                                            <strong class="text-dark">{{ $menu->name }}</strong>
                                        </td>
                                        <td><code class="small">{{ $menu->url ?? '#' }}</code></td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input toggle-menu-access" type="checkbox" 
                                                    data-role-id="{{ $selectedRoleId }}" 
                                                    data-menu-id="{{ $menu->id }}"
                                                    {{ in_array($menu->id, $activeMenuIds) ? 'checked' : '' }}
                                                    style="cursor: pointer; width: 40px; height: 20px;">
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">Belum ada Menu Utama.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL 2: HAK AKSES SUBMENU -->
        <div class="col-lg-6 col-12 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title text-success font-weight-bold mb-0">
                        <i class="fa-solid fa-folder-tree me-1"></i> Akses Submenu Navigasi
                    </h6>
                    <span class="badge bg-light text-dark fs-12 border">Tabel Bawah</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%" class="text-center">No</th>
                                    <th>Submenu</th>
                                    <th>Parent Menu</th>
                                    <th width="20%" class="text-center">Akses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submenus as $index => $sub)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong class="text-primary">{{ $sub->submenu }}</strong>
                                            <small class="d-block text-muted">{{ $sub->url }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary light">
                                                {{ $sub->menu->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input toggle-submenu-access" type="checkbox" 
                                                    data-role-id="{{ $selectedRoleId }}" 
                                                    data-submenu-id="{{ $sub->id }}"
                                                    {{ in_array($sub->id, $activeSubmenuIds) ? 'checked' : '' }}
                                                    style="cursor: pointer; width: 40px; height: 20px;">
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">Belum ada Submenu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // SweetAlert2 Toast Notifikasi Pojok Kanan Atas (Tanpa Reload)
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // 1. Event Listener Toggle Akses Menu Utama
    document.querySelectorAll('.toggle-menu-access').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const roleId = this.getAttribute('data-role-id');
            const menuId = this.getAttribute('data-menu-id');

            fetch("{{ route('system.access.toggle-menu') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ role_id: roleId, menu_id: menuId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Toast.fire({
                        icon: data.action === 'added' ? 'success' : 'warning',
                        title: data.message
                    });
                } else {
                    this.checked = !this.checked; // Revert switch jika gagal
                    Toast.fire({ icon: 'error', title: 'Gagal memperbarui hak akses!' });
                }
            })
            .catch(err => {
                this.checked = !this.checked;
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan jaringan!' });
            });
        });
    });

    // 2. Event Listener Toggle Akses Submenu
    document.querySelectorAll('.toggle-submenu-access').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const roleId = this.getAttribute('data-role-id');
            const submenuId = this.getAttribute('data-submenu-id');

            fetch("{{ route('system.access.toggle-submenu') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ role_id: roleId, submenu_id: submenuId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Toast.fire({
                        icon: data.action === 'added' ? 'success' : 'warning',
                        title: data.message
                    });
                } else {
                    this.checked = !this.checked;
                    Toast.fire({ icon: 'error', title: 'Gagal memperbarui hak akses!' });
                }
            })
            .catch(err => {
                this.checked = !this.checked;
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan jaringan!' });
            });
        });
    });
});
</script>
@endpush