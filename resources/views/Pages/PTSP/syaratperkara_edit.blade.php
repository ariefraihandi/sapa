@extends('layouts.app')

@section('title', 'Kelola Syarat Perkara - PTSP')

@section('content')
<div class="container-fluid">
    <!-- Header & Tombol Back -->
    <div class="row page-titles mx-0 align-items-center mb-4">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Kelola Persyaratan Dokumen</h4>
                <p class="mb-0">Perbarui rincian, tambah, hapus, atau atur keaktifan dokumen</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <a href="{{ route('ptsp.syarat-perkara.index') }}" class="btn btn-secondary btn-sm px-3 shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> Terdapat kesalahan input. Pastikan URL valid dan nama dokumen terisi.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h4 class="card-title m-0 fw-bold" style="color: #000000 !important;">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Layanan: {{ $sample->jenisPerkara->nama_layanan ?? '-' }}
                    </h4>

                    <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddSyarat">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Syarat Baru
                    </button>
                </div>
                
                <div class="card-body">
                    <!-- Info Layanan -->
                    @php
                        $totalDocs = $syaratList->count();
                        $activeDocs = $syaratList->where('is_active', 1)->count();
                        // Otomatis disetujui HANYA JIKA ada dokumen DAN semuanya aktif (100%)
                        $isFullyApproved = ($totalDocs > 0) && ($totalDocs === $activeDocs);
                    @endphp
                    <div class="mb-3 p-3 bg-light rounded border border-secondary">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Satker Pengelola:</small>
                                <strong>{{ $sample->satker->satker_name ?? '-' }}</strong>
                            </div>
                            <div class="col-md-4 mt-2 mt-md-0">
                                <small class="text-muted d-block">Kategori Perkara:</small>
                                <strong>{{ $sample->jenisPerkara->kategori ?? '-' }}</strong>
                            </div>
                            <div class="col-md-4 mt-2 mt-md-0 text-md-end">
                                <small class="text-muted d-block mb-1">Status Penayangan Layanan:</small>
                                @if($isFullyApproved)
                                    <span class="badge" style="background-color: #10b981; color: #ffffff; font-weight: 600; padding: 8px 14px; font-size: 0.85rem;">
                                        <i class="fa-solid fa-check-double me-1"></i> DISETUJUI & TAYANG
                                    </span>
                                @else
                                    <span class="badge" style="background-color: #f59e0b; color: #ffffff; font-weight: 600; padding: 8px 14px; font-size: 0.85rem;">
                                        <i class="fa-solid fa-clock me-1"></i> PENDING REVIEW
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Sistem Penayangan Otomatis Barunya -->
                    <div class="alert alert-info py-2 mb-4 border-info">
                        <small>
                            <i class="fa-solid fa-circle-info me-1"></i> 
                            <strong>Sistem Penayangan Otomatis:</strong> 
                            Layanan perkara ini akan <b>Otomatis TAYANG (Disetujui)</b> apabila <u>seluruh dokumen (100%)</u> di bawah ini berstatus <b>AKTIF</b>. Jika ada 1 atau lebih dokumen yang NONAKTIF, status penayangan otomatis menjadi <b>PENDING REVIEW</b>.
                        </small>
                    </div>

                    <!-- Daftar Dokumen (Tabel) -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Dokumen Persyaratan</th>
                                    <th>Tautan Template / Blanko</th>
                                    <th width="15%">Status Dokumen</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($syaratList as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td><strong>{{ $item->syarat_dokumen }}</strong></td>
                                        <td class="text-center">
                                            @if($item->url_dokumen)
                                                <a href="{{ $item->url_dokumen }}" target="_blank" class="badge bg-primary">
                                                    <i class="fa-solid fa-link me-1"></i> Lihat Tautan
                                                </a>
                                            @else
                                                <span class="text-muted small">Tidak ada link</span>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- Toggle Aktif/Nonaktif Per Baris -->
                                            <div class="form-check form-switch d-flex justify-content-center m-0">
                                                <input class="form-check-input ajax-toggle-doc" type="checkbox" role="switch" 
                                                    id="toggleDoc{{ $item->id }}" data-id="{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2 status-label {{ $item->is_active ? 'text-success' : 'text-danger' }}" for="toggleDoc{{ $item->id }}">
                                                    <strong>{{ $item->is_active ? 'AKTIF' : 'NONAKTIF' }}</strong>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <!-- Tombol Edit Modal -->
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}" title="Edit">
                                                <i class="fa-solid fa-pencil"></i>
                                            </button>
                                            <!-- Tombol Hapus Modal -->
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $item->id }}" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada dokumen syarat. Silakan tambah syarat baru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- =========================================================
                        SEMUA MODAL DILETAKKAN DI LUAR TABEL (DI SINI)
                        ========================================================= --}}
                    @foreach($syaratList as $item)
                        <!-- MODAL EDIT DOKUMEN -->
                        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form class="modal-content" action="{{ route('ptsp.syarat-perkara.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title text-dark"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Dokumen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Nama Syarat / Dokumen <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="syarat_dokumen" value="{{ $item->syarat_dokumen }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">URL Template / Form (Opsional)</label>
                                            <input type="url" class="form-control" name="url_dokumen" value="{{ $item->url_dokumen }}" placeholder="https://drive.google.com/...">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- MODAL HAPUS DOKUMEN -->
                        <div class="modal fade" id="modalDelete{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body text-center py-4">
                                        <i class="fa-solid fa-trash text-danger fa-3x mb-3"></i>
                                        <h5 class="mb-3">Hapus Dokumen Ini?</h5>
                                        <p class="text-muted"><strong>{{ $item->syarat_dokumen }}</strong></p>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-center">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('ptsp.syarat-perkara.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================== MODAL TAMBAH SYARAT BARU ========================== -->
<div class="modal fade" id="modalAddSyarat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" action="{{ route('ptsp.syarat-perkara.store') }}" method="POST">
            @csrf
            <input type="hidden" name="satker_id" value="{{ $sample->satker_id }}">
            <input type="hidden" name="jenis_perkara_id" value="{{ $sample->jenis_perkara_id }}">
            
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-white"><i class="fa-solid fa-plus-circle me-2"></i>Tambah Syarat Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Dokumen / Syarat <span class="text-danger">*</span></label>
                    <input type="text" name="syarat_dokumen" class="form-control" placeholder="Contoh: Bukti Transfer (Asli)" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">URL Template (Opsional)</label>
                    <input type="url" name="url_dokumen" class="form-control" placeholder="https://docs.google.com/...">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-save me-1"></i> Simpan Dokumen Baru</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Handle AJAX Toggle Per Baris Dokumen
        $(document).on('change', '.ajax-toggle-doc', function() {
            let toggleInput = $(this);
            let id = toggleInput.data('id');
            let isChecked = toggleInput.is(':checked');
            let label = toggleInput.siblings('.status-label');

            label.html('<i class="fa-solid fa-spinner fa-spin"></i>').removeClass('text-success text-danger').addClass('text-warning');
            
            $.ajax({
                url: "{{ route('ptsp.syarat-perkara.toggle-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    is_active: isChecked ? 1 : 0
                },
                success: function(response) {
                    if(response.success) {
                        if (response.is_active) {
                            label.html('<strong>AKTIF</strong>').removeClass('text-warning text-danger').addClass('text-success');
                        } else {
                            label.html('<strong>NONAKTIF</strong>').removeClass('text-warning text-success').addClass('text-danger');
                        }
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                error: function(xhr) {
                    toggleInput.prop('checked', !isChecked);
                    label.html(isChecked ? '<strong>NONAKTIF</strong>' : '<strong>AKTIF</strong>')
                         .removeClass('text-warning')
                         .addClass(isChecked ? 'text-danger' : 'text-success');
                         
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan sistem saat memperbarui status dokumen.'
                    });
                }
            });
        });
    });
</script>
@endpush