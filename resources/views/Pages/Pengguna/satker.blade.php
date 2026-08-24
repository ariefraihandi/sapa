@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="row page-titles mx-0 py-2 mb-3">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Manajemen Satuan Kerja</h4>
                <p class="mb-0 small text-muted">Daftar Mahkamah Syar'iyah Tingkat Banding & Pertama se-Wilayah Hukum Aceh</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Pengguna</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Satker</a></li>
            </ol>
        </div>
    </div>

    <!-- Filter & Aksi Tambah Satker -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-0">
                <div class="card-body py-2 px-3">
                    <form action="{{ url('/pengguna/satker') }}" method="GET" class="row g-2 align-items-center">
                        <!-- Pencarian Nama Satker / Kode -->
                        <div class="col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama satker, singkatan, alamat, atau email..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="flaticon-381-search-1 me-1"></i> Cari
                                </button>
                                @if(request('search'))
                                    <a href="{{ url('/pengguna/satker') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Tombol Tambah Satker -->
                        <div class="col-md-3 text-md-end">
                            <button type="button" class="btn btn-sm btn-success text-white w-100" data-bs-toggle="modal" data-bs-target="#modalTambahSatker" style="background-color: #0b6e39; border-color: #0b6e39;">
                                <i class="fa fa-plus me-1"></i> Tambah Satker Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Satker (COMPACT VERSION) -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0">
                        <i class="flaticon-381-home me-1"></i> Daftar Satuan Kerja ({{ count($satkers) }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2" style="width: 40px;">No</th>
                                    <th class="py-2">Satuan Kerja</th>
                                    <th class="py-2">Kode / VShort</th>
                                    <th class="py-2">Kontak & Alamat</th>
                                    <th class="text-center py-2">Total User</th>
                                    <th class="text-center py-2 pe-3" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($satkers as $index => $satker)
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted small py-2">{{ $index + 1 }}</td>
                                        <td class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2 rounded bg-light d-flex align-items-center justify-content-center text-success font-weight-bold border" style="width: 36px; height: 36px; min-width: 36px; font-size: 0.75rem;">
                                                    @if($satker->logo && $satker->logo != 'logo.png')
                                                        <img src="{{ asset('storage/' . $satker->logo) }}" class="rounded w-100 h-100" style="object-fit: contain;">
                                                    @else
                                                        <i class="flaticon-381-home text-success"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-dark font-weight-bold small line-height-1">{{ $satker->satker_name }}</div>
                                                    <span class="badge bg-light text-success border py-0 px-2 mt-1" style="font-size: 0.68rem;">
                                                        {{ $satker->satker_short_name }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <code class="text-primary font-weight-bold" style="font-size: 0.75rem;">{{ $satker->satker_vshort }}</code>
                                        </td>
                                        <td class="py-2">
                                            @if($satker->telepon || $satker->whatsapp)
                                                <div class="small text-dark" style="font-size: 0.75rem;">
                                                    <i class="fa fa-phone me-1 text-muted"></i>{{ $satker->telepon ?? $satker->whatsapp }}
                                                </div>
                                            @endif
                                            @if($satker->email)
                                                <div class="small text-muted" style="font-size: 0.7rem;">
                                                    <i class="fa fa-envelope me-1 text-muted"></i>{{ $satker->email }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center py-2">
                                            <a href="{{ url('/pengguna/admin?satker_id=' . $satker->id) }}" title="Lihat daftar pengguna satker ini">
                                                <span class="badge bg-soft-success text-success font-weight-bold px-2 py-1" style="font-size: 0.7rem; background-color: #e8f5e9;">
                                                    <i class="fa fa-users me-1"></i> {{ $satker->users_count ?? $satker->user_count ?? 0 }} User
                                                </span>
                                            </a>
                                        </td>
                                        <td class="text-center pe-3 py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ url('/pengguna/admin?satker_id=' . $satker->id) }}" class="btn btn-xs btn-outline-info py-0 px-1" title="Lihat User">
                                                    <i class="fa fa-users"></i>
                                                </a>
                                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1" title="Edit Satker" data-bs-toggle="modal" data-bs-target="#modalEditSatker{{ $satker->id }}">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="flaticon-381-home display-4 d-block mb-1 text-muted"></i>
                                            Belum ada data Satuan Kerja yang ditemukan.
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

<!-- MODAL TAMBAH SATKER -->
<div class="modal fade" id="modalTambahSatker" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #0b6e39;">
                <h5 class="modal-title text-white mb-0"><i class="fa fa-plus-circle me-2"></i> Tambah Satuan Kerja Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/pengguna/satker/store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Nama Satker Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="satker_name" class="form-control form-control-sm" placeholder="Contoh: Mahkamah Syar'iyah Banda Aceh" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold">Singkatan <span class="text-danger">*</span></label>
                            <input type="text" name="satker_short_name" class="form-control form-control-sm" placeholder="MS Banda Aceh" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold">Kode / Slug (VShort) <span class="text-danger">*</span></label>
                            <input type="text" name="satker_vshort" class="form-control form-control-sm" placeholder="ms-bna" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="ms.bandaaceh@go.id">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Nomor Telepon / WA</label>
                            <input type="text" name="telepon" class="form-control form-control-sm" placeholder="0651-xxxxx">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">Alamat Kantor</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2" placeholder="Alamat lengkap kantor..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">Simpan Satker</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection