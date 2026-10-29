@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="row page-titles mx-0 py-2 mb-3">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Laporan & Rencana Belanja ATK</h4>
                <p class="mb-0 small text-muted">Kelola Pengadaan / Rencana Belanja & Rekapitulasi Bulanan</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inventaris ATK</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan & Rekap</a></li>
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

    <!-- Section 1: Daftar Rencana Belanja (Pengadaan) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0" style="color: #0b6e39 !important;">
                        <i class="fa-solid fa-cart-shopping me-1"></i> Daftar Rencana Belanja / Pengadaan ATK
                    </h6>
                    <button type="button" class="btn btn-sm btn-success text-white" data-bs-toggle="modal" data-bs-target="#modalRencanaBelanja" style="background-color: #0b6e39; border-color: #0b6e39;">
                        <i class="fa-solid fa-plus me-1"></i> Buat Rencana Belanja Baru
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-dark">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Kode Rencana</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Tanggal Rencana</th>
                                    <th class="text-center">Total Item</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shoppingPlans as $plan)
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted">{{ $plan->kode_rencana }}</td>
                                        <td class="fw-bold text-dark">{{ $plan->nama_kegiatan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($plan->tanggal_rencana)->format('d/m/Y') }}</td>
                                        <td class="text-center fw-bold">{{ $plan->details->count() }} Item</td>
                                        <td class="text-center">
                                            @if($plan->status == 'completed')
                                                <span class="badge bg-success">Selesai (Stok Ditambahkan)</span>
                                            @elseif($plan->status == 'approved')
                                                <span class="badge bg-primary">Disetujui</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Draft / Proses</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($plan->status !== 'completed')
                                                <button class="btn btn-sm btn-success text-white" data-bs-toggle="modal" data-bs-target="#modalComplete{{ $plan->id }}" style="background-color: #0b6e39;">
                                                    <i class="fa-solid fa-check me-1"></i> Selesaikan Belanja
                                                </button>

                                                <!-- MODAL SELESAIKAN BELANJA -->
                                                <div class="modal fade" id="modalComplete{{ $plan->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow">
                                                            <div class="modal-header text-white" style="background-color: #0b6e39;">
                                                                <h5 class="modal-title text-white mb-0"><i class="fa-solid fa-circle-check me-2"></i> Konfirmasi Selesai Belanja ({{ $plan->kode_rencana }})</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('atk.rencana-belanja.complete', $plan->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body p-4 text-start text-dark">
                                                                    <p class="small text-muted mb-3">Pastikan jumlah realisasi barang belanja sesuai. Saat tombol disimpan, stok akan otomatis ditambahkan ke sistem.</p>
                                                                    <div class="table-responsive">
                                                                        <table class="table table-sm table-bordered align-middle">
                                                                            <thead class="bg-light">
                                                                                <tr>
                                                                                    <th>Nama Barang</th>
                                                                                    <th class="text-center">Rencana</th>
                                                                                    <th class="text-center" width="30%">Realisasi Belanja</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($plan->details as $det)
                                                                                    <tr>
                                                                                        <td class="fw-bold">{{ $det->item->nama_barang ?? '-' }}</td>
                                                                                        <td class="text-center">{{ $det->jumlah_rencana }} {{ $det->item->satuan ?? '' }}</td>
                                                                                        <td>
                                                                                            <input type="number" name="realisasi[{{ $det->id }}]" class="form-control form-control-sm text-center" value="{{ $det->jumlah_rencana }}" min="1" required>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light py-2">
                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">Proses & Tambahkan Stok</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small"><i class="fa-solid fa-lock me-1"></i> Terselesaikan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada rencana belanja.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Filter Rekapitulasi Bulanan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="card-title text-success font-weight-bold mb-0" style="color: #0b6e39 !important;">
                        <i class="fa-solid fa-file-invoice me-1"></i> Rekapitulasi Penggunaan & Masuk Stok (Periode {{ $bulan }}/{{ $tahun }})
                    </h6>
                    <form action="{{ route('atk.laporan') }}" method="GET" class="d-flex align-items-center gap-2">
                        <select name="bulan" class="form-select form-select-sm">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                        <select name="tahun" class="form-select form-select-sm">
                            @for($y=date('Y'); $y>=2024; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">Filter</button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 text-dark">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Total Masuk</th>
                                    <th>Total Keluar</th>
                                    <th>Sisa Stok Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $idx => $it)
                                    @php
                                        $totalMasuk = $it->transactions->where('jenis_transaksi', 'masuk')->sum('jumlah');
                                        $totalKeluar = $it->transactions->where('jenis_transaksi', 'keluar')->sum('jumlah');
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                        <td class="fw-bold">{{ $it->nama_barang }}</td>
                                        <td class="text-center"><span class="badge bg-secondary">{{ $it->satuan }}</span></td>
                                        <td class="text-center fw-bold text-success">+{{ $totalMasuk }}</td>
                                        <td class="text-center fw-bold text-danger">-{{ $totalKeluar }}</td>
                                        <td class="text-center fw-bold fs-6">{{ $it->stok_saat_ini }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data rekapitulasi.</td>
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

<!-- MODAL BUAT RENCANA BELANJA -->
<div class="modal fade" id="modalRencanaBelanja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #0b6e39;">
                <h5 class="modal-title text-white mb-0"><i class="fa-solid fa-cart-plus me-2"></i> Buat Rencana Belanja ATK Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('atk.rencana-belanja.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nama Kegiatan / Pengadaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control form-control-sm" placeholder="Contoh: Pengadaan ATK Triwulan IV 2026" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tanggal Rencana <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_rencana" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Barang yang Akan Dibeli <span class="text-danger">*</span></label>
                        <div id="wrapper-item-belanja">
                            <div class="row g-2 mb-2 item-row">
                                <div class="col-md-7">
                                    <select name="items[0][item_id]" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($items as $barang)
                                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }} ({{ $barang->satuan }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="items[0][jumlah]" class="form-control form-control-sm" min="1" placeholder="Jumlah Beli" required>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" style="display:none;"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success mt-2" id="btn-add-item"><i class="fa-solid fa-plus me-1"></i> Tambah Item Lain</button>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">CatatanTambahan</label>
                        <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Catatan pengadaan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">Simpan Rencana Belanja</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 1;
        const wrapper = document.getElementById('wrapper-item-belanja');
        const btnAdd = document.getElementById('btn-add-item');

        btnAdd.addEventListener('click', function () {
            const firstRow = wrapper.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);
            
            // Reset input values
            newRow.querySelector('select').name = `items[${itemIndex}][item_id]`;
            newRow.querySelector('select').value = '';
            newRow.querySelector('input').name = `items[${itemIndex}][jumlah]`;
            newRow.querySelector('input').value = '';
            
            const btnRemove = newRow.querySelector('.btn-remove-row');
            btnRemove.style.display = 'block';
            btnRemove.addEventListener('click', function () {
                newRow.remove();
            });

            wrapper.appendChild(newRow);
            itemIndex++;
        });
    });
</script>
@endpush
@endsection