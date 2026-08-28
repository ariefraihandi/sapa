@extends('Layouts.app')

@section('title', 'Syarat Perkara - PTSP')

@section('content')
<div class="container-fluid">
    <!-- Header & Breadcrumb -->
    <div class="row page-titles mx-0 align-items-center mb-4">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Data Syarat Perkara</h4>
                <p class="mb-0">Kelola dan tinjau persyaratan layanan permohonan perkara Satker</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">PTSP</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Syarat Perkara</a></li>
            </ol>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title m-0 me-3"><i class="fa-solid fa-list-check me-2 text-success"></i>Daftar Persyaratan Perkara</h4>
                        
                        {{-- Tombol Tambah Jenis Perkara (Dapat Diakses Semua Satker) --}}
                        <button type="button" class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddJenisPerkara">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Jenis Perkara
                        </button>
                    </div>
                    
                    {{-- Filter Khusus Superadmin MS Aceh --}}
                    @if($isMsAceh)
                        <form method="GET" action="{{ route('ptsp.syarat-perkara.index') }}" class="d-flex align-items-center">
                            <label class="me-2 fw-bold text-nowrap mb-0">Pilih Satker:</label>
                            <select name="satker_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">-- Semua Satker --</option>
                                @foreach($satkers ?? [] as $satker)
                                    <option value="{{ $satker->id }}" {{ request('satker_id') == $satker->id ? 'selected' : '' }}>
                                        {{ $satker->satker_name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <span class="badge bg-success fs-14">
                            <i class="fa-solid fa-building me-1"></i> {{ Auth::user()->satker ? Auth::user()->satker->satker_name : 'Satker Daerah' }}
                        </span>
                    @endif
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableSyaratPerkara" class="table table-hover table-striped display min-w850 align-middle">
                            <thead>
                                <tr class="bg-light text-center">
                                    <th width="5%">No</th>
                                    @if($isMsAceh)
                                        <th>Satker</th>
                                    @endif
                                    <th>Kategori</th>
                                    <th>Nama Perkara</th>
                                    <th class="text-center" width="22%">Status Penayangan & Dokumen</th>                                    
                                    <th class="text-center" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($syaratPerkara as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        @if($isMsAceh)
                                            <td><span class="fw-bold text-dark">{{ $item->satker->satker_short_name ?? $item->satker->satker_name ?? '-' }}</span></td>
                                        @endif
                                        
                                        <td>
                                            <span class="badge" style="background-color: #e2e8f0; color: #1e293b; font-weight: 600; padding: 6px 10px;">
                                                {{ $item->jenisPerkara->kategori ?? 'Umum' }}
                                            </span>
                                        </td>

                                        <td>
                                            <strong class="text-primary">{{ $item->jenisPerkara->nama_layanan ?? '-' }}</strong>
                                            <small class="d-block text-muted">{{ Str::limit($item->jenisPerkara->deskripsi ?? '', 55) }}</small>
                                        </td>

                                        <td class="text-center">
                                            @if($item->is_approved)
                                                <span class="badge" style="background-color: #10b981; color: #ffffff; font-weight: 600; padding: 6px 12px;">
                                                    <i class="fa-solid fa-check-double me-1"></i> DISETUJUI & TAYANG
                                                </span>
                                                <small class="d-block text-muted mt-1">
                                                    ({{ $item->total_aktif }}/{{ $item->total_dokumen }} Dokumen Aktif)
                                                </small>
                                            @else
                                                <span class="badge mb-1" style="background-color: #f59e0b; color: #ffffff; font-weight: 600; padding: 6px 12px;">
                                                    <i class="fa-solid fa-clock me-1"></i> PENDING REVIEW
                                                </span>
                                                <small class="d-block text-danger font-weight-bold" style="font-size: 0.8rem;">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                                    @if($item->total_dokumen == 0)
                                                        Belum ada dokumen
                                                    @else
                                                        {{ $item->belum_valid }} Dokumen Belum Valid
                                                    @endif
                                                </small>
                                            @endif
                                        </td>                                        

                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                    Aksi
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item text-primary" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $loop->index }}">
                                                        <i class="fa-solid fa-eye me-2"></i> Cek Syarat
                                                    </a>
                                                    <a class="dropdown-item text-warning" href="{{ route('ptsp.syarat-perkara.edit', ['id' => $item->id]) }}">
                                                        <i class="fa-solid fa-pen-to-square me-2"></i> Kelola Syarat
                                                    </a>
                                                    
                                                    {{-- HANYA ADMINISTRATOR / SUPERADMIN YANG BISA TAMPILKAN TOMBOL HAPUS --}}
                                                    @if(strtolower(Auth::user()->role->role_name ?? Auth::user()->role ?? '') === 'administrator' || $isMsAceh)
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $loop->index }}">
                                                            <i class="fa-solid fa-trash me-2"></i> Hapus Jenis Perkara
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $isMsAceh ? 7 : 6 }}" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                            Tidak ada data persyaratan perkara yang ditemukan.
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

<!-- ========================== MODAL DETAIL & HAPUS (DI LUAR TABEL) ========================== -->
@foreach($syaratPerkara as $index => $item)
    <!-- MODAL DETAIL -->
    <div class="modal fade" id="modalDetail{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white">
                        <i class="fa-solid fa-file-contract me-2"></i>Persyaratan: {{ $item->jenisPerkara->nama_layanan ?? '-' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3 p-3 bg-light rounded border">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Satker Pengelola:</small>
                                <strong>{{ $item->satker->satker_name ?? '-' }}</strong>
                            </div>
                            <div class="col-md-6 mt-2 mt-md-0">
                                <small class="text-muted d-block">Kategori Perkara:</small>
                                <strong>{{ $item->jenisPerkara->kategori ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-clipboard-list me-2 text-success"></i>Daftar Dokumen Persyaratan ({{ $item->total_dokumen }} Dokumen):</h6>
                    
                    @php
                        $allSyaratDokumen = \App\Models\SyaratPerkara::where('satker_id', $item->satker_id)
                            ->where('jenis_perkara_id', $item->jenis_perkara_id)
                            ->get();
                    @endphp

                    @if($allSyaratDokumen->count() > 0)
                        <ul class="list-group mb-3">
                            @foreach($allSyaratDokumen as $doc)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-start">
                                        <i class="fa-solid fa-circle-check {{ $doc->is_active ? 'text-success' : 'text-secondary' }} mt-1 me-3"></i>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $doc->syarat_dokumen }}</div>
                                            @if($doc->url_dokumen)
                                                <a href="{{ $doc->url_dokumen }}" target="_blank" class="small text-primary">
                                                    <i class="fa-solid fa-link me-1"></i>Buka Link Template
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    @if($doc->is_active)
                                        <span class="badge bg-success">Aktif / Valid</span>
                                    @else
                                        <span class="badge bg-danger">Belum Valid / Nonaktif</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Belum ada daftar persyaratan.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL HAPUS JENIS PERKARA (HANYA UNTUK ADMINISTRATOR) -->
    @if(strtolower(Auth::user()->role->role_name ?? Auth::user()->role ?? '') === 'administrator' || $isMsAceh)
        <div class="modal fade" id="modalDelete{{ $loop->index }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>Konfirmasi Hapus Jenis Perkara
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fa-solid fa-trash text-danger fa-3x mb-3"></i>
                        <h5>Apakah Anda yakin ingin menghapus seluruh Jenis Perkara ini?</h5>
                        <p class="text-muted mb-0"><strong>{{ $item->jenisPerkara->nama_layanan ?? '-' }}</strong></p>
                        <p class="text-danger small mb-0 mt-2">Tindakan ini akan menghapus jenis perkara beserta <b>semua dokumen syarat</b> yang ada di dalamnya!</p>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('ptsp.syarat-perkara.destroy-jenis', $item->jenis_perkara_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Ya, Hapus Seluruh Jenis Perkara</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- ========================== MODAL TAMBAH JENIS PERKARA BARU ========================== -->
<div class="modal fade" id="modalAddJenisPerkara" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content border-0 shadow" action="{{ route('ptsp.syarat-perkara.store-jenis') }}" method="POST">
            @csrf
            
            @if($isMsAceh)
                <div class="p-3 bg-light border-bottom">
                    <label class="form-label fw-bold">Pilih Satker Pemilik Layanan: <span class="text-danger">*</span></label>
                    <select name="satker_id" class="form-select" required>
                        <option value="">-- Pilih Satker --</option>
                        @foreach($satkers as $s)
                            <option value="{{ $s->id }}">{{ $s->satker_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white"><i class="fa-solid fa-folder-plus me-2"></i>Tambah Jenis Perkara Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body text-start">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kategori Perkara <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <option value="Perkawinan">Perkawinan</option>
                            <option value="Kewarisan & Harta">Kewarisan & Harta</option>
                            <option value="Ekonomi Syariah">Ekonomi Syariah</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Layanan / Perkara Baru <span class="text-danger">*</span></label>
                        <input type="text" name="nama_layanan" class="form-control" placeholder="Contoh: Permohonan Isbat Nikah Konsuler" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Deskripsi Layanan (Singkat)</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Jelaskan secara singkat mengenai permohonan/gugatan ini..."></textarea>
                    </div>

                    <hr class="my-2">
                    <h6 class="fw-bold text-success mb-3"><i class="fa-solid fa-file-pen me-1"></i>Syarat Dokumen Pertama</h6>

                    <div class="col-md-7 mb-3">
                        <label class="form-label fw-bold">Nama Dokumen Pertama <span class="text-danger">*</span></label>
                        <input type="text" name="syarat_pertama" class="form-control" placeholder="Contoh: Surat Permohonan Bermaterai" required>
                    </div>

                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-bold">URL Template/Blanko (Opsional)</label>
                        <input type="url" name="url_dokumen" class="form-control" placeholder="https://docs.google.com/...">
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-save me-1"></i> Simpan & Kelola Syarat</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tableSyaratPerkara').DataTable({
            responsive: true,
            ordering: false,
            language: {
                search: "Cari Perkara/Syarat:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Sebelum"
                },
                emptyTable: "Data tidak tersedia"
            }
        });
    });
</script>
@endpush