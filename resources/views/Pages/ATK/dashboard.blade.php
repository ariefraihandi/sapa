@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="row page-titles mx-0 py-2 mb-3">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Dashboard Inventaris ATK</h4>
                <p class="mb-0 small text-muted">Ringkasan Stok, Monitoring Stok Kritis & Transaksi Terbaru</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inventaris ATK</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
            </ol>
        </div>
    </div>

    <!-- Alert Flash Message -->
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

    <!-- Cards Widget Summary -->
    <div class="row mb-4">
        <!-- Card Total Jenis Barang -->
        <div class="col-xl-4 col-xxl-4 col-sm-6 mb-3 mb-sm-0">
            <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-bold d-block mb-1">TOTAL JENIS BARANG</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalItems }} <small class="fs-6 text-muted">Item</small></h3>
                    </div>
                    <div class="p-3 bg-success-subtle rounded-circle text-success">
                        <i class="fa-solid fa-boxes-stacked fa-2x" style="color: #0b6e39;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Stok Kritis -->
        <div class="col-xl-4 col-xxl-4 col-sm-6 mb-3 mb-sm-0">
            <div class="card shadow-sm border-0 border-start border-4 border-warning h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-bold d-block mb-1">STOK KRITIS / MENIPIS</span>
                        <h3 class="fw-bold mb-0 text-warning">{{ $stokMinimalCount }} <small class="fs-6 text-muted">Item</small></h3>
                    </div>
                    <div class="p-3 bg-warning-subtle rounded-circle text-warning">
                        <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Quick Action Keluar Barang -->
        <div class="col-xl-4 col-xxl-4 col-sm-12">
            <div class="card shadow-sm border-0 bg-success text-white h-100" style="background-color: #0b6e39 !important;">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-white mb-1"><i class="fa-solid fa-hand-holding me-2"></i> Pengeluaran Barang</h6>
                        <p class="small text-white-50 mb-3">Catat pengambilan ATK oleh unit/bagian perkara secara langsung.</p>
                    </div>
                    <button type="button" class="btn btn-light btn-sm text-success fw-bold w-100" data-bs-toggle="modal" data-bs-target="#modalPengeluaranCepat">
                        <i class="fa-solid fa-minus-circle me-1"></i> Input Barang Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row">
        <!-- Tabel Ringkasan Stok Barang -->
        <div class="col-xl-7 col-lg-12 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0" style="color: #0b6e39 !important;">
                        <i class="fa-solid fa-list-check me-1"></i> Ringkasan Stok Barang ATK
                    </h6>
                    <a href="{{ route('atk.master-barang') }}" class="btn btn-outline-success btn-sm">
                        Kelola Master <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 text-dark">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-3" width="10%">Kode</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center" width="15%">Satuan</th>
                                    <th class="text-center" width="15%">Stok Sisa</th>
                                    <th class="text-center" width="15%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted"><small>{{ $item->kode_barang }}</small></td>
                                        <td class="fw-bold text-dark">{{ $item->nama_barang }}</td>
                                        <td class="text-center"><span class="badge bg-secondary text-white">{{ $item->satuan }}</span></td>
                                        <td class="text-center fw-bold fs-6">
                                            @if($item->stok_saat_ini <= $item->stok_minimal)
                                                <span class="text-danger">{{ $item->stok_saat_ini }}</span>
                                            @else
                                                <span class="text-success">{{ $item->stok_saat_ini }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->stok_saat_ini == 0)
                                                <span class="badge bg-danger">Habis</span>
                                            @elseif($item->stok_saat_ini <= $item->stok_minimal)
                                                <span class="badge bg-warning text-dark">Kritis</span>
                                            @else
                                                <span class="badge bg-success">Aman</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data barang ATK.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Log Transaksi Terbaru -->
        <div class="col-xl-5 col-lg-12 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0" style="color: #0b6e39 !important;">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> Transaksi Terbaru
                    </h6>
                    <a href="{{ route('atk.transaksi') }}" class="btn btn-outline-secondary btn-sm">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 text-dark">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-3">Tgl / Barang</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Jumlah</th>
                                    <th>Pengambil / Ket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $trx)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold">{{ $trx->item->nama_barang ?? '-' }}</div>
                                            <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if($trx->jenis_transaksi == 'masuk')
                                                <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-arrow-down me-1"></i>Masuk</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-arrow-up me-1"></i>Keluar</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold">
                                            {{ $trx->jumlah }} {{ $trx->item->satuan ?? '' }}
                                        </td>
                                        <td>
                                            @if($trx->jenis_transaksi == 'keluar')
                                                <div class="fw-semibold text-dark"><i class="fa-solid fa-user me-1 text-muted"></i>{{ $trx->nama_pengambil ?? '-' }}</div>
                                                <small class="text-muted"><i class="fa-solid fa-building me-1"></i>{{ $trx->ruangan ?? '-' }}</small>
                                            @else
                                                <small class="text-muted">{{ $trx->keterangan ?? 'Pengisian Stok' }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td>
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

<!-- MODAL INPUT PENGELUARAN BARANG CEPAT -->
<div class="modal fade" id="modalPengeluaranCepat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #0b6e39;">
                <h5 class="modal-title text-white mb-0"><i class="fa-solid fa-hand-holding me-2"></i> Input Pengeluaran ATK</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('atk.transaksi.keluar') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Barang ATK <span class="text-danger">*</span></label>
                        <select name="atk_item_id" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($items as $item)
                                @if($item->stok_saat_ini > 0)
                                    <option value="{{ $item->id }}">
                                        {{ $item->nama_barang }} (Sisa Stok: {{ $item->stok_saat_ini }} {{ $item->satuan }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jumlah Keluar <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control form-control-sm" min="1" placeholder="Contoh: 2" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Pengambil <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengambil" class="form-control form-control-sm" placeholder="Contoh: Ahmad" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ruangan / Unit <span class="text-danger">*</span></label>
                            <input type="text" name="ruangan" class="form-control form-control-sm" placeholder="Contoh: Kepaniteraan Perkara" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Dokumen</label>
                        <input type="text" name="nomor_dokumen" class="form-control form-control-sm" placeholder="Contoh: 01/2026">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Keterangan / Keperluan</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Catatan penggunaan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection