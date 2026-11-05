@extends('Layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">    
    <style>
        .card { border-radius: 12px !important; overflow: hidden; }
        .card-header { padding: 0 1.25rem !important; background-color: #ffffff !important; }
        .nav-tabs { border-bottom: none !important; margin-bottom: 0 !important; }
        .nav-tabs .nav-link { 
            border: none !important; 
            border-bottom: 3px solid transparent !important; 
            border-radius: 0 !important; 
            padding: 12px 18px !important; 
            font-size: 14px; font-weight: 600;
            color: #64748b !important; background: transparent !important;
            transition: all 0.2s ease-in-out; margin-bottom: -1px;
        }
        .nav-tabs .nav-link:hover { color: #2563eb !important; border-bottom-color: #cbd5e1 !important; }
        .nav-tabs .nav-link.active { color: #2563eb !important; border-bottom-color: #2563eb !important; }
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { padding-top: 0.5rem; padding-bottom: 0.75rem; margin-bottom: 0.25rem; }
        .dataTables_length label, .dataTables_filter label { font-size: 13px; font-weight: 500; color: #475569; display: inline-flex; align-items: center; gap: 6px; }
        .dataTables_length select { width: auto !important; display: inline-block !important; padding: 4px 10px !important; font-size: 13px; border-radius: 8px !important; border: 1px solid #cbd5e1 !important; outline: none; }
        .dataTables_filter input { width: 200px !important; display: inline-block !important; padding: 4px 12px !important; font-size: 13px; border-radius: 8px !important; border: 1px solid #cbd5e1 !important; outline: none; transition: all 0.2s ease; }
        .dataTables_filter input:focus, .dataTables_length select:focus { border-color: #2563eb !important; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important; }
        table.dataTable { border-collapse: separate !important; border-spacing: 0 !important; margin-top: 0 !important; }
        table.dataTable thead th { background-color: #f8fafc !important; color: #334155 !important; padding: 12px 14px !important; vertical-align: middle !important; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0 !important; border-top: 1px solid #e2e8f0 !important; }
        table.dataTable td { padding: 12px 14px !important; vertical-align: middle !important; font-size: 13px; color: #334155; border-bottom: 1px solid #f1f5f9 !important; }
        table.dataTable tbody tr:hover { background-color: #f8fafc !important; }
        .dataTables_wrapper .dataTables_paginate .page-link { border-radius: 6px !important; padding: 5px 11px !important; font-size: 12px; margin: 0 2px; }
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link { background-color: #2563eb !important; border-color: #2563eb !important; }
        .select2-container--bootstrap-5 { z-index: 1060 !important; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header Title & Sound Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1">Daftar Pengunjung PTSP</h4>
                <p class="text-muted small mb-0">Kelola dan pantau data pengunjung PTSP secara real-time.</p>
            </div>
            <div>
                <button id="btnToggleSound" onclick="toggleAudioPermission()" class="btn {{ $isSoundActive ? 'btn-outline-success' : 'btn-outline-warning' }} border-2 fw-bold rounded-pill px-3 btn-sm">
                    <i id="iconSound" class="fa-solid {{ $isSoundActive ? 'fa-volume-high' : 'fa-volume-xmark' }} me-1"></i>
                    <span id="textSound">{{ $isSoundActive ? 'Izin Suara Aktif' : 'Izin Suara Belum Aktif (Klik Disini)' }}</span>
                </button>
                <audio id="notifAudio" src="{{ asset('assets/sounds/notification.mp3') }}" preload="auto"></audio>
            </div>
        </div>

        <!-- 📊 STATS CARDS -->
        @include('Pages.PTSP.pengunjung.partials._stats_cards')

        <!-- HEADER DATA KUNJUNGAN & TOMBOL AKSI -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
            <div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold" id="labelActiveFilter">
                    <i class="fa-solid fa-calendar-week me-1"></i> Mode: 1 Minggu Terakhir (Default)
                </span>
            </div>
            <div class="d-flex flex-wrap gap-2 w-100 w-md-auto ms-auto">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFilterTanggal">
                    <i class="fas fa-filter me-1"></i> Filter Periode
                </button>
                <button type="button" class="btn btn-danger btn-sm px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFilterPdf">
                    <i class="fas fa-file-pdf me-1"></i> Cetak Laporan PDF
                </button>
                <button type="button" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPengunjungFisik">
                    <i class="fas fa-user-plus me-1"></i> Tambah Pengunjung Fisik
                </button>
            </div>
        </div>

        <!-- 📑 CARD TABEL KUNJUNGAN -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-bottom pt-2 px-3 pb-0">
                <ul class="nav nav-tabs card-header-tabs" id="ptspTab" role="tablist">
                    @if($isMsAceh)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-ms-aceh-btn" data-bs-toggle="tab" data-bs-target="#tab-ms-aceh" type="button" role="tab">
                                <i class="fa-solid fa-building-columns me-2"></i>MS Aceh
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-daerah-btn" data-bs-toggle="tab" data-bs-target="#tab-daerah" type="button" role="tab">
                                <i class="fa-solid fa-map-location-dot me-2"></i>MS Kabupaten / Kota
                            </button>
                        </li>
                    @else
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-daerah-btn" data-bs-toggle="tab" data-bs-target="#tab-daerah" type="button" role="tab">
                                <i class="fa-solid fa-building me-2"></i>Data Kunjungan Satker
                            </button>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="card-body p-3">
                <div class="tab-content" id="ptspTabContent">
                    @if($isMsAceh)
                        <div class="tab-pane fade show active" id="tab-ms-aceh" role="tabpanel">
                            @include('Pages.PTSP.pengunjung.partials._table_ms_aceh')
                        </div>
                    @endif

                    <div class="tab-pane fade {{ !$isMsAceh ? 'show active' : '' }}" id="tab-daerah" role="tabpanel">
                        @include('Pages.PTSP.pengunjung.partials._table_daerah')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔍 MODAL FILTER PERIODE TANGGAL DINAMIS -->
    <div class="modal fade" id="modalFilterTanggal" tabindex="-1" aria-labelledby="modalFilterTanggalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header bg-light border-0">
                    <h6 class="modal-title fw-bold mb-0" id="modalFilterTanggalTitle">
                        <i class="fa-solid fa-filter text-primary me-2"></i>Filter Periode Kunjungan
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Pilih Mode Filter</label>
                        <select id="selectModeFilter" class="form-select form-select-sm" onchange="switchFilterMode(this.value)">
                            <option value="week" selected>1 Minggu Terakhir (Default)</option>
                            <option value="month">Per Bulan</option>
                            <option value="triwulan">Per Triwulan</option>
                            <option value="custom">Rentang Tanggal Custom</option>
                        </select>
                    </div>

                    <hr class="my-3">

                    <!-- CONTAINER PER BULAN -->
                    <div id="containerFilterBulan" class="d-none">
                        <div class="row g-2">
                            <div class="col-7">
                                <label class="form-label fw-semibold small text-muted">Bulan</label>
                                <select id="filterSelectBulan" class="form-select form-select-sm">
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-5">
                                <label class="form-label fw-semibold small text-muted">Tahun</label>
                                <select id="filterTahunBulan" class="form-select form-select-sm">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- CONTAINER PER TRIWULAN -->
                    <div id="containerFilterTriwulan" class="d-none">
                        <div class="row g-2">
                            <div class="col-7">
                                <label class="form-label fw-semibold small text-muted">Triwulan</label>
                                <select id="filterSelectTriwulan" class="form-select form-select-sm">
                                    <option value="1">Triwulan I (Jan - Mar)</option>
                                    <option value="2">Triwulan II (Apr - Jun)</option>
                                    <option value="3">Triwulan III (Jul - Sep)</option>
                                    <option value="4">Triwulan IV (Okt - Des)</option>
                                </select>
                            </div>
                            <div class="col-5">
                                <label class="form-label fw-semibold small text-muted">Tahun</label>
                                <select id="filterTahunTriwulan" class="form-select form-select-sm">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- CONTAINER CUSTOM RANGE -->
                    <div id="containerFilterCustom" class="d-none">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold small text-muted">Dari Tanggal</label>
                                <input type="date" id="filterStartDate" class="form-control form-control-sm" autocomplete="off">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small text-muted">Sampai Tanggal</label>
                                <input type="date" id="filterEndDate" class="form-control form-control-sm" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- INFO DISPLAY DEFAULT WEEK -->
                    <div id="containerFilterWeek" class="alert alert-info py-2 px-3 mb-0 small">
                        <i class="fa-solid fa-circle-info me-1"></i> Menampilkan data kunjungan 7 hari terakhir secara otomatis.
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-danger fw-bold" onclick="resetFilterPeriode()">
                        <i class="fa-solid fa-rotate-left me-1"></i>Reset (Default)
                    </button>
                    <div>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-sm btn-primary fw-bold px-3" onclick="submitFilterPeriode()">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals Partials Static & Dynamic -->
    @include('Pages.PTSP.pengunjung.partials._modal_tambah_fisik')
    @include('Pages.PTSP.pengunjung.partials._modal_filter_pdf')
    @foreach($pengunjung as $item)
        @include('Pages.PTSP.pengunjung.partials._modal_dynamic_item', ['item' => $item])
    @endforeach
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // VARIABLE GLOBAL FILTER PERIODE TANGGAL
        window.filterStartDate = '';
        window.filterEndDate = '';

        function reloadActiveDataTables() {
            if (window.tableMsAceh && typeof window.tableMsAceh.ajax !== 'undefined') {
                window.tableMsAceh.ajax.reload(null, false);
            }
            if (window.tableDaerah && typeof window.tableDaerah.ajax !== 'undefined') {
                window.tableDaerah.ajax.reload(null, false);
            }
        }

        // SWAP TAMPILAN ELEMENT FORM DI MODAL
        function switchFilterMode(mode) {
            $('#containerFilterBulan, #containerFilterTriwulan, #containerFilterCustom, #containerFilterWeek').addClass('d-none');
            
            if (mode === 'month') {
                $('#containerFilterBulan').removeClass('d-none');
            } else if (mode === 'triwulan') {
                $('#containerFilterTriwulan').removeClass('d-none');
            } else if (mode === 'custom') {
                $('#containerFilterCustom').removeClass('d-none');
            } else {
                $('#containerFilterWeek').removeClass('d-none');
            }
        }

        // FORMATTER TANGGAL ISO YYYY-MM-DD
        function formatDateISO(year, monthIndex, day) {
            const y = year;
            const m = String(monthIndex + 1).padStart(2, '0');
            const d = String(day).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        // SUBMIT FILTER PERIODE
        function submitFilterPeriode() {
            const mode = $('#selectModeFilter').val();
            let labelText = '1 Minggu Terakhir (Default)';

            if (mode === 'month') {
                const bulanInt = parseInt($('#filterSelectBulan').val()); // 1 s/d 12
                const tahunInt = parseInt($('#filterTahunBulan').val());
                const namaBulan = $('#filterSelectBulan option:selected').text();
                
                // First day & Last day
                const firstDay = new Date(tahunInt, bulanInt - 1, 1);
                const lastDay = new Date(tahunInt, bulanInt, 0); // 0 = Hari terakhir bulan sebelumnya
                
                window.filterStartDate = formatDateISO(firstDay.getFullYear(), firstDay.getMonth(), firstDay.getDate());
                window.filterEndDate = formatDateISO(lastDay.getFullYear(), lastDay.getMonth(), lastDay.getDate());
                labelText = `Bulan ${namaBulan} ${tahunInt}`;

            } else if (mode === 'triwulan') {
                const triwulanInt = parseInt($('#filterSelectTriwulan').val());
                const tahunInt = parseInt($('#filterTahunTriwulan').val());
                
                let startMonthIdx = (triwulanInt - 1) * 3; // 0, 3, 6, 9
                let endMonthIdx = startMonthIdx + 2;       // 2, 5, 8, 11
                
                const firstDay = new Date(tahunInt, startMonthIdx, 1);
                const lastDay = new Date(tahunInt, endMonthIdx + 1, 0);
                
                window.filterStartDate = formatDateISO(firstDay.getFullYear(), firstDay.getMonth(), firstDay.getDate());
                window.filterEndDate = formatDateISO(lastDay.getFullYear(), lastDay.getMonth(), lastDay.getDate());
                labelText = `Triwulan ${triwulanInt} (${tahunInt})`;

            } else if (mode === 'custom') {
                window.filterStartDate = $('#filterStartDate').val();
                window.filterEndDate = $('#filterEndDate').val();

                if (!window.filterStartDate || !window.filterEndDate) {
                    alert('Harap tentukan "Dari Tanggal" dan "Sampai Tanggal" terlebih dahulu!');
                    return;
                }
                labelText = `Custom (${window.filterStartDate} s/d ${window.filterEndDate})`;

            } else {
                window.filterStartDate = '';
                window.filterEndDate = '';
                labelText = '1 Minggu Terakhir (Default)';
            }

            $('#labelActiveFilter').html(`<i class="fa-solid fa-filter me-1"></i> Mode: ${labelText}`);
            reloadActiveDataTables();
            $('#modalFilterTanggal').modal('hide');
        }

        function resetFilterPeriode() {
            $('#selectModeFilter').val('week');
            switchFilterMode('week');
            
            const currentMonth = new Date().getMonth() + 1;
            $('#filterSelectBulan').val(currentMonth);
            $('#filterStartDate').val('');
            $('#filterEndDate').val('');
            
            window.filterStartDate = '';
            window.filterEndDate = '';

            $('#labelActiveFilter').html(`<i class="fa-solid fa-calendar-week me-1"></i> Mode: 1 Minggu Terakhir (Default)`);
            reloadActiveDataTables();
            $('#modalFilterTanggal').modal('hide');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const currentMonth = new Date().getMonth() + 1;
            $('#filterSelectBulan').val(currentMonth);

            const tabElList = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabElList.forEach(function(tabEl) {
                tabEl.addEventListener('shown.bs.tab', function(event) {
                    const targetId = event.target.getAttribute('data-bs-target');
                    if (targetId === '#tab-ms-aceh' && window.tableMsAceh) {
                        window.tableMsAceh.columns.adjust().draw(false);
                    } else if (targetId === '#tab-daerah' && window.tableDaerah) {
                        window.tableDaerah.columns.adjust().draw(false);
                    }
                });
            });
        });

        function markAsFollowedUp(id) {
            fetch(`/ptsp/pengunjung/${id}/tindak-lanjut`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    reloadActiveDataTables();
                } else {
                    alert(data.message || 'Gagal memperbarui status.');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function selesaiMelayaniFisik(id) {
            Swal.fire({
                title: 'Selesai Melayani?',
                text: 'Apakah pelayanan fisik untuk pengunjung ini sudah selesai?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa-solid fa-check me-1"></i>Ya, Selesai!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyimpan status pelayanan',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch(`/ptsp/pengunjung/${id}/tindak-lanjut`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            reloadActiveDataTables();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Pelayanan berhasil diselesaikan.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Gagal memperbarui status.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Akses Ditolak / Terjadi Kesalahan',
                            text: error.message || 'Terjadi kesalahan sistem.'
                        });
                    });
                }
            });
        }
    </script>
@endpush