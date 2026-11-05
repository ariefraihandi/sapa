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