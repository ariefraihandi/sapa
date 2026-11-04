@extends('Layouts.app')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 31px !important;
        padding: 0.25rem 0.5rem !important;
        font-size: 0.875rem !important;
    }
    
    /* Mencegah konflik pointer event kalender pada Webkit/Blink */
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- SECTION 1: FORM BUAT RENCANA BELANJA -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title text-success font-weight-bold mb-0" style="color: #0b6e39 !important;">
                            <i class="fa-solid fa-cart-plus me-1"></i> Buat Form Rencana Belanja ATK Baru
                        </h6>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFormRencana" aria-expanded="true">
                            <i class="fa-solid fa-chevron-down"></i> Buka/Tutup Form
                        </button>
                    </div>
                    <div class="collapse show" id="collapseFormRencana">
                        <div class="card-body p-4">
                            <form action="{{ route('atk.rencana-belanja.store') }}" method="POST" id="formRencanaBelanja">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-bold">Nama Kegiatan / Pengadaan <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_kegiatan" class="form-control form-control-sm" placeholder="Contoh: Pengadaan ATK Triwulan IV 2026" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Tanggal Rencana <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_rencana" class="form-control form-control-sm custom-date-picker" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <hr class="my-3 text-muted">

                                <!-- Dynamic Items Table -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold mb-2">Pilih Barang yang Akan Dibeli <span class="text-danger">*</span></label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle" id="table-items-belanja">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th width="65%">Pilih Barang ATK</th>
                                                    <th width="25%">Jumlah Beli</th>
                                                    <th class="text-center" width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="wrapper-item-belanja">
                                                <tr class="item-row">
                                                    <td>
                                                        <select name="items[0][item_id]" class="form-select form-select-sm select2-item" required>
                                                            <option value="">-- Cari / Pilih Barang --</option>
                                                            @foreach($items as $barang)
                                                                <option value="{{ $barang->id }}">{{ $barang->nama_barang }} ({{ $barang->satuan }}) - Stok: {{ $barang->stok_saat_ini }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[0][jumlah]" class="form-control form-control-sm" min="1" placeholder="Jumlah Beli" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" style="display:none;">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="btn-add-item">
                                        <i class="fa-solid fa-plus me-1"></i> Tambah Item Lain
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Catatan Tambahan</label>
                                    <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Catatan pengadaan..."></textarea>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-sm btn-secondary" onclick="resetFormBelanja()">Reset Form</button>
                                    <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">
                                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Rencana Belanja
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: DAFTAR RENCANA BELANJA -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 px-3 d-flex justify-content-between align-items-center border-bottom">
                        <h6 class="card-title text-success font-weight-bold mb-0" style="color: #0b6e39 !important;">
                            <i class="fa-solid fa-cart-shopping me-1"></i> Daftar Rencana Belanja / Pengadaan ATK
                        </h6>
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

        <!-- SECTION 3: REKAPITULASI BULANAN -->
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
                                            // Menggunakan filter Collection Laravel (Aman untuk huruf besar/kecil & spasi)
                                            $totalMasuk = $it->transactions->filter(function($trx) {
                                                return strtolower(trim($trx->jenis_transaksi)) === 'masuk';
                                            })->sum('jumlah');

                                            $totalKeluar = $it->transactions->filter(function($trx) {
                                                return strtolower(trim($trx->jenis_transaksi)) === 'keluar';
                                            })->sum('jumlah');
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
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.getElementById('wrapper-item-belanja');
        const btnAdd = document.getElementById('btn-add-item');

        // Fix Datepicker Re-triggering
        function fixDatePickerBehavior() {
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(function (input) {
                input.addEventListener('change', function () {
                    this.blur();
                });
                input.addEventListener('blur', function (e) {
                    e.stopImmediatePropagation();
                });
            });
        }
        fixDatePickerBehavior();

        // Helper untuk Inisialisasi Select2
        function initSelect2(element) {
            if (typeof $.fn.select2 !== 'undefined') {
                $(element).select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Cari / Pilih Barang --',
                    allowClear: true,
                    width: '100%'
                });
            }
        }

        // Inisialisasi awal Select2 untuk baris pertama
        $('.select2-item').each(function () {
            initSelect2(this);
        });

        // Helper Re-Index Name Attribute agar array POST di PHP selalu berurutan (0, 1, 2, dst)
        function reindexRows() {
            const rows = wrapper.querySelectorAll('.item-row');
            rows.forEach((row, index) => {
                const selectElement = row.querySelector('select');
                const inputElement = row.querySelector('input[type="number"]');

                if (selectElement) {
                    selectElement.setAttribute('name', `items[${index}][item_id]`);
                }
                if (inputElement) {
                    inputElement.setAttribute('name', `items[${index}][jumlah]`);
                }
            });
            updateRemoveButtons();
        }

        // Event Tambah Baris Baru
        if (btnAdd) {
            btnAdd.addEventListener('click', function () {
                const rowTemplate = `
                    <tr class="item-row">
                        <td>
                            <select class="form-select form-select-sm select2-item-dynamic" required>
                                <option value="">-- Cari / Pilih Barang --</option>
                                @foreach($items as $barang)
                                    <option value="{{ $barang->id }}">{{ addslashes($barang->nama_barang) }} ({{ $barang->satuan }}) - Stok: {{ $barang->stok_saat_ini }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" min="1" placeholder="Jumlah Beli" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                // Append ke DOM
                wrapper.insertAdjacentHTML('beforeend', rowTemplate);
                
                // Ambil elemen baris terakhir yang baru saja ditambahkan
                const lastRow = wrapper.lastElementChild;
                const newSelect = lastRow.querySelector('select');
                
                // Inisialisasi Select2 pada baris baru
                initSelect2(newSelect);

                // Urutkan kembali nama attribute index items[]
                reindexRows();
            });
        }

        // Event Hapus Baris (Event Delegation)
        if (wrapper) {
            wrapper.addEventListener('click', function (e) {
                const btnRemove = e.target.closest('.btn-remove-row');
                if (btnRemove) {
                    const row = btnRemove.closest('.item-row');
                    const selectElement = row.querySelector('select');
                    
                    // Destroy instance Select2 sebelum menghapus baris dari DOM
                    if ($(selectElement).data('select2')) {
                        $(selectElement).select2('destroy');
                    }
                    
                    row.remove();
                    
                    // Re-index ulang name atribut setelah hapus
                    reindexRows();
                }
            });
        }

        function updateRemoveButtons() {
            const rows = wrapper.querySelectorAll('.item-row');
            rows.forEach((row) => {
                const btn = row.querySelector('.btn-remove-row');
                if (rows.length === 1) {
                    btn.style.display = 'none';
                } else {
                    btn.style.display = 'inline-block';
                }
            });
        }
    });

    function resetFormBelanja() {
        setTimeout(function() {
            $('.select2-item, .select2-item-dynamic').val('').trigger('change');
        }, 100);
    }
</script>
@endpush