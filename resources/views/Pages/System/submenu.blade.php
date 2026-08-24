@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="row page-titles mx-0 py-2 mb-3">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Manajemen Submenu</h4>
                <p class="mb-0 small text-muted">Pengaturan Submenu Navigasi Sistem di bawah Menu Utama</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">System</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Submenu</a></li>
            </ol>
        </div>
    </div>

    <!-- Filter & Aksi Tambah Submenu -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-0">
                <div class="card-body py-2 px-3">
                    <form action="{{ url('/system/submenu') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama submenu atau url..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Cari
                                </button>
                                @if(request('search'))
                                    <a href="{{ url('/system/submenu') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 text-md-end">
                            <button type="button" class="btn btn-sm btn-success text-white w-100" data-bs-toggle="modal" data-bs-target="#modalTambahSubmenu" style="background-color: #0b6e39; border-color: #0b6e39;">
                                <i class="fa-solid fa-plus me-1"></i> Tambah Submenu Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Submenu -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0">
                        <i class="fa-solid fa-list-check me-1"></i> Daftar Submenu
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" id="table-container">
                        @include('Layouts.Partials.submenu_table', ['submenusGrouped' => $submenusGrouped])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH SUBMENU -->
<div class="modal fade" id="modalTambahSubmenu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #0b6e39;">
                <h5 class="modal-title text-white mb-0"><i class="fa-solid fa-circle-plus me-2"></i> Tambah Submenu Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/system/submenu/store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">Parent Menu Utama <span class="text-danger">*</span></label>
                            <select name="menu_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Menu Utama --</option>
                                @foreach($menus as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">Nama Submenu <span class="text-danger">*</span></label>
                            <input type="text" name="submenu" class="form-control form-control-sm" placeholder="Contoh: Daftar Satker" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label font-weight-bold">URL / Route <span class="text-danger">*</span></label>
                            <input type="text" name="url" class="form-control form-control-sm" placeholder="Contoh: system/satker" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold">Urutan</label>
                            <input type="number" name="order" class="form-control form-control-sm" value="1" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">Simpan Submenu</button>
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

            // Fungsi Reload Tabel via AJAX
            function refreshTable() {
                const searchParam = new URLSearchParams(window.location.search).get('search') || '';
                fetch(`{{ route('system.submenu') }}?search=${searchParam}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    const container = document.querySelector('#table-container');
                    if (container && data.html) {
                        container.innerHTML = data.html;
                    }
                })
                .catch(error => console.error('Gagal memperbarui tabel:', error));
            }

            // Event Delegation untuk tombol reorder
            document.addEventListener('click', function (e) {
                const button = e.target.closest('.btn-reorder');
                if (!button) return;

                const id = button.getAttribute('data-id');
                const type = button.getAttribute('data-type');
                const direction = button.getAttribute('data-direction');

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
                        }).then(() => {
                            // Eksekusi pembaruan tabel
                            refreshTable();
                        });
                    } else if (data.status === 'info') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Informasi',
                            text: data.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan!',
                        text: error.message || 'Gagal memproses permintaan.'
                    });
                });
            });
        });
    </script>
@endpush