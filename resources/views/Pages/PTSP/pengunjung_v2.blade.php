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

        <!-- 📊 CARD STATISTIK KINERJA LAYANAN PTSP -->
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

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                <h5 class="mb-0"><i class="fas fa-university me-2"></i>Data Kunjungan</h5>
            </div>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                    <!-- Tombol Cetak PDF -->
                    <button type="button" class="btn btn-danger w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalFilterPdf">
                        <i class="fas fa-file-pdf me-1"></i> Cetak Laporan PDF
                    </button>
                    <!-- Tombol Tambah Pengunjung -->
                    <button type="button" class="btn btn-primary w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalTambahPengunjungFisik">
                        <i class="fas fa-user-plus me-1"></i> Tambah Pengunjung Fisik
                    </button>
                </div>
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
        @foreach($pengunjung as $item)
            <!-- 1. MODAL DETAIL PENGUNJUNG -->
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
                                <tr><td class="text-muted">Satker Asal</td><td>:</td><td>{{ $item->satker->satker_name ?? '-' }}</td></tr>
                                @if($item->satker_tujuan_id && $item->satker_tujuan_id !== $item->satker_id)
                                    <tr>
                                        <td class="text-muted">Satker Disposisi</td>
                                        <td>:</td>
                                        <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $item->satkerTujuan->satker_name ?? '-' }}</span></td>
                                    </tr>
                                @endif
                                <tr><td class="text-muted">Jenis Layanan</td><td>:</td><td><span class="badge bg-secondary">{{ ucfirst($item->jenis_layanan) }}</span></td></tr>
                                <tr><td class="text-muted">Waktu Kunjungan</td><td>:</td><td>{{ $item->created_at ? $item->created_at->format('d F Y - H:i') . ' WIB' : '-' }}</td></tr>
                                <tr><td class="text-muted">Durasi Respon</td><td>:</td><td><span class="badge bg-light text-dark border">{{ $item->format_durasi }}</span></td></tr>
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

            <!-- 4. MODAL DISPOSISI / ALIHKAN SATKER -->
            <div class="modal fade" id="modalDisposisiPengunjung{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form class="modal-content border-0 shadow" action="{{ route('ptsp.pengunjung.disposisi', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-info text-white py-2">
                            <h6 class="modal-title fw-bold text-white"><i class="fa-solid fa-share-nodes me-2"></i>Disposisi / Dialihkan Ke Satker Lain</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-start">
                            <div class="alert alert-warning py-2 px-3 small mb-3">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                Gunakan fitur ini jika pemohon <strong>salah alamat</strong> dan seharusnya menghubungi Satker lain.
                            </div>

                            <table class="table table-sm table-borderless mb-2 small">
                                <tr>
                                    <td width="35%" class="text-muted">Nama Pemohon</td>
                                    <td width="5%">:</td>
                                    <td class="fw-bold">{{ $item->nama_responden }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Satker Asal/Saat Ini</td>
                                    <td>:</td>
                                    <td><span class="badge bg-secondary">{{ $item->satker->satker_short_name ?? '-' }}</span></td>
                                </tr>
                            </table>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Pilih Satker Tujuan Baru <span class="text-danger">*</span></label>
                                <select name="satker_tujuan_id" class="form-select form-select-sm" required>
                                    <option value="" selected disabled>-- Pilih Satker Tujuan --</option>
                                    @foreach($satkers as $satker)
                                        @if($satker->id !== $item->satker_id)
                                            <option value="{{ $satker->id }}">
                                                {{ $satker->satker_name }} ({{ $satker->satker_short_name }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer py-1">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-info text-white fw-bold"><i class="fa-solid fa-paper-plane me-1"></i> Kirim Disposisi</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    <!-- Dynamic Modals Component -->

    <!-- Modal Tambah Pengunjung Fisik -->
    <div class="modal fade" id="modalTambahPengunjungFisik" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTambahLabel"><i class="fas fa-walking me-2"></i>Input Pengunjung Fisik (Tatap Muka)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('ptsp.pengunjung.store-offline') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Nama Responden -->
                            <div class="col-md-6">
                                <label class="form-label required">Nama Responden / Pemohon</label>
                                <input type="text" name="nama_responden" class="form-control" placeholder="Nama lengkap..." required>
                            </div>

                            <!-- No HP / WhatsApp -->
                            <div class="col-md-6">
                                <label class="form-label required">No. HP / WhatsApp</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
                            </div>

                            <!-- NIK -->
                            <div class="col-md-6">
                                <label class="form-label">NIK (KTP)</label>
                                <input type="text" name="nik" maxlength="16" class="form-control" placeholder="16 digit NIK...">
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="text" name="email" class="form-control" placeholder="contoh@email.com">
                                <small class="form-text text-muted">*Jika tidak ada email, isi dengan tanda <strong>-</strong></small>
                            </div>        

                            <!-- Domisili Kab/Kota (Panggil dari DB Kabkot) -->
                            <div class="col-md-6">
                                <label class="form-label required">Kabupaten / Kota Domisili</label>
                                <select name="kabkot_id" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Kabupaten/Kota --</option>
                                    @foreach($kabkots as $kabkot)
                                        <option value="{{ $kabkot->id }}" data-satker="{{ $kabkot->satker_id }}">
                                            {{ $kabkot->nama_kabkot }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Pekerjaan (Panggil dari DB Pekerjaan) -->
                            <div class="col-md-6">
                                <label class="form-label required">Pekerjaan</label>
                                <select name="pekerjaan" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Pekerjaan --</option>
                                    @foreach($pekerjaans as $p)
                                        <option value="{{ $p->nama_pekerjaan }}">{{ $p->nama_pekerjaan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div class="col-md-4">
                                <label class="form-label required">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <!-- Usia -->
                            <div class="col-md-4">
                                <label class="form-label required">Usia</label>
                                <input type="text" name="usia" class="form-control" placeholder="Contoh: 30 Tahun" required>
                            </div>

                            <!-- Pendidikan -->
                            <div class="col-md-4">
                                <label class="form-label required">Pendidikan Terakhir</label>
                                <select name="pendidikan" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih --</option>
                                    <option value="SD">SD / Sederajat</option>
                                    <option value="SMP">SMP / Sederajat</option>
                                    <option value="SMA">SMA / Sederajat</option>
                                    <option value="D3">D3 / D4</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2 / S3</option>
                                </select>
                            </div>

                            <!-- Pilihan Kategori Layanan -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label required">Layanan / Keperluan</label>
                                <select id="kategori_layanan" class="form-control" required>
                                    <option value="">-- Pilih Jenis Layanan --</option>
                                    <option value="syarat_perkara">Syarat Perkara</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <!-- Container 1: Jenis Perkara (Awalnya Tersembunyi) -->
                            <div class="col-md-12 mb-3" id="wrapper_jenis_perkara" style="display: none;">
                                <label for="jenis_perkara_id" class="form-label">Jenis Perkara</label>
                                <select name="jenis_perkara_id" id="jenis_perkara_id" class="form-control">
                                    <option value="">-- Pilih Jenis Perkara --</option>
                                    @foreach($jenisPerkaras as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->nama_layanan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Container 2: Textarea Keperluan (Awalnya Tersembunyi) -->
                            <div class="col-md-12 mb-3" id="wrapper_keperluan" style="display: none;">
                                <label class="form-label">Penjelasan Keperluan / Layanan</label>
                                <textarea name="keperluan" id="keperluan" class="form-control" rows="3" placeholder="Jelaskan maksud dan tujuan kedatangan..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Data Pengunjung</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Filter PDF -->
    <div class="modal fade" id="modalFilterPdf" tabindex="-1" aria-labelledby="modalFilterPdfLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalFilterPdfLabel">
                        <i class="fas fa-file-pdf me-2"></i>Cetak Laporan Pengunjung PTSP
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('ptsp.pengunjung.download_pdf') }}" method="POST" target="_blank">
                    @csrf
                    <div class="modal-body">
                        
                        @if($isMsAceh)
                            <!-- Select Filter Satker Khusus User MS Aceh -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Satker</label>
                                <select name="satker_id" id="satker_id" class="form-select" required>
                                    <option value="all" selected>-- Seluruh Satker (MS Aceh & Satker Daerah) --</option>
                                    @foreach($satkers as$stk)
                                        <option value="{{ $stk->id }}">{{ $stk->satker_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Select Tipe Periode -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Tipe Laporan</label>
                            <select name="tipe_periode" id="tipe_periode" class="form-select" required onchange="toggleFilterPeriode()">
                                <option value="bulanan" selected>Laporan Bulanan</option>
                                <option value="triwulan">Laporan Triwulan</option>
                            </select>
                        </div>

                        <!-- Container Filter Bulanan -->
                        <div class="mb-3" id="container_bulanan">
                            <label class="form-label fw-bold">Pilih Bulan</label>
                            <select name="bulan" id="bulan" class="form-select">
                                @php
                                    $daftarBulan = [ 1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember' ];$currentMonth = date('n');
                                @endphp
                                @foreach($daftarBulan as $key =>$namaBulan)
                                    <option value="{{ $key }}" {{ $currentMonth ==$key ? 'selected' : '' }}>
                                        {{ $namaBulan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Container Filter Triwulan -->
                        <div class="mb-3 d-none" id="container_triwulan">
                            <label class="form-label fw-bold">Pilih Triwulan</label>
                            <select name="triwulan" id="triwulan" class="form-select">
                                <option value="1">Triwulan I (Januari - Maret)</option>
                                <option value="2">Triwulan II (April - Juni)</option>
                                <option value="3">Triwulan III (Juli - September)</option>
                                <option value="4">Triwulan IV (Oktober - Desember)</option>
                            </select>
                        </div>

                        <!-- Select Tahun -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Tahun</label>
                            <select name="tahun" id="tahun" class="form-select" required>
                                @php
                                    $tahunSekarang = (int) date('Y');
                                    $startTahun =$tahunSekarang - 5;
                                    $endTahun =$tahunSekarang + 5;
                                @endphp
                                @for($t = $startTahun; $t <= $endTahun; $t++)
                                    <option value="{{ $t }}" {{ $t ==$tahunSekarang ? 'selected' : '' }}>
                                        {{ $t }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Option Samarkan Data Sensitif -->
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="samarkan_detail" value="1" id="samarkan_detail">
                            <label class="form-check-label text-danger fw-bold" for="samarkan_detail">
                                <i class="fas fa-user-shield me-1"></i> Samarkan Detail Pengunjung (NIK, No.HP, Email, Nama)
                            </label>
                        </div>

                        @if($isMsAceh)
                            <!-- Option Kecualikan MS Aceh -->
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="exclude_ms_aceh" value="1" id="exclude_ms_aceh">
                                <label class="form-check-label text-primary fw-bold" for="exclude_ms_aceh">
                                    <i class="fas fa-filter me-1"></i> Kecualikan MS Aceh (Hanya Satker Daerah)
                                </label>
                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-download me-1"></i> Download PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">    

    <style>
        .dataTables_length select { width: auto !important; display: inline-block !important; padding: 2px 8px !important; margin: 0 5px !important; font-size: 13px; }
        .dataTables_filter input { width: auto !important; display: inline-block !important; margin-left: 5px !important; padding: 2px 8px !important; font-size: 13px; }
        .dataTables_wrapper .dataTables_paginate .page-link { border-radius: 4px !important; padding: 4px 10px !important; font-size: 12px; }
        table.dataTable td { padding: 8px 10px !important; vertical-align: middle !important; font-size: 13px; }
        table.dataTable thead th { padding: 8px 10px !important; vertical-align: middle !important; font-size: 13px; font-weight: 600; }
        .select2-container--bootstrap-5 { z-index: 1060 !important; }
    </style>
@endpush
    
@push('scripts')
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

        function selesaiMelayaniFisik(id) {
            Swal.fire({
                title: 'Selesai Melayani?',
                text: 'Apakah pelayanan fisik untuk pengunjung ini sudah selesai?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545', // Warna merah
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa-solid fa-check me-1"></i>Ya, Selesai!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading saat proses fetch berlangsung
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyimpan status pelayanan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
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
                            // Sembunyikan tombol merah "Selesai Melayani"
                            const btnSelesai = document.getElementById(`btn-selesai-${id}`);
                            if (btnSelesai) {
                                btnSelesai.remove();
                            }

                            // Ubah tombol WA menjadi status "Ditindaklanjuti"
                            const btnWa = document.getElementById(`btn-wa-${id}`);
                            if (btnWa) {
                                btnWa.className = 'btn btn-sm btn-outline-success fw-bold px-2 py-1';
                                btnWa.style.fontSize = '11px';
                                btnWa.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i>Ditindaklanjuti';
                            }

                            // Reload DataTables jika ada agar kolom durasi ter-update
                            if (typeof table !== 'undefined') {
                                table.ajax.reload(null, false);
                            }

                            // Tampilkan notifikasi sukses SweetAlert
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
        window.soundEnabled = {{ $isSoundActive ? 'true' : 'false' }};
        window.audioUnlocked = false;

        function unlockAudioContext() {
            const notifAudio = document.getElementById('notifAudio');
            if (!notifAudio || window.audioUnlocked) return;

            notifAudio.play().then(() => {
                notifAudio.pause();
                notifAudio.currentTime = 0;
                window.audioUnlocked = true;
                console.log('✅ Audio unlocked');
            }).catch(e => {
                console.warn('⚠️ Menunggu interaksi user untuk audio:', e);
            });
        }

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
                        playNotifSound();
                    }
                }
            })
            .catch(err => console.error('Error toggling sound:', err));
        }

        function playNotifSound() {
            const notifAudio = document.getElementById('notifAudio');
            if (window.soundEnabled && notifAudio) {
                notifAudio.currentTime = 0;
                notifAudio.play().catch(error => console.error('Audio play error:', error));
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
                        if (window.lastMsAcehId === null) {
                            window.lastMsAcehId = data.latest_ms_aceh_id;
                        } else if (data.latest_ms_aceh_id && window.lastMsAcehId !== data.latest_ms_aceh_id) {
                            window.lastMsAcehId = data.latest_ms_aceh_id;
                            hasNewData = true;

                            if (window.tableMsAceh) {
                                window.tableMsAceh.ajax.reload(null, false);
                            }
                        }

                        if (window.lastDaerahId === null) {
                            window.lastDaerahId = data.latest_daerah_id;
                        } else if (data.latest_daerah_id && window.lastDaerahId !== data.latest_daerah_id) {
                            window.lastDaerahId = data.latest_daerah_id;
                            hasNewData = true;

                            if (window.tableDaerah) {
                                window.tableDaerah.ajax.reload(null, false);
                            }
                        }

                    } else {
                        if (window.lastLatestId === null) {
                            window.lastLatestId = data.latest_id;
                        } else if (data.latest_id && window.lastLatestId !== data.latest_id) {
                            window.lastLatestId = data.latest_id;
                            hasNewData = true;

                            if (window.tableDaerah) {
                                window.tableDaerah.ajax.reload(null, false);
                            }
                        }
                    }

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectKategori = document.getElementById('kategori_layanan');
            const wrapperJenisPerkara = document.getElementById('wrapper_jenis_perkara');
            const wrapperKeperluan = document.getElementById('wrapper_keperluan');
            const inputJenisPerkara = document.getElementById('jenis_perkara_id');
            const inputKeperluan = document.getElementById('keperluan');

            selectKategori.addEventListener('change', function () {
                const value = this.value;

                if (value === 'syarat_perkara') {
                    // Tampilkan Jenis Perkara, Sembunyikan Textarea
                    wrapperJenisPerkara.style.display = 'block';
                    wrapperKeperluan.style.display = 'none';

                    // Reset & atur required
                    inputJenisPerkara.setAttribute('required', 'required');
                    inputKeperluan.removeAttribute('required');
                    inputKeperluan.value = '';

                } else if (value === 'lainnya') {
                    // Tampilkan Textarea, Sembunyikan Jenis Perkara
                    wrapperKeperluan.style.display = 'block';
                    wrapperJenisPerkara.style.display = 'none';

                    // Reset & atur required
                    inputKeperluan.setAttribute('required', 'required');
                    inputJenisPerkara.removeAttribute('required');
                    inputJenisPerkara.value = '';

                } else {
                    // Sembunyikan keduanya jika belum memilih
                    wrapperJenisPerkara.style.display = 'none';
                    wrapperKeperluan.style.display = 'none';

                    inputJenisPerkara.removeAttribute('required');
                    inputKeperluan.removeAttribute('required');
                    inputJenisPerkara.value = '';
                    inputKeperluan.value = '';
                }
            });
        });
    </script>

    <script>
        function toggleFilterPeriode() {
            const tipe = document.getElementById('tipe_periode').value;
            const containerBulanan = document.getElementById('container_bulanan');
            const containerTriwulan = document.getElementById('container_triwulan');

            if (tipe === 'bulanan') {
                containerBulanan.classList.remove('d-none');
                containerTriwulan.classList.add('d-none');
            } else {
                containerBulanan.classList.add('d-none');
                containerTriwulan.classList.remove('d-none');
            }
        }
    </script>
@endpush