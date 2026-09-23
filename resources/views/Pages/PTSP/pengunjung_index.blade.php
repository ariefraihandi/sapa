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
                    $satkerName = $user->satker->satker_name ?? '';
                    $isMsAceh = ($user->role === 'admin') || str_contains(strtolower($satkerName), 'mahkamah syar\'iyah aceh') || str_contains(strtolower($satkerName), 'ms aceh');
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
                    <tbody>
                        @forelse($pengunjung as $index => $item)
                            @php
                                $no_hp_clean = preg_replace('/[^0-9]/', '', $item->no_hp);
                                if (str_starts_with($no_hp_clean, '0')) {
                                    $no_hp_clean = '62' . substr($no_hp_clean, 1);
                                }
                                $link_wa = "https://wa.me/" . $no_hp_clean;
                            @endphp
                            <tr>
                                <td>{{ $pengunjung->firstItem() + $index }}</td>
                                <td>
                                    <strong class="d-block text-dark fs-15">{{ $item->nama_responden }}</strong>
                                    <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                        <span class="badge {{ $item->jenis_kelamin == 'L' ? 'bg-primary' : 'bg-danger' }}" style="font-size: 0.7rem;">
                                            {{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                        @if($item->pekerjaan)
                                            <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                                <i class="fa-solid fa-briefcase me-1 text-secondary"></i>{{ $item->pekerjaan }}
                                            </span>
                                        @endif
                                        @if($item->nik)
                                            <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                                                NIK: {{ $item->nik }}
                                            </span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-clock me-1"></i>{{ $item->created_at->format('d M Y - H:i') }} WIB
                                    </small>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-solid fa-building-columns me-1 text-success"></i>
                                        {{ $item->satker->satker_short_name ?? $item->satker->satker_name ?? 'MS Aceh' }}
                                    </span>
                                </td>

                                <td>
                                    @if($item->jenis_layanan === 'pesan')
                                        <span class="badge bg-success text-white fw-semibold px-2 py-1">
                                            <i class="fa-brands fa-whatsapp me-1"></i> Pesan
                                        </span>
                                    @else
                                        <span class="badge bg-primary text-white fw-semibold px-2 py-1">
                                            <i class="fa-solid fa-phone me-1"></i> Telepon
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="p-2 rounded bg-light border text-secondary small text-wrap" style="max-width: 280px;">
                                        {{ Str::limit($item->keperluan ?: 'Tidak ada catatan keperluan', 70) }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    <a href="{{ $link_wa }}" 
                                       target="_blank" 
                                       onclick="markAsFollowedUp('{{ $item->id }}')"
                                       id="btn-wa-{{ $item->id }}"
                                       class="btn btn-sm {{ $item->is_tindak_lanjut ? 'btn-success' : 'btn-danger' }} px-3 py-1" 
                                       style="border-radius: 50px; font-weight: 600; font-size: 0.8rem; white-space: nowrap;">
                                        <i class="fa-brands fa-whatsapp me-1"></i> {{ $item->no_hp }}
                                    </a>
                                </td>

                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-light btn-sm dropdown-toggle border" data-bs-toggle="dropdown">
                                            Aksi
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <a class="dropdown-item text-primary" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDetailPengunjung{{ $loop->index }}">
                                                <i class="fa-solid fa-eye me-2"></i> Detail
                                            </a>
                                            <a class="dropdown-item text-warning" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalEditPengunjung{{ $loop->index }}">
                                                <i class="fa-solid fa-pen-to-square me-2"></i> Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDeletePengunjung{{ $loop->index }}">
                                                <i class="fa-solid fa-trash me-2"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                    Belum ada data pengunjung PTSP yang terekam.
                                </td>
                            </tr>
                        @endforelse
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
@foreach($pengunjung as $index => $item)
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
                    <label class="fw-bold mb-1">Keperluan Consultation:</label>
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
</script>
@endsection