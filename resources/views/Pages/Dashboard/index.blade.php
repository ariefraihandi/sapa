@extends('Layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <!-- HEADER DASHBOARD -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="text-success fw-bold mb-1">Selamat Datang di Portal SAPA-MS</h4>
            <p class="mb-0 text-muted small">
                @if($isMsAceh)
                    Monitoring Pelayanan & Pengaduan PTSP Lintas Mahkamah Syar'iyah Se-Aceh
                @else
                    Monitoring Pelayanan & Pengaduan PTSP {{ $satkerName }}
                @endif
            </p>
        </div>
        <div>
            <span class="badge bg-success text-white fs-13 px-3 py-2 rounded-pill shadow-sm">
                <i class="fa-solid fa-building-columns me-1"></i> {{ $isMsAceh ? 'MS ACEH (SUPERADMIN)' : $satkerName }}
            </span>
        </div>
    </div>

    <!-- MAIN GRID CONTAINER -->
    <div class="row g-4 align-items-stretch">
        
        <!-- KOLOM KIRI (CARDS & GRAFIK TREN BULANAN) -->
        <div class="col-xl-8 col-lg-7 d-flex flex-column">
            
            <!-- 3 SUMMARY CARDS -->
            <div class="row g-3 mb-4">
                <!-- Total Pengunjung -->
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; border-left: 5px solid #10b981 !important;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold d-block mb-1">Total Pengunjung</span>
                                <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalPengunjung) }}</h3>
                            </div>
                            <div class="p-2 bg-light text-success rounded-circle">
                                <i class="fa-solid fa-users fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Pengaduan -->
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; border-left: 5px solid #ef4444 !important;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold d-block mb-1">Total Pengaduan</span>
                                <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalPengaduan) }}</h3>
                            </div>
                            <div class="p-2 bg-light text-danger rounded-circle">
                                <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pengaduan Selesai -->
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; border-left: 5px solid #3b82f6 !important;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold d-block mb-1">Pengaduan Selesai</span>
                                <h3 class="fw-bold mb-0 text-dark">{{ $persenPengaduan }}%</h3>
                            </div>
                            <div class="p-2 bg-light text-primary rounded-circle">
                                <i class="fa-solid fa-circle-check fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAFIK TREN BULANAN -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-1">Grafik Tren Interaksi SAPA-MS ({{ date('Y') }})</h5>
                    <p class="fs-13 text-muted mb-0">Perbandingan jumlah pengunjung vs pengaduan per bulan</p>
                </div>
                <div class="card-body px-3 pb-3 pt-2 d-flex align-items-center">
                    <div id="chartAnalytics" class="w-100" style="min-height: 350px;"></div>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN (PIE & RECENT LIST) -->
        <div class="col-xl-4 col-lg-5 d-flex flex-column">
            
            <!-- PIE CHART -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Metode Kontak Layanan Online</h5>
                </div>
                <div class="card-body p-3">
                    <div id="chartLayananPie" style="min-height: 200px;"></div>
                </div>
            </div>

            <!-- RECENT PENGUNJUNG -->
            <div class="card border-0 shadow-sm flex-grow-1 mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Pengunjung Terbaru</h5>
                    <a href="{{ route('ptsp.pengunjung.index') }}" class="btn btn-sm btn-link text-success fw-bold p-0 text-decoration-none">Lihat Semua</a>
                </div>
                <div class="card-body p-3">
                    @forelse($recentPengunjung as $p)
                        @php
                            switch ($p->jenis_layanan) {
                                case 'tatap_muka':
                                    $iconClass = 'fa-solid fa-users';
                                    $bgClass   = 'bg-warning text-dark';
                                    break;
                                case 'pesan':
                                    $iconClass = 'fa-brands fa-whatsapp';
                                    $bgClass   = 'bg-success text-white';
                                    break;
                                case 'telepon':
                                    $iconClass = 'fa-solid fa-phone';
                                    $bgClass   = 'bg-primary text-white';
                                    break;
                                default:
                                    $iconClass = 'fa-solid fa-circle-info';
                                    $bgClass   = 'bg-secondary text-white';
                                    break;
                            }
                        @endphp

                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                            <div class="p-2 rounded-circle me-3 {{ $bgClass }}">
                                <i class="{{ $iconClass }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold text-dark small">{{ $p->nama_responden }}</h6>
                                <span class="text-muted d-block" style="font-size: 0.75rem;">
                                    {{ $p->satker->satker_short_name ?? 'MS Aceh' }} • {{ $p->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <span class="badge {{ $p->is_tindak_lanjut ? 'bg-success' : 'bg-danger' }}" style="font-size: 0.65rem;">
                                {{ $p->is_tindak_lanjut ? 'Responded' : 'Pending' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4 small mb-0">Belum ada data pengunjung.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- SECTION 1: GRAFIK PENGGUNAAN SAPA (FISIK VS ONLINE PER SATKER) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-chart-column me-2 text-success"></i>Grafik Penggunaan Layanan Per Satker
                    </h5>
                    <p class="fs-13 text-muted mb-0">Perbandingan statistik Pengunjung Fisik vs Pengguna SAPA Online (Pesan & Telepon)</p>
                </div>
                <div class="card-body p-3">
                    <div id="chartSapaSatker" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: TABEL KESIAPAN SYARAT PERKARA PER SATKER -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="fa-solid fa-folder-check me-2 text-success"></i>Kesiapan Syarat Perkara Per Satker
                        </h5>
                        <p class="fs-13 text-muted mb-0">Klik tombol Detail pada Satker untuk melihat daftar jenis perkara yang sudah memiliki syarat aktif</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 50px;">No</th>
                                    <th class="py-3">Satuan Kerja</th>
                                    <th class="py-3 text-center">Kode VShort</th>
                                    <th class="py-3 text-center">Jenis Perkara Aktif</th>
                                    <th class="pe-4 py-3 text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapSatker as $index => $item)
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted small">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark fs-14">{{ $item->satker_name }}</div>
                                            <span class="badge bg-light text-success border py-0 px-2" style="font-size: 0.7rem;">{{ $item->satker_short_name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <code class="text-primary font-weight-bold">{{ $item->satker_vshort }}</code>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $item->total_jenis_perkara_aktif > 0 ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 rounded-pill fs-12">
                                                <i class="fa-solid fa-folder-check me-1"></i> {{ $item->total_jenis_perkara_aktif }} / {{ $item->total_jenis_perkara_master }} Jenis
                                            </span>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" data-bs-toggle="modal" data-bs-target="#modalSyaratSatker{{ $item->satker_id }}" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-eye me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada data Satuan Kerja.</td>
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

<!-- MODAL DETAIL SYARAT PERKARA PER SATKER -->
@foreach($rekapSatker as $item)
    <div class="modal fade" id="modalSyaratSatker{{ $item->satker_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white fw-bold mb-0">
                        <i class="fa-solid fa-building-columns me-2"></i>Kesiapan Syarat Perkara - {{ $item->satker_short_name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-dark">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h6 class="fw-bold mb-0">{{ $item->satker_name }}</h6>
                            <span class="text-muted small">Total Jenis Perkara Berkas Aktif: <strong>{{ $item->total_jenis_perkara_aktif }} Jenis</strong></span>
                        </div>
                        <a href="{{ route('ptsp.syarat-perkara.index', ['satker_id' => $item->satker_id]) }}" class="btn btn-sm btn-outline-success">
                            <i class="fa-solid fa-gear me-1"></i> Kelola Syarat
                        </a>
                    </div>

                    @if(count($item->jenis_perkara_aktif_list) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center py-2" style="width: 40px;">No</th>
                                        <th class="py-2">Nama Jenis Perkara / Layanan</th>
                                        <th class="py-2">Kategori</th>
                                        <th class="text-center py-2">Dokumen Aktif</th>
                                        <th class="text-center py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->jenis_perkara_aktif_list as $subIndex => $jp)
                                        <tr>
                                            <td class="text-center small text-muted">{{ $subIndex + 1 }}</td>
                                            <td class="fw-bold text-dark small">{{ $jp->nama_layanan }}</td>
                                            <td><span class="badge bg-light text-dark border small">{{ $jp->kategori }}</span></td>
                                            <td class="text-center small fw-bold text-success">{{ $jp->total_aktif }} / {{ $jp->total_dokumen }} Dokumen</td>
                                            <td class="text-center">
                                                @if($jp->is_full)
                                                    <span class="badge bg-success" style="font-size: 0.65rem;"><i class="fa-solid fa-circle-check me-1"></i>Lengkap</span>
                                                @else
                                                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;"><i class="fa-solid fa-clock me-1"></i>Parsial</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-folder-open display-4 d-block mb-2 text-warning"></i>
                            <p class="mb-0">Belum ada syarat jenis perkara yang diaktifkan untuk Satker ini.</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- CHARTS APEX -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. TREN BULANAN
    var optionsAnalytics = {
        series: [{
            name: 'Pengunjung PTSP',
            type: 'column',
            data: @json($dataPengunjungPerBulan)
        }, {
            name: 'Pengaduan Masuk',
            type: 'line',
            data: @json($dataPengaduanPerBulan)
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: false }
        },
        stroke: { width: [0, 3], curve: 'smooth' },
        plotOptions: { bar: { columnWidth: '35%', borderRadius: 6 } },
        colors: ['#10b981', '#ef4444'],
        labels: @json($chartBulan),
        xaxis: { type: 'category' },
        yaxis: [{ title: { text: 'Jumlah' } }]
    };
    new ApexCharts(document.querySelector("#chartAnalytics"), optionsAnalytics).render();

    // 2. PIE LAYANAN
    var optionsPie = {
        series: [{{ $layananPesan }}, {{ $layananTelepon }}],
        chart: { type: 'donut', height: 200 },
        labels: ['WhatsApp Pesan', 'Telepon Direct'],
        colors: ['#10b981', '#3b82f6'],
        legend: { position: 'bottom' }
    };
    new ApexCharts(document.querySelector("#chartLayananPie"), optionsPie).render();

    // 3. GRAFIK PENGGUNAAN SAPA PER SATKER (FISIK VS ONLINE)
    var optionsSapaSatker = {
        series: [{
            name: 'Pengunjung Fisik',
            data: @json($dataPengunjungFisikSatker)
        }, {
            name: 'Pengguna SAPA Online',
            data: @json($dataPenggunaSapaSatker)
        }],
        chart: {
            type: 'bar',
            height: 380,
            toolbar: { show: true }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4
            },
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        colors: ['#0284c7', '#10b981'],
        xaxis: {
            categories: @json($categoriesSatker),
            labels: {
                rotate: -45,
                style: { fontSize: '11px' }
            }
        },
        yaxis: { title: { text: 'Jumlah Pengunjung' } },
        fill: { opacity: 1 },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " Pengunjung"
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#chartSapaSatker"), optionsSapaSatker).render();
});
</script>
@endsection