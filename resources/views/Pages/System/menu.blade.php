@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="row page-titles mx-0 py-2 mb-3">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Manajemen Menu</h4>
                <p class="mb-0 small text-muted">Pengaturan Menu Utama & Submenu Navigasi Sistem</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">System</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Menu</a></li>
            </ol>
        </div>
    </div>

    <!-- Filter & Aksi Tambah Menu -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-0">
                <div class="card-body py-2 px-3">
                    <form action="{{ url('/system/menu') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama menu, icon, atau url..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Cari
                                </button>
                                @if(request('search'))
                                    <a href="{{ url('/system/menu') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 text-md-end">
                            <button type="button" class="btn btn-sm btn-success text-white w-100" data-bs-toggle="modal" data-bs-target="#modalTambahMenu" style="background-color: #0b6e39; border-color: #0b6e39;">
                                <i class="fa-solid fa-plus me-1"></i> Tambah Menu Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Menu -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0">
                        <i class="fa-solid fa-list me-1"></i> Daftar Menu Utama ({{ count($menus) }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" id="table-container">
                        @include('Layouts.Partials.menu_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH MENU -->
<div class="modal fade" id="modalTambahMenu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #0b6e39;">
                <h5 class="modal-title text-white mb-0"><i class="fa-solid fa-circle-plus me-2"></i> Tambah Menu Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/system/menu/store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">Nama Menu <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Contoh: System" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">Icon Class (FontAwesome)</label>
                            <input type="text" name="icon" class="form-control form-control-sm" placeholder="Contoh: fa-solid fa-gear">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">URL / Route</label>
                            <input type="text" name="url" class="form-control form-control-sm" placeholder="Contoh: system/menu (Kosongkan jika dropdown)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_dropdown" id="is_dropdown" value="1">
                                <label class="form-check-input-label font-weight-bold" for="is_dropdown">
                                    Memiliki Submenu (Dropdown)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-input-label font-weight-bold" for="is_active">
                                    Status Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Fungsi Reload Tabel Menu via AJAX
            function refreshTable() {
                const searchParam = new URLSearchParams(window.location.search).get('search') || '';
                fetch(`{{ route('system.menu') }}?search=${searchParam}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    const container = document.querySelector('#table-container');
                    if (container && data.html) {
                        container.innerHTML = data.html;
                    }
                })
                .catch(error => console.error('Gagal memperbarui tabel menu:', error));
            }

            // Event Delegation untuk Seluruh Aksi di Tabel (Reorder & Hapus)
            document.addEventListener('click', function (e) {
                
                // 1. PROSES REORDER MENU
                const reorderBtn = e.target.closest('.btn-reorder');
                if (reorderBtn) {
                    const id = reorderBtn.getAttribute('data-id');
                    const type = reorderBtn.getAttribute('data-type');
                    const direction = reorderBtn.getAttribute('data-direction');

                    fetch("{{ route('system.reorder') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ id: id, type: type, direction: direction })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 800,
                                showConfirmButton: false
                            }).then(() => refreshTable());
                        } else {
                            Swal.fire({ icon: 'info', title: 'Informasi', text: data.message, timer: 1200, showConfirmButton: false });
                        }
                    });
                    return;
                }

                // 2. PROSES HAPUS MENU (DENGAN SWEETALERT KONFIRMASI)
                const deleteBtn = e.target.closest('.btn-delete-menu');
                if (deleteBtn) {
                    const id = deleteBtn.getAttribute('data-id');
                    const name = deleteBtn.getAttribute('data-name');

                    Swal.fire({
                        title: 'Hapus Menu?',
                        html: `Apakah Anda yakin ingin menghapus menu <strong>"${name}"</strong>?<br><small class="text-danger">*Semua Submenu di bawahnya juga akan ikut terhapus!</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`{{ url('/system/menu') }}/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: data.message,
                                        timer: 1000,
                                        showConfirmButton: false
                                    }).then(() => refreshTable());
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                                }
                            })
                            .catch(error => {
                                Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan!', text: error.message });
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush