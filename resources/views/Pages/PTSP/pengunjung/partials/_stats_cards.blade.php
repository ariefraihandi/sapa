<div class="row g-3 mb-4">
    <!-- 1. Total Pengunjung -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
            <div class="card-body p-3 text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small fw-semibold text-uppercase d-block" style="font-size: 11px;">Total Kunjungan</span>
                        <h3 class="fw-bold mb-0 mt-1 text-white">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(255, 255, 255, 0.25);">
                        <i class="fa-solid fa-users text-white fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Sudah Ditindaklanjuti -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
            <div class="card-body p-3 text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small fw-semibold text-uppercase d-block" style="font-size: 11px;">Sudah Dilayani</span>
                        <h3 class="fw-bold mb-0 mt-1 text-white">{{ number_format($stats['selesai']) }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(255, 255, 255, 0.25);">
                        <i class="fa-solid fa-circle-check text-white fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Belum Ditindaklanjuti (Pending) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
            <div class="card-body p-3 text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small fw-semibold text-uppercase d-block" style="font-size: 11px;">Belum Dilayani</span>
                        <h3 class="fw-bold mb-0 mt-1 text-white">{{ number_format($stats['pending']) }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(255, 255, 255, 0.25);">
                        <i class="fa-solid fa-clock-rotate-left text-white fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Rata-Rata Waktu Layanan (Jam Kerja) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
            <div class="card-body p-3 text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small fw-semibold text-uppercase d-block" style="font-size: 11px;">Rata-Rata Respon (Jam Kerja)</span>
                        <h3 class="fw-bold mb-0 mt-1 text-white">{{ $stats['avg_durasi'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(255, 255, 255, 0.25);">
                        <i class="fa-solid fa-stopwatch text-white fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>