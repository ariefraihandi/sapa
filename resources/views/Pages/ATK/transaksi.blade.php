@extends('Layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 31px !important;
        padding: 0.25rem 0.5rem !important;
        font-size: 0.875rem !important;
    }
    input[type="date"] {
        position: relative;
    }
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="row page-titles mx-0 py-2 mb-3">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Transaksi Stok ATK</h4>
                <p class="mb-0 small text-muted">Pencatatan Pengeluaran Barang & Riwayat Transaksi</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inventaris ATK</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Transaksi Stok</a></li>
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

    <div class="row">
        <!-- Form Pengeluaran Barang -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 px-3 border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0" style="color: #0b6e39 !important;">
                        <i class="fa-solid fa-hand-holding me-1"></i> Form Pengeluaran ATK
                    </h6>
                </div>
                <div class="card-body p-3 text-dark">
                    <form action="{{ route('atk.transaksi.keluar') }}" method="POST" id="formTransaksiKeluar">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                            <select name="atk_item_id" class="form-select form-select-sm select2-single" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->nama_barang }} (Sisa Stok: {{ $item->stok_saat_ini }} {{ $item->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah" class="form-control form-control-sm" min="1" placeholder="Contoh: 2" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Pengambil <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengambil" class="form-control form-control-sm" placeholder="Nama pegawai / staf" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ruangan / Sub Bagian <span class="text-danger">*</span></label>
                            <input type="text" name="ruangan" class="form-control form-control-sm" placeholder="Contoh: Kepaniteraan Perkara" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Dokumen Form <span class="text-muted small">(Otomatis)</span></label>
                            <input type="text" name="nomor_dokumen" class="form-control form-control-sm bg-light" value="{{ $autoNomorDokumen }}" placeholder="Contoh: 001/2026">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan / Keperluan <span class="text-muted small">(Opsional)</span></label>
                            <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Tujuan penggunaan..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success text-white w-100" style="background-color: #0b6e39; border-color: #0b6e39;">
                            <i class="fa-solid fa-paper-plane me-1"></i> Simpan Transaksi Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Transaksi -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 px-3 border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0" style="color: #0b6e39 !important;">
                        <i class="fa-solid fa-list me-1"></i> Riwayat Transaksi Keluar / Masuk
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-dark">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">No. Dokumen / Tgl</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Jumlah</th>
                                    <th>Pengambil / Ruang</th>
                                    <th>Keterangan</th>
                                    <th class="text-center" width="12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark">{{ $trx->nomor_dokumen ?? '-' }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="fw-bold text-dark">{{ $trx->item->nama_barang ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($trx->jenis_transaksi == 'masuk')
                                                <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-arrow-down me-1"></i>Masuk</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-arrow-up me-1"></i>Keluar</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold">{{ $trx->jumlah }} {{ $trx->item->satuan ?? '' }}</td>
                                        <td>
                                            @if($trx->jenis_transaksi == 'keluar')
                                                <div class="fw-bold">{{ $trx->nama_pengambil ?? '-' }}</div>
                                                <small class="text-muted">{{ $trx->ruangan ?? '-' }}</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $trx->keterangan ?? '-' }}</td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- Tombol Edit -->
                                                <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditTrx{{ $trx->id }}">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                                <!-- Tombol Hapus -->
                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalDeleteTrx{{ $trx->id }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- MODAL EDIT TRANSAKSI -->
                                            <div class="modal fade text-start" id="modalEditTrx{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header bg-warning text-dark">
                                                            <h6 class="modal-title font-weight-bold"><i class="fa-solid fa-pen me-1"></i> Edit Transaksi ({{ $trx->nomor_dokumen ?? 'Detail' }})</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('atk.transaksi.update', $trx->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body text-dark">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                                                                    <select name="atk_item_id" class="form-select form-select-sm" required>
                                                                        @foreach($items as $item)
                                                                            <option value="{{ $item->id }}" {{ $trx->atk_item_id == $item->id ? 'selected' : '' }}>
                                                                                {{ $item->nama_barang }} (Sisa Stok: {{ $item->stok_saat_ini }} {{ $item->satuan }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-6 mb-3">
                                                                        <label class="form-label fw-bold">Jumlah <span class="text-danger">*</span></label>
                                                                        <input type="number" name="jumlah" class="form-control form-control-sm" value="{{ $trx->jumlah }}" min="1" required>
                                                                    </div>
                                                                    <div class="col-6 mb-3">
                                                                        <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                                                                        <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('Y-m-d') }}" required>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Nama Pengambil <span class="text-danger">*</span></label>
                                                                    <input type="text" name="nama_pengambil" class="form-control form-control-sm" value="{{ $trx->nama_pengambil }}" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Ruangan / Sub Bagian <span class="text-danger">*</span></label>
                                                                    <input type="text" name="ruangan" class="form-control form-control-sm" value="{{ $trx->ruangan }}" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Nomor Dokumen Form</label>
                                                                    <input type="text" name="nomor_dokumen" class="form-control form-control-sm" value="{{ $trx->nomor_dokumen }}">
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Keterangan / Keperluan</label>
                                                                    <textarea name="keterangan" class="form-control form-control-sm" rows="2">{{ $trx->keterangan }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light py-2">
                                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-sm btn-warning">Update Transaksi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- MODAL HAPUS TRANSAKSI -->
                                            <div class="modal fade text-start" id="modalDeleteTrx{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h6 class="modal-title text-white font-weight-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Konfirmasi Hapus</h6>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('atk.transaksi.destroy', $trx->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-body text-dark">
                                                                Apakah Anda yakin ingin menghapus transaksi ini? 
                                                                <br><strong class="text-danger">Stok barang akan otomatis dikembalikan/direset!</strong>
                                                            </div>
                                                            <div class="modal-footer bg-light py-2">
                                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-sm btn-danger">Hapus & Reset Stok</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-single').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Barang --',
                allowClear: true,
                width: '100%'
            });
        }

        const dateInput = document.getElementById('tanggal_transaksi');
        if (dateInput) {
            dateInput.addEventListener('change', function () {
                this.blur();
            });
            dateInput.addEventListener('blur', function (e) {
                e.stopImmediatePropagation();
            });
        }
    });
</script>
@endpush