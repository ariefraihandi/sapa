@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- BREADCRUMB HEADER -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-0 text-success fw-bold">Daftar PTSP Se-Aceh</h4>
            <p class="mb-0 small text-muted">Monitoring Kontak Penanggung Jawab & Admin PTSP Mahkamah Syar'iyah Se-Aceh</p>
        </div>
        <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
            <ol class="breadcrumb d-inline-flex mb-0 float-sm-end">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Informasi & Pengaduan</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Daftar PTSP</a></li>
            </ol>
        </div>
    </div>

    <!-- CARD UTAMA DAFTAR PTSP -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h6 class="card-title text-success mb-0 fw-bold">
                    <i class="fa-solid fa-headset me-2"></i> Data PTSP Satuan Kerja Se-Aceh
                </h6>
                <small class="text-muted">Total: {{ count($daftarSatker) }} Satuan Kerja</small>
            </div>

            <!-- INPUT SEARCH / FILTER -->
            <div class="w-100 w-sm-auto" style="min-width: 280px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="searchPtsp" class="form-control border-start-0 bg-light" placeholder="Cari Satker / Penanggung Jawab...">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablePtsp">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="ps-4" style="width: 5%;">No</th>
                            <th style="width: 30%;">Satuan Kerja</th>
                            <th style="width: 25%;">Penanggung Jawab (PJ)</th>
                            <th style="width: 20%;">No. HP PJ</th>
                            <th style="width: 20%;">No. WA Admin PTSP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarSatker as $index => $item)
                            @php
                                $ptsp = $item->ptspDaerah;
                                $hpPj = $ptsp->no_hp_pj ?? null;
                                $waAdmin = $ptsp->no_wa_layanan ?? $item->whatsapp ?? null;
                                $namaPj = $ptsp->nama_pj ?? '-';
                            @endphp
                            <tr class="row-ptsp" data-search="{{ strtolower($item->satker_name . ' ' . $item->satker_short_name . ' ' . $namaPj . ' ' . $hpPj . ' ' . $waAdmin) }}">
                                <td class="ps-4 fw-bold text-muted index-number">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-light rounded p-1 border flex-shrink-0" style="width: 38px; height: 38px;">
                                            @if($item->logo && file_exists(public_path('assets/images/satker/' . $item->logo)))
                                                <img src="{{ asset('assets/images/satker/' . $item->logo) }}" class="w-100 h-100" style="object-fit: contain;">
                                            @else
                                                <i class="fa-solid fa-landmark text-success"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark fs-14">{{ $item->satker_name }}</h6>
                                            <span class="badge bg-light text-success border" style="font-size: 0.7rem;">{{ $item->satker_short_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $namaPj }}</span>
                                </td>
                                
                                <!-- NOMOR HP PJ (HIJAU JIKA TERSEDIA) -->
                                <td>
                                    @if($hpPj)
                                        <a href="https://wa.me/{{ $hpPj }}" target="_blank" class="btn btn-sm text-white fw-bold py-1 px-3 shadow-sm rounded-pill" style="font-size: 0.78rem; background-color: #25D366; border-color: #25D366;">
                                            <i class="fa-brands fa-whatsapp me-1"></i> {{ $hpPj }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary text-white py-1 px-2" style="font-size: 0.75rem;">Belum Diatur</span>
                                    @endif
                                </td>

                                <!-- NOMOR WA ADMIN PTSP (HIJAU JIKA TERSEDIA) -->
                                <td>
                                    @if($waAdmin)
                                        <a href="https://wa.me/{{ $waAdmin }}" target="_blank" class="btn btn-sm text-white fw-bold py-1 px-3 shadow-sm rounded-pill" style="font-size: 0.78rem; background-color: #059669; border-color: #059669;">
                                            <i class="fa-brands fa-whatsapp me-1"></i> {{ $waAdmin }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary text-white py-1 px-2" style="font-size: 0.75rem;">Belum Diatur</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data Satuan Kerja.</td>
                            </tr>
                        @endforelse
                        <tr id="noResultRow" style="display: none;">
                            <td colspan="5" class="text-center py-4 text-muted">Data Satuan Kerja tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT SEARCH REAL-TIME -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchPtsp");
    const rows = document.querySelectorAll(".row-ptsp");
    const noResultRow = document.getElementById("noResultRow");

    searchInput.addEventListener("keyup", function () {
        const query = this.value.toLowerCase().trim();
        let matchCount = 0;

        rows.forEach(row => {
            const searchData = row.getAttribute("data-search");
            if (searchData.includes(query)) {
                row.style.display = "";
                matchCount++;
            } else {
                row.style.display = "none";
            }
        });

        // Tampilkan pesan kosong jika tidak ada yang cocok
        if (matchCount === 0 && rows.length > 0) {
            noResultRow.style.display = "";
        } else {
            noResultRow.style.display = "none";
        }
    });
});
</script>
@endsection