@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="row page-titles mx-0 py-2 mb-3">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="mb-0 text-success fw-bold">Master Data Barang ATK</h4>
                <p class="mb-0 small text-muted">Kelola Katalog, Satuan, Kode Barang, dan Batas Stok Kritis</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inventaris ATK</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Master Barang</a></li>
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

    <!-- Aksi & Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-0">
                <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title text-success font-weight-bold mb-0" style="color: #0b6e39 !important;">
                        <i class="fa-solid fa-boxes-stacked me-1"></i> Daftar Katalog ATK ({{ count($items) }} Item)
                    </h6>
                    <button type="button" class="btn btn-sm btn-success text-white" data-bs-toggle="modal" data-bs-target="#modalItem" onclick="resetForm()" style="background-color: #0b6e39; border-color: #0b6e39;">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Barang Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Barang -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-dark">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3" width="5%">No</th>
                                    <th width="12%">Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center" width="12%">Satuan</th>
                                    <th class="text-center" width="12%">Stok Saat Ini</th>
                                    <th class="text-center" width="12%">Stok Minimal</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                    <tr>
                                        <td class="ps-3 text-muted fw-bold">{{ $index + 1 }}</td>
                                        <td class="fw-bold text-muted"><span class="badge bg-light text-dark border">{{ $item->kode_barang }}</span></td>
                                        <td class="fw-bold text-dark">{{ $item->nama_barang }}</td>
                                        <td class="text-center"><span class="badge bg-secondary text-white">{{ $item->satuan }}</span></td>
                                        <td class="text-center fw-bold fs-6">
                                            @if($item->stok_saat_ini <= $item->stok_minimal)
                                                <span class="text-danger">{{ $item->stok_saat_ini }}</span>
                                            @else
                                                <span class="text-success">{{ $item->stok_saat_ini }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold text-muted">{{ $item->stok_minimal }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-warning me-1" title="Edit Barang"
                                                onclick="editItem('{{ $item->id }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->satuan }}', '{{ $item->stok_minimal }}')">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <form action="{{ route('atk.item.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang {{ addslashes($item->nama_barang) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Barang">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data barang ATK.</td>
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

<!-- MODAL TAMBAH / EDIT BARANG -->
<div class="modal fade" id="modalItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #0b6e39;">
                <h5 class="modal-title text-white mb-0" id="modalItemTitle"><i class="fa-solid fa-box me-2"></i> Tambah Barang ATK</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('atk.item.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="item_id">
                <div class="modal-body p-4 text-dark">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" id="item_nama_barang" class="form-control form-control-sm" placeholder="Contoh: Kertas HVS A4" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" id="item_satuan" class="form-control form-control-sm" placeholder="Contoh: rim, pcs, kotak" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Stok Minimal (Alert)</label>
                            <input type="number" name="stok_minimal" id="item_stok_minimal" class="form-control form-control-sm" value="5" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #0b6e39;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function resetForm() {
        document.getElementById('item_id').value = '';
        document.getElementById('item_nama_barang').value = '';
        document.getElementById('item_satuan').value = '';
        document.getElementById('item_stok_minimal').value = '5';
        document.getElementById('modalItemTitle').innerHTML = '<i class="fa-solid fa-box me-2"></i> Tambah Barang ATK';
    }

    function editItem(id, nama, satuan, stokMin) {
        document.getElementById('item_id').value = id;
        document.getElementById('item_nama_barang').value = nama;
        document.getElementById('item_satuan').value = satuan;
        document.getElementById('item_stok_minimal').value = stokMin;
        document.getElementById('modalItemTitle').innerHTML = '<i class="fa-solid fa-pen me-2"></i> Edit Barang ATK';
        var modal = new bootstrap.Modal(document.getElementById('modalItem'));
        modal.show();
    }
</script>
@endpush
@endsection