@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- HEADER HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color: #064e3b;">Rekap & Monitoring Pengunjung PTSP</h3>
            <p class="text-muted small mb-0">
                @php
                    $user = Auth::user();
                    $satkerName = strtolower($user->satker->satker_name ?? '');
                    // Gunakan str_contains tanpa backslash manual pada string 'syari'
                    $isMsAceh = ($user->role === 'admin') || str_contains($satkerName, 'mahkamah syari') || str_contains($satkerName, 'ms aceh');
                @endphp

                @if($isMsAceh)
                    Monitoring seluruh laporan data pengunjung (Pesan & Telepon) dari seluruh Satker se-Aceh via Portal SAPA.
                @else
                    Monitoring daftar pemohon layanan komunikasi (Pesan & Telepon) untuk {{ $satkerName ?: 'Satker Anda' }}.
                @endif
            </p>
        </div>
    </div>

    <!-- ALERT MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- TOMBOL & AUDIO NOTIFIKASI -->
    <div class="mb-3 d-flex align-items-center gap-2">
        <button id="btnToggleSound" class="btn btn-sm btn-outline-success">
            <i class="fa-solid fa-volume-xmark me-1" id="iconSound"></i> 
            <span id="textSound">Klik untuk Mengaktifkan Suara Notifikasi</span>
        </button>
    </div>

    <audio id="notifAudio" preload="auto">
        <source src="{{ asset('assets/sounds/notification.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- TABEL DATA PENGUNJUNG -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-nowrap">
                            <th style="width: 50px;">No</th>
                            <th>Nama Pemohon</th>
                            <th>Satker Tujuan</th>
                            <th>Layanan</th>
                            <th>Keperluan</th>
                            <th class="text-center">Kontak WA</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pengunjungTableBody">
                        @include('Pages.PTSP.partials.pengunjung_table_body')
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $pengunjung->links() }}
            </div>
        </div>
    </div>
</div>

<!-- ========================== MODALS ACTION ========================== -->
@foreach($pengunjung as $index =>$item)
    <!-- 1. MODAL DETAIL -->
    <div class="modal fade" id="modalDetailPengunjung{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white"><i class="fa-solid fa-id-card me-2"></i>Detail Pengunjung</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <table class="table table-sm table-borderless">
                        <tr><td width="35%" class="text-muted">Nama Pemohon</td><td width="5%">:</td><td class="fw-bold">{{ $item->nama_responden }}</td></tr>
                        <tr><td class="text-muted">Nomor HP/WA</td><td>:</td><td>{{ $item->no_hp }}</td></tr>
                        <tr><td class="text-muted">Jenis Kelamin</td><td>:</td><td>{{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                        <tr><td class="text-muted">Pekerjaan</td><td>:</td><td>{{ $item->pekerjaan ?: '-' }}</td></tr>
                        <tr><td class="text-muted">NIK / KTP</td><td>:</td><td>{{ $item->nik ?: '-' }}</td></tr>
                        <tr><td class="text-muted">Satker Tujuan</td><td>:</td><td>{{ $item->satker->satker_name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Jenis Layanan</td><td>:</td><td><span class="badge bg-secondary">{{ ucfirst($item->jenis_layanan) }}</span></td></tr>
                        <tr><td class="text-muted">Waktu Kunjungan</td><td>:</td><td>{{ $item->created_at->format('d F Y - H:i') }} WIB</td></tr>
                    </table>
                    <hr>
                    <label class="fw-bold mb-1">Keperluan:</label>
                    <div class="p-2 bg-light border rounded small">{{ $item->keperluan ?: 'Tidak ada rincian keperluan.' }}</div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>

    <!-- 2. MODAL EDIT -->
    <div class="modal fade" id="modalEditPengunjung{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow" action="{{ route('ptsp.pengunjung.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Data Pengunjung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_responden" class="form-control" value="{{ $item->nama_responden }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="no_hp" class="form-control" value="{{ $item->no_hp }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" {{ $item->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $item->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control" value="{{ $item->pekerjaan }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIK / No. KTP</label>
                        <input type="text" name="nik" class="form-control" maxlength="16" value="{{ $item->nik }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keperluan</label>
                        <textarea name="keperluan" class="form-control" rows="3">{{ $item->keperluan }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold"><i class="fa-solid fa-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. MODAL HAPUS -->
    <div class="modal fade" id="modalDeletePengunjung{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white"><i class="fa-solid fa-trash me-2"></i>Hapus Data Pengunjung</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fa-solid fa-circle-exclamation text-danger fa-3x mb-3"></i>
                    <h5>Apakah Anda yakin ingin menghapus data ini?</h5>
                    <p class="text-muted mb-0">Pemohon: <strong>{{ $item->nama_responden }}</strong></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('ptsp.pengunjung.destroy', $item->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

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
            if(data.success) {
                const btn = document.getElementById(`btn-wa-${id}`);
                if (btn) {
                    btn.classList.remove('btn-danger');
                    btn.classList.add('btn-success');
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    document.addEventListener('DOMContentLoaded', function () {
        const notifAudio = document.getElementById('notifAudio');
        const btnToggleSound = document.getElementById('btnToggleSound');
        const iconSound = document.getElementById('iconSound');
        const textSound = document.getElementById('textSound');

        let soundEnabled = false;
        let lastLatestId = null;

        // 1. Fungsi Cek & Minta Izin Suara/Notifikasi Browser
        function checkAndRequestAudioPermission() {
            // Cek apakah browser mendukung Audio Context
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                const audioCtx = new AudioContext();
                
                if (audioCtx.state === 'suspended') {
                    // Jika audio masih diblokir browser, ubah tampilan tombol agar user sadar
                    textSound.innerText = '⚠️ Izin Suara Belum Aktif (Klik Disini)';
                    btnToggleSound.classList.remove('btn-outline-success');
                    btnToggleSound.classList.add('btn-warning', 'text-dark');
                } else {
                    activateAudioState();
                }
            }
        }

        function activateAudioState() {
            notifAudio.play().then(() => {
                notifAudio.pause();
                notifAudio.currentTime = 0;
                soundEnabled = true;
                
                btnToggleSound.classList.remove('btn-outline-success', 'btn-warning');
                btnToggleSound.classList.add('btn-success', 'text-white');
                iconSound.className = 'fa-solid fa-volume-high me-1';
                textSound.innerText = 'Suara Notifikasi Aktif';
            }).catch(err => {
                console.log('Menunggu interaksi pengguna untuk izin audio...');
            });
        }

        // Cek status izin saat halaman dimuat
        checkAndRequestAudioPermission();

        // Tombol manual untuk memberikan izin
        btnToggleSound.addEventListener('click', function() {
            activateAudioState();
        });

        // Berikan izin otomatis saat ada interaksi klik di mana saja pada halaman
        document.body.addEventListener('click', function() {
            if (!soundEnabled) {
                activateAudioState();
            }
        }, { once: true });

        // 2. Play Audio Notifikasi
        function playNotifSound() {
            if (soundEnabled) {
                notifAudio.currentTime = 0;
                notifAudio.play().catch(e => {
                    console.error('Gagal memutar audio, minta ulang izin:', e);
                    soundEnabled = false;
                    checkAndRequestAudioPermission();
                });
            }
        }

        // 3. Pengecekan Data Pengunjung Baru & Reload Tabel Otomatis
        function checkNewData() {
            fetch("{{ route('ptsp.pengunjung.check-new') }}?load_table=1", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.latest_id) {
                    if (lastLatestId === null) {
                        lastLatestId = data.latest_id;
                    } 
                    else if (lastLatestId !== data.latest_id) {
                        lastLatestId = data.latest_id;
                        
                        // Putar Suara
                        playNotifSound();

                        // Perbarui tabel secara instan
                        if (data.html) {
                            document.getElementById('pengunjungTableBody').innerHTML = data.html;
                        }
                    }
                }
            })
            .catch(err => console.error('Error checking new data:', err));
        }

        checkNewData();

        // 4. Interval Acak (1 sampai 5 Menit)
        function scheduleNextCheck() {
            const minMinutes = 1;
            const maxMinutes = 5;
            const randomTime = Math.floor(Math.random() * ((maxMinutes - minMinutes) * 60 * 1000 + 1)) + (minMinutes * 60 * 1000);

            console.log(`Pengecekan pengunjung berikutnya dalam ${Math.round(randomTime / 1000)} detik.`);

            setTimeout(function () {
                checkNewData();
                scheduleNextCheck();
            }, randomTime);
        }

        scheduleNextCheck();
    });
</script>
@endsection