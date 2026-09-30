@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Audio Permission & Title -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Daftar Pengunjung PTSP</h4>
            <p class="text-muted small mb-0">Kelola dan pantau data pengunjung PTSP secara real-time.</p>
        </div>
        <div>
            <button id="btnToggleSound" 
                    onclick="toggleAudioPermission()" 
                    class="btn {{ $isSoundActive ? 'btn-outline-success' : 'btn-outline-warning' }} border-2 fw-bold rounded-pill px-3 btn-sm">
                <i id="iconSound" class="fa-solid {{ $isSoundActive ? 'fa-volume-high' : 'fa-volume-xmark' }} me-1"></i>
                <span id="textSound">{{ $isSoundActive ? 'Izin Suara Aktif' : 'Izin Suara Belum Aktif (Klik Disini)' }}</span>
            </button>
            <audio id="notifAudio" src="{{ asset('assets/sounds/notification.mp3') }}" preload="auto"></audio>
        </div>
    </div>

    @if($isMsAceh)
        <!-- TABEL 1: KHUSUS MS ACEH -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold text-primary mb-0">
                    <i class="fa-solid fa-building-columns me-2"></i>Data Kunjungan Mahkamah Syar'iyah Aceh
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="tablePengunjungMsAceh">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th style="min-width: 220px;">Pemohon</th>
                                <th class="text-center" style="width: 170px;">Tujuan & Layanan</th>
                                <th>Keperluan</th>
                                <th class="text-center" style="width: 160px;">Tindakan / WA</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TABEL 2: KHUSUS MS DAERAH -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold text-secondary mb-0">
                    <i class="fa-solid fa-map-location-dot me-2"></i>Data Kunjungan Mahkamah Syar'iyah Kabupaten/Kota
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="tablePengunjungDaerah">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th style="min-width: 220px;">Pemohon</th>
                                <th class="text-center" style="width: 170px;">Tujuan & Layanan</th>
                                <th>Keperluan</th>
                                <th class="text-center" style="width: 160px;">Tindakan / WA</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <!-- TABEL TUNGGAL UNTUK USER SATKER DAERAH -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="tablePengunjungDaerah">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th style="min-width: 220px;">Pemohon</th>
                                <th class="text-center" style="width: 170px;">Tujuan & Layanan</th>
                                <th>Keperluan</th>
                                <th class="text-center" style="width: 160px;">Tindakan / WA</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Dynamic Modals Component -->
@foreach($pengunjung as$item)
    <!-- 1. MODAL DETAIL -->
    <div class="modal fade" id="modalDetailPengunjung{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title text-white fw-bold"><i class="fa-solid fa-id-card me-2"></i>Detail Pengunjung</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td width="35%" class="text-muted">ID Registrasi</td><td width="5%">:</td><td class="fw-bold">{{ $item->id }}</td></tr>
                        <tr><td class="text-muted">Nama Pemohon</td><td>:</td><td class="fw-bold">{{ $item->nama_responden }}</td></tr>
                        <tr><td class="text-muted">Nomor HP/WA</td><td>:</td><td>{{ $item->no_hp }}</td></tr>
                        <tr><td class="text-muted">Jenis Kelamin</td><td>:</td><td>{{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                        <tr><td class="text-muted">Pekerjaan</td><td>:</td><td>{{ $item->pekerjaan ?: '-' }}</td></tr>
                        <tr><td class="text-muted">Satker Tujuan</td><td>:</td><td>{{ $item->satker->satker_name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Jenis Layanan</td><td>:</td><td><span class="badge bg-secondary">{{ ucfirst($item->jenis_layanan) }}</span></td></tr>
                        <tr><td class="text-muted">Waktu Kunjungan</td><td>:</td><td>{{ $item->created_at ? $item->created_at->format('d F Y - H:i') . ' WIB' : '-' }}</td></tr>
                    </table>
                    <hr class="my-2">
                    <label class="fw-bold mb-1 small">Keperluan:</label>
                    <div class="p-2 bg-light border rounded small">{{ $item->keperluan ?: 'Tidak ada rincian keperluan.' }}</div>
                </div>
                <div class="modal-footer py-1"><button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>

    <!-- 2. MODAL EDIT -->
    <div class="modal fade" id="modalEditPengunjung{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow" action="{{ route('ptsp.pengunjung.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark py-2">
                    <h6 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Data Pengunjung</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-2">
                        <label class="form-label fw-bold small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_responden" class="form-control form-control-sm" value="{{ $item->nama_responden }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="no_hp" class="form-control form-control-sm" value="{{ $item->no_hp }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select form-select-sm" required>
                            <option value="L" {{ $item->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $item->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control form-control-sm" value="{{ $item->pekerjaan }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">Keperluan</label>
                        <textarea name="keperluan" class="form-control form-control-sm" rows="3">{{ $item->keperluan }}</textarea>
                    </div>
                </div>
                <div class="modal-footer py-1">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold"><i class="fa-solid fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. MODAL HAPUS -->
    <div class="modal fade" id="modalDeletePengunjung{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title text-white fw-bold"><i class="fa-solid fa-trash me-2"></i>Hapus Data Pengunjung</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-3">
                    <i class="fa-solid fa-circle-exclamation text-danger fa-2x mb-2"></i>
                    <p class="mb-1 fw-bold">Apakah Anda yakin ingin menghapus data ini?</p>
                    <p class="text-muted small mb-0">Pemohon: <strong>{{ $item->nama_responden }}</strong></p>
                </div>
                <div class="modal-footer justify-content-center py-1">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('ptsp.pengunjung.destroy', $item->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .dataTables_length select { width: auto !important; display: inline-block !important; padding: 2px 8px !important; margin: 0 5px !important; font-size: 13px; }
        .dataTables_filter input { width: auto !important; display: inline-block !important; margin-left: 5px !important; padding: 2px 8px !important; font-size: 13px; }
        .dataTables_wrapper .dataTables_paginate .page-link { border-radius: 4px !important; padding: 4px 10px !important; font-size: 12px; }
        table.dataTable td { padding: 8px 10px !important; vertical-align: middle !important; font-size: 13px; }
        table.dataTable thead th { padding: 8px 10px !important; vertical-align: middle !important; font-size: 13px; font-weight: 600; }
    </style>
    <!-- PATH BARU (BENAR) -->
    @endpush
    
@push('scripts')
    <audio id="notifAudio" src="{{ asset('assets/sounds/notification.mp3') }}" preload="auto"></audio>

    <!-- CDN DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- 1. SCRIPT FUNGSI DITINDAKLANJUTI (AJAX ACTION) -->
    <script>
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
                    const btn = document.getElementById(`btn-wa-${id}`);
                    if (btn) {
                        btn.className = 'btn btn-sm btn-outline-success fw-bold px-2 py-1';
                        btn.style.fontSize = '11px';
                        btn.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i>Ditindaklanjuti';
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

    <!-- 2. SCRIPT INISIALISASI DATATABLES -->
    <script>
        window.tableMsAceh = window.tableMsAceh || null;
        window.tableDaerah = window.tableDaerah || null;

        $(document).ready(function() {
            const columnsConfig = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'pemohon_html', name: 'pemohon_html' },
                { data: 'tujuan_layanan_html', name: 'satker.satker_short_name', className: 'text-center' },
                { data: 'keperluan', name: 'keperluan' },
                { data: 'kontak_wa_html', name: 'no_hp', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ];

            @if($isMsAceh)
                window.tableMsAceh = $('#tablePengunjungMsAceh').DataTable({
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('ptsp.pengunjung.index') }}",
                        data: { type: 'ms_aceh' }
                    },
                    columns: columnsConfig
                });
            @endif

            window.tableDaerah = $('#tablePengunjungDaerah').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('ptsp.pengunjung.index') }}",
                    data: { type: 'daerah' }
                },
                columns: columnsConfig
            });
        });
    </script>

    <!-- 3. SCRIPT MANAGEMENT AUDIO NOTIFIKASI -->
    <script>
        // Status dari DB
        window.soundEnabled = {{ $isSoundActive ? 'true' : 'false' }};
        window.audioUnlocked = false;

        // Fungsi untuk unlock audio browser via interaksi user
        function unlockAudioContext() {
            const notifAudio = document.getElementById('notifAudio');
            if (!notifAudio || window.audioUnlocked) return;

            notifAudio.play().then(() => {
                notifAudio.pause();
                notifAudio.currentTime = 0;
                window.audioUnlocked = true;
                console.log('%c✅ DEBUG: Audio browser berhasil di-unlock via interaksi user!', 'color: #00ff00; font-weight: bold;');
            }).catch(e => {
                console.warn('⚠️ Menunggu klik user untuk unlock audio:', e);
            });
        }

        // Pasang listener di seluruh body (klik pertama akan langsung unlock audio)
        document.addEventListener('click', unlockAudioContext, { once: true });
        document.addEventListener('keydown', unlockAudioContext, { once: true });

        function updateButtonUI(isActive) {
            const btn = document.getElementById('btnToggleSound');
            const icon = document.getElementById('iconSound');
            const text = document.getElementById('textSound');

            if (!btn || !icon || !text) return;

            if (isActive) {
                btn.className = 'btn btn-outline-success border-2 fw-bold rounded-pill px-3 btn-sm';
                icon.className = 'fa-solid fa-volume-high me-1';
                text.innerText = 'Izin Suara Aktif';
            } else {
                btn.className = 'btn btn-outline-warning border-2 fw-bold rounded-pill px-3 btn-sm';
                icon.className = 'fa-solid fa-volume-xmark me-1';
                text.innerText = 'Izin Suara Belum Aktif (Klik Disini)';
            }
        }

        function toggleAudioPermission() {
            // Interaksi klik tombol ini otomatis unlock audio
            unlockAudioContext();

            fetch("{{ route('ptsp.pengunjung.toggle-sound') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.soundEnabled = data.is_active;
                    updateButtonUI(data.is_active);

                    if (data.is_active) {
                        playNotifSound(); // Uji coba suara

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Suara Berhasil Diaktifkan!',
                                text: 'Uji coba suara sedang dimainkan.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Suara Dinonaktifkan',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    }
                }
            })
            .catch(err => console.error('Error toggling sound:', err));
        }

        function playNotifSound() {
            const notifAudio = document.getElementById('notifAudio');
            if (window.soundEnabled && notifAudio) {
                notifAudio.currentTime = 0;
                
                // Mengatasi Play Promise Error dari Browser Autoplay Policy
                const promise = notifAudio.play();
                if (promise !== undefined) {
                    promise.then(() => {
                        console.log('🔔 Suara notifikasi berhasil diputar!');
                    }).catch(error => {
                        console.error('❌ Browser memblokir suara otomatis:', error);
                        console.warn('👉 Klik di mana saja pada layar sekali agar browser mengizinkan suara.');
                    });
                }
            }
        }
    </script>

    <!-- 4. SCRIPT REAL-TIME POLLING & AUTO RELOAD -->
    <script>
        window.lastMsAcehId = window.lastMsAcehId || null;
        window.lastDaerahId = window.lastDaerahId || null;
        window.lastLatestId = window.lastLatestId || null;

        function checkNewData() {
            fetch("{{ route('ptsp.pengunjung.check-new') }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    let hasNewData = false;

                    if (data.is_ms_aceh) {
                        // Cek Tabel MS Aceh
                        if (window.lastMsAcehId === null) {
                            window.lastMsAcehId = data.latest_ms_aceh_id;
                        } else if (data.latest_ms_aceh_id && window.lastMsAcehId !== data.latest_ms_aceh_id) {
                            console.log('🔔 Data Baru Terdeteksi di MS Aceh:', data.latest_ms_aceh_id);
                            window.lastMsAcehId = data.latest_ms_aceh_id;
                            hasNewData = true;

                            if (window.tableMsAceh) {
                                window.tableMsAceh.ajax.reload(null, false);
                            }
                        }

                        // Cek Tabel Daerah
                        if (window.lastDaerahId === null) {
                            window.lastDaerahId = data.latest_daerah_id;
                        } else if (data.latest_daerah_id && window.lastDaerahId !== data.latest_daerah_id) {
                            console.log('🔔 Data Baru Terdeteksi di Satker Daerah:', data.latest_daerah_id);
                            window.lastDaerahId = data.latest_daerah_id;
                            hasNewData = true;

                            if (window.tableDaerah) {
                                window.tableDaerah.ajax.reload(null, false);
                            }
                        }

                    } else {
                        // Satker Daerah
                        if (window.lastLatestId === null) {
                            window.lastLatestId = data.latest_id;
                        } else if (data.latest_id && window.lastLatestId !== data.latest_id) {
                            console.log('🔔 Data Baru Terdeteksi:', data.latest_id);
                            window.lastLatestId = data.latest_id;
                            hasNewData = true;

                            if (window.tableDaerah) {
                                window.tableDaerah.ajax.reload(null, false);
                            }
                        }
                    }

                    // Bunyikan suara
                    if (hasNewData) {
                        playNotifSound();
                    }
                }
            })
            .catch(err => console.error('Error checking new data:', err))
            .finally(() => {
                scheduleNextCheck();
            });
        }

        function scheduleNextCheck() {
            const minSeconds = 3;
            const maxSeconds = 7;
            const randomTime = Math.floor(Math.random() * ((maxSeconds - minSeconds) * 1000 + 1)) + (minSeconds * 1000);

            setTimeout(function () {
                checkNewData();
            }, randomTime);
        }

        $(document).ready(function() {
            checkNewData();
        });
    </script>
@endpush