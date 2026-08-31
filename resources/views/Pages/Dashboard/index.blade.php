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
        
        <!-- KOLOM KIRI (CARDS & GRAFIK TREN) -->
        <div class="col-xl-8 col-lg-7 d-flex flex-column">
            
            <!-- 4 SUMMARY CARDS (FULL WIDTH GRID 4 KOLOM DI SCREEN BESAR) -->
            <div class="row g-3 mb-4">
                <!-- 1. Total Pengunjung -->
                <div class="col-sm-6 col-md-3">
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

                <!-- 2. Total Pengaduan -->
                <div class="col-sm-6 col-md-3">
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

                <!-- 3. Pengaduan Selesai -->
                <div class="col-sm-6 col-md-3">
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

                <!-- 4. Syarat Perkara -->
                <div class="col-sm-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; border-left: 5px solid #f59e0b !important;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold d-block mb-1">Syarat Perkara</span>
                                <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalSyarat) }}</h3>
                            </div>
                            <div class="p-2 bg-light text-warning rounded-circle">
                                <i class="fa-solid fa-folder-open fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAFIK TREN UTAMA (MENGISI SISA TINGGI CONTAINER KIRI) -->
            <div class="card border-0 shadow-sm flex-grow-1" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-1">Grafik Tren Interaksi SAPA-MS ({{ date('Y') }})</h5>
                    <p class="fs-13 text-muted mb-0">Perbandingan jumlah pengunjung vs pengaduan per bulan</p>
                </div>
                <div class="card-body px-3 pb-3 pt-2 d-flex align-items-center">
                    <div id="chartAnalytics" class="w-100" style="min-height: 380px;"></div>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN (PIE & RECENT LIST) -->
        <div class="col-xl-4 col-lg-5 d-flex flex-column">
            
            <!-- PIE CHART -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Metode Kontak Layanan</h5>
                </div>
                <div class="card-body p-3">
                    <div id="chartLayananPie" style="min-height: 220px;"></div>
                </div>
            </div>

            <!-- RECENT PENGUNJUNG (FLEX GROW SUPAYA TINGGINYA PAS DENGAN KIRI) -->
            <div class="card border-0 shadow-sm flex-grow-1" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Pengunjung Terbaru</h5>
                    <a href="{{ route('ptsp.pengunjung.index') }}" class="btn btn-sm btn-link text-success fw-bold p-0 text-decoration-none">Lihat Semua</a>
                </div>
                <div class="card-body p-3">
                    @forelse($recentPengunjung as $p)
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                            <div class="p-2 rounded-circle me-3 {{ $p->jenis_layanan == 'pesan' ? 'bg-success text-white' : 'bg-primary text-white' }}">
                                <i class="{{ $p->jenis_layanan == 'pesan' ? 'fa-brands fa-whatsapp' : 'fa-solid fa-phone' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold text-dark small">{{ $p->nama_responden }}</h6>
                                <span class="text-muted d-block" style="font-size: 0.75rem;">{{ $p->satker->satker_short_name ?? 'MS Aceh' }} • {{ $p->created_at->diffForHumans() }}</span>
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
</div>

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
            height: 380,
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
        chart: { type: 'donut', height: 220 },
        labels: ['WhatsApp Pesan', 'Telepon Direct'],
        colors: ['#10b981', '#3b82f6'],
        legend: { position: 'bottom' }
    };
    new ApexCharts(document.querySelector("#chartLayananPie"), optionsPie).render();
});
</script>
@endsection