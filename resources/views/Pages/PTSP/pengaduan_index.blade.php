@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- HEADER HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color: #064e3b;">Rekap & Monitoring Pengaduan PTSP</h3>
            <p class="text-muted small mb-0">
                @php
                    $user = Auth::user();
                    $satkerName = $user->satker->satker_name ?? '';
                    $isMsAceh = ($user->role === 'admin') || str_contains(strtolower($satkerName), 'mahkamah syar\'iyah aceh') || str_contains(strtolower($satkerName), 'ms aceh');
                @endphp

                @if($isMsAceh)
                    Monitoring seluruh laporan pengaduan masyarakat yang masuk dari seluruh Satker se-Aceh.
                @else
                    Monitoring laporan pengaduan masyarakat yang masuk untuk {{ $satkerName ?: 'Satker Anda' }}.
                @endif
            </p>
        </div>
    </div>

    <!-- TABEL DATA PENGADUAN -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Identitas Pelapor</th>
                            @if($isMsAceh)
                                <th>Satker Tujuan</th>
                            @endif
                            <th>Uraian Pengaduan</th>
                            <th>Detail Tindak Lanjut</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengaduan as $index => $item)
                            @php
                                $no_hp_clean = preg_replace('/[^0-9]/', '', $item->no_hp);
                                if (str_starts_with($no_hp_clean, '0')) {
                                    $no_hp_clean = '62' . substr($no_hp_clean, 1);
                                }
                                $link_wa = "https://wa.me/" . $no_hp_clean;
                            @endphp
                            <tr>
                                <td>{{ $pengaduan->firstItem() + $index }}</td>
                                <td>
                                    <strong class="d-block text-dark">{{ $item->nama_pelapor }}</strong>
                                    <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                        <small class="text-muted"><i class="fa-solid fa-clock me-1"></i>{{ $item->created_at->format('d M Y - H:i') }} WIB</small>
                                        @if($item->nik)
                                            <span class="badge bg-light text-secondary border fw-normal">NIK: {{ $item->nik }}</span>
                                        @endif
                                    </div>
                                </td>

                                @if($isMsAceh)
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fa-solid fa-building-columns me-1 text-success"></i>
                                            {{ $item->satker->satker_short_name ?? $item->satker->satker_name ?? 'MS Aceh' }}
                                        </span>
                                    </td>
                                @endif

                                <td>
                                    <div class="p-2 rounded bg-light border text-secondary small text-wrap" style="max-width: 350px;">
                                        <i class="fa-solid fa-quote-left me-1 text-muted opacity-50"></i>
                                        {{ $item->uaraian_pengaduan }}
                                    </div>
                                </td>

                                <td>
                                    <div id="wrapper-detail-{{ $item->id }}">
                                        @if($item->is_tindak_lanjut)
                                            <div class="small">
                                                <span class="badge bg-success mb-1"><i class="fa-solid fa-check-double me-1"></i>Selesai</span>
                                                <p class="mb-1 text-dark fw-semibold text-wrap" style="max-width: 250px;">{{ $item->catatan_tindak_lanjut }}</p>
                                                @if($item->file_tindak_lanjut)
                                                    <a href="{{ asset('storage/' . $item->file_tindak_lanjut) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 style="font-size: 0.75rem;">
                                                        <i class="fa-solid fa-paperclip me-1"></i>Lihat Berkas
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Belum Ada Catatan</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        <!-- Tombol Kontak Pelapor -->
                                        <a href="{{ $link_wa }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-success w-100 py-1" 
                                           style="border-radius: 50px; font-weight: 600; font-size: 0.78rem;">
                                            <i class="fa-brands fa-whatsapp me-1"></i> {{ $item->no_hp }}
                                        </a>

                                        <!-- Tombol Pemicu Modal Tindak Lanjut -->
                                        <button type="button"
                                                onclick="openModalTindakLanjut('{{ $item->id }}', '{{ addslashes($item->nama_pelapor) }}', '{{ addslashes($item->catatan_tindak_lanjut) }}', '{{ $item->file_tindak_lanjut ? asset('storage/' . $item->file_tindak_lanjut) : '' }}')"
                                                id="btn-tindak-{{ $item->id }}"
                                                class="btn btn-sm {{ $item->is_tindak_lanjut ? 'btn-success' : 'btn-warning text-dark' }} w-100 py-1"
                                                style="border-radius: 50px; font-weight: 600; font-size: 0.78rem;">
                                            <i class="fa-solid {{ $item->is_tindak_lanjut ? 'fa-pen-to-square' : 'fa-list-check' }} me-1"></i>
                                            <span id="label-btn-{{ $item->id }}">{{ $item->is_tindak_lanjut ? 'Edit Tindak Lanjut' : 'Tindak Lanjuti' }}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isMsAceh ? '6' : '5' }}" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                    <h5>Belum ada laporan pengaduan</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $pengaduan->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function openModalTindakLanjut(id, namaPelapor, existingCatatan, existingFileUrl) {
    let filePreview = '';
    if (existingFileUrl) {
        filePreview = `<div class="mt-2 text-start small"><i class="fa-solid fa-file me-1 text-primary"></i> <a href="${existingFileUrl}" target="_blank" class="text-decoration-underline">Lihat Berkas Terupload saat ini</a></div>`;
    }

    Swal.fire({
        title: `<span style="color:#047857; font-size: 1.15rem;"><i class="fa-solid fa-list-check me-1"></i> Form Tindak Lanjut Pengaduan</span>`,
        html: `
            <div style="text-align: left; font-size: 0.85rem; color: #475569;">
                <p style="margin-bottom: 12px;">Tindak lanjut laporan pengaduan dari <b>${namaPelapor}</b>:</p>
                
                <div style="margin-bottom: 10px;">
                    <label style="font-weight: 600; display: block; margin-bottom: 3px;">Deskripsi / Catatan Tindak Lanjut <span style="color:red">*</span></label>
                    <textarea id="sw_catatan" class="swal2-textarea" placeholder="Contoh: Sudah diselesaikan dengan menghubungi langsung pihak pelapor..." style="width: 100%; margin: 0; height: 85px; font-size: 0.85rem;">${existingCatatan}</textarea>
                </div>

                <div style="margin-bottom: 8px;">
                    <label style="font-weight: 600; display: block; margin-bottom: 3px;">Upload Berkas Pendukung (Opsional)</label>
                    <input type="file" id="sw_file" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted d-block mt-1">Format: PDF, JPG, PNG (Maks. 5MB)</small>
                    ${filePreview}
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-floppy-disk me-1"></i> Simpan Hasil',
        cancelButtonText: 'Batal',
        focusConfirm: false,
        preConfirm: () => {
            const catatan = document.getElementById('sw_catatan').value.trim();
            const fileInput = document.getElementById('sw_file').files[0];

            if (!catatan) {
                Swal.showValidationMessage('Deskripsi / Catatan tindak lanjut wajib diisi!');
                return false;
            }

            // Gunakan FormData untuk mengirim Text + File via AJAX
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('catatan_tindak_lanjut', catatan);
            if (fileInput) {
                formData.append('file_tindak_lanjut', fileInput);
            }

            return formData;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = result.value;

            Swal.showLoading();

            fetch(`/ptsp/pengaduan/${id}/tindak-lanjut`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Berhasil!', 'Data tindak lanjut berhasil disimpan.', 'success').then(() => {
                        window.location.reload(); // Reload halaman untuk menyegarkan tampilan
                    });
                } else {
                    Swal.fire('Gagal!', 'Gagal menyimpan data.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error!', 'Terjadi kesalahan sistem/jaringan.', 'error');
            });
        }
    });
}
</script>
@endsection