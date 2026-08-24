@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Manajemen Pengguna Satker</h4>
                <p class="mb-0">Kelola akun administrator dan petugas PTSP dari seluruh Mahkamah Syar'iyah se-Aceh</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Pengguna</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Admin Satker</a></li>
            </ol>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-0">
                <div class="card-body py-2 px-3">
                    <form action="{{ url('/system/users') }}" method="GET" class="row g-2 align-items-center">
                        <!-- Filter Satker -->
                        <div class="col-md-4">
                            <select name="satker_id" class="form-select form-select-sm text-dark" onchange="this.form.submit()">
                                <option value="">-- Semua Satker --</option>
                                @foreach($satkers as $satker)
                                    <option value="{{ $satker->id }}" {{ isset($selectedSatker) && $selectedSatker == $satker->id ? 'selected' : '' }}>
                                        {{ $satker->satker_name }} ({{ $satker->satker_short_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pencarian Nama / Username / Email -->
                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama, username, email..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="flaticon-381-search-1 me-1"></i> Cari
                                </button>
                                @if(request('search') || request('satker_id'))
                                    <a href="{{ url('/system/users') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Tombol Tambah Pengguna -->
                        <div class="col-md-3 text-md-end">
                            <button type="button" class="btn btn-sm btn-success text-white w-100" data-bs-toggle="modal" data-bs-target="#modalTambahUser" style="background-color: #0b6e39; border-color: #0b6e39;">
                                <i class="fa fa-user-plus me-1"></i> Tambah Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Pengguna (COMPACT VERSION) -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0">
                        <i class="flaticon-381-user-7 me-1"></i> Daftar Pengguna Administrasi
                    </h6>
                    <span class="badge bg-light text-dark border">Total: {{ count($users) }} Pengguna</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <!-- Tambahkan table-sm & align-middle untuk tampilan padat -->
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2" style="width: 40px;">No</th>
                                    <th class="py-2">Pengguna</th>
                                    <th class="py-2">Satuan Kerja</th>
                                    <th class="py-2">Jabatan / Role</th>
                                    <th class="py-2">Kontak</th>
                                    <th class="text-center py-2">Status</th>
                                    <th class="text-center py-2 pe-3" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted small py-1">{{ $index + 1 }}</td>
                                        <td class="py-1">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2 rounded-circle bg-light d-flex align-items-center justify-content-center text-success fw-bold" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.75rem;">
                                                    @if($user->avatar)
                                                        <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                                    @else
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-dark font-weight-bold small line-height-1">{{ $user->name }}</div>
                                                    <small class="text-primary font-weight-bold" style="font-size: 0.7rem;">{{ $user->username ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-1">
                                            <span class="badge bg-light text-success font-weight-bold border py-0 px-2" style="font-size: 0.7rem;">
                                                {{ $user->satker->satker_short_name ?? '-' }}
                                            </span>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                {{ $user->satker->satker_name ?? 'Pusat MS Aceh' }}
                                            </div>
                                        </td>
                                        <td class="py-1">
                                            <div class="small text-dark font-weight-semibold" style="font-size: 0.75rem;">{{ $user->jabatan ?? '-' }}</div>
                                            <span class="badge bg-soft-primary text-primary px-1 py-0" style="font-size: 0.65rem;">
                                                {{ $user->role->role_name ?? 'admin' }}
                                            </span>
                                        </td>
                                        <td class="py-1">
                                            <div class="small text-dark" style="font-size: 0.75rem;"><i class="fa fa-envelope me-1 text-muted"></i>{{ $user->email }}</div>
                                            @if($user->phone)
                                                <div class="small text-muted" style="font-size: 0.7rem;"><i class="fa fa-phone me-1 text-muted"></i>{{ $user->phone }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center py-1">
                                            @if($user->is_active)
                                                <span class="badge bg-success text-white px-2 py-0" style="font-size: 0.65rem;">Aktif</span>
                                            @else
                                                <span class="badge bg-danger text-white px-2 py-0" style="font-size: 0.65rem;">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center pe-3 py-1">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-xs btn-outline-warning py-0 px-1" title="Reset Password" data-bs-toggle="modal" data-bs-target="#modalResetPass{{ $user->id }}">
                                                    <i class="fa fa-key"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1" title="Edit Pengguna" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $user->id }}">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="flaticon-381-user-7 display-4 d-block mb-1 text-muted"></i>
                                            Belum ada data pengguna admin yang ditemukan.
                                        </td>
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

<!-- MODAL: TAMBAH USER BARU -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #0b6e39;">
                <h5 class="modal-header-title text-white mb-0"><i class="fa fa-user-plus me-2"></i> Tambah Admin Satker Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/pengguna/admin/store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Satuan Kerja (Satker) <span class="text-danger">*</span></label>
                            <select name="satker_id" class="form-control text-dark" required>
                                <option value="">-- Pilih Satker --</option>
                                @foreach($satkers as $satker)
                                    <option value="{{ $satker->id }}">{{ $satker->satker_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">NIP (Opsional)</label>
                            <input type="text" name="nip" class="form-control" placeholder="Masukkan 18 digit NIP">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap petugas" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Administrator PTSP" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="admin@ptsp.go.id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Nomor Telepon / WA</label>
                            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Password Default <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Status Akun</label>
                            <select name="is_active" class="form-control text-dark">
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-white" style="background-color: #0b6e39;">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection