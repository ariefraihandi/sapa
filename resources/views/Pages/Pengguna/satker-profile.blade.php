@extends('Layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm-6">
                <div class="welcome-text">
                    <h4 class="mb-0 text-success fw-bold">Profil Satuan Kerja</h4>
                    <p class="mb-0 small text-muted">Kelola identitas dan informasi kontak instansi Anda</p>
                </div>
            </div>
            <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                <ol class="breadcrumb d-inline-flex mb-0 float-sm-end">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Pengguna</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Profile Satker</a></li>
                </ol>
            </div>
        </div>

        <!-- Header Card Satker -->
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <div class="card-body p-3 p-md-4">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-xl bg-light rounded p-2 border flex-shrink-0" style="width: 80px; height: 80px;">
                            @if($satker->logo && file_exists(public_path('assets/images/satker/' . $satker->logo)))
                                <img src="{{ asset('assets/images/satker/' . $satker->logo) }}" class="rounded w-100 h-100" style="object-fit: contain;">
                            @else
                                <i class="flaticon-381-home text-success"></i>
                            @endif
                        </div>
                    </div>
                    <div class="col">
                        <h4 class="fw-bold mb-1 text-dark">{{ $satker->satker_name }}</h4>
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            <span class="badge bg-light text-success border">{{ $satker->satker_short_name }}</span>
                            <code class="text-primary font-weight-bold">{{ $satker->satker_vshort }}</code>
                        </div>
                        <p class="small text-muted mb-0"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $satker->alamat ?? 'Alamat kantor belum diatur' }}</p>
                    </div>
                    <div class="col-12 col-md-auto text-md-end">
                        <button type="button" class="btn btn-sm btn-success text-white w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#modalEditSatkerProfile" style="background-color: #0b6e39; border-color: #0b6e39;">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Data Satker
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Informasi Satker -->
        <div class="row g-3">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-2 px-3 border-bottom">
                        <h6 class="card-title text-success mb-0 fw-bold">
                            <i class="fa-solid fa-building me-2"></i> Detail Kontak & Instansi
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small d-block">Nama Lengkap Satker</label>
                                <span class="fw-bold text-dark fs-6">{{ $satker->satker_name }}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="text-muted small d-block">Nama Singkatan</label>
                                <span class="fw-bold text-dark fs-6">{{ $satker->satker_short_name }}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="text-muted small d-block">Kode VShort / Slug</label>
                                <code class="fw-bold text-primary fs-6">{{ $satker->satker_vshort }}</code>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small d-block">Alamat Email Resmi</label>
                                <span class="fw-bold text-dark"><i class="fa-solid fa-envelope me-1 text-warning"></i>{{ $satker->email ?? '-' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small d-block">Nomor Telepon Kantor</label>
                                <span class="fw-bold text-dark"><i class="fa-solid fa-phone me-1 text-primary"></i>{{ $satker->telepon ?? '-' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small d-block">Nomor WhatsApp PTSP</label>
                                <span class="fw-bold text-dark"><i class="fa-brands fa-whatsapp me-1 text-success"></i>{{ $satker->whatsapp ?? '-' }}</span>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted small d-block">Alamat Lengkap Kantor</label>
                                <span class="fw-bold text-dark"><i class="fa-solid fa-map-location-dot me-1 text-danger"></i>{{ $satker->alamat ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- MODAL: EDIT SATKER -->
<div class="modal fade" id="modalEditSatkerProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #0b6e39;">
                <h5 class="modal-title text-white mb-0"><i class="fa-solid fa-building-circle-check me-2"></i> Edit Data Satuan Kerja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pengguna.satker-profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 text-dark">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label font-weight-bold">Nama Satker Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="satker_name" class="form-control form-control-sm" value="{{ $satker->satker_name }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Singkatan <span class="text-danger">*</span></label>
                            <input type="text" name="satker_short_name" class="form-control form-control-sm" value="{{ $satker->satker_short_name }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control form-control-sm" value="{{ $satker->email }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Telepon Kantor</label>
                            <input type="text" name="telepon" class="form-control form-control-sm" value="{{ $satker->telepon }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">WhatsApp PTSP</label>
                            <input type="text" name="whatsapp" class="form-control form-control-sm" value="{{ $satker->whatsapp }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold">Alamat Kantor</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2">{{ $satker->alamat }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold">Logo Instansi (Opsional)</label>
                            <input type="file" name="logo" class="form-control form-control-sm" accept="image/*">
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
@endsection