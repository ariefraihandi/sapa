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
                        <div class="col-md-6">
                            <label class="form-label required">Nama Responden / Pemohon</label>
                            <input type="text" name="nama_responden" class="form-control" placeholder="Nama lengkap..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIK (KTP)</label>
                            <input type="text" name="nik" maxlength="16" class="form-control" placeholder="16 digit NIK...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" placeholder="contoh@email.com">
                            <small class="form-text text-muted">*Jika tidak ada email, isi dengan tanda <strong>-</strong></small>
                        </div>        
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
                        <div class="col-md-6">
                            <label class="form-label required">Pekerjaan</label>
                            <select name="pekerjaan" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Pekerjaan --</option>
                                @foreach($pekerjaans as $p)
                                    <option value="{{ $p->nama_pekerjaan }}">{{ $p->nama_pekerjaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="" selected disabled>-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Usia</label>
                            <input type="text" name="usia" class="form-control" placeholder="Contoh: 30 Tahun" required>
                        </div>
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
                        <div class="col-md-12 mb-3">
                            <label class="form-label required">Layanan / Keperluan</label>
                            <select id="kategori_layanan" class="form-control" required>
                                <option value="">-- Pilih Jenis Layanan --</option>
                                <option value="syarat_perkara">Syarat Perkara</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3" id="wrapper_jenis_perkara" style="display: none;">
                            <label for="jenis_perkara_id" class="form-label">Jenis Perkara</label>
                            <select name="jenis_perkara_id" id="jenis_perkara_id" class="form-control">
                                <option value="">-- Pilih Jenis Perkara --</option>
                                @foreach($jenisPerkaras as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_layanan }}</option>
                                @endforeach
                            </select>
                        </div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectKategori = document.getElementById('kategori_layanan');
        const wrapperJenisPerkara = document.getElementById('wrapper_jenis_perkara');
        const wrapperKeperluan = document.getElementById('wrapper_keperluan');
        const inputJenisPerkara = document.getElementById('jenis_perkara_id');
        const inputKeperluan = document.getElementById('keperluan');

        if (selectKategori) {
            selectKategori.addEventListener('change', function () {
                const value = this.value;

                if (value === 'syarat_perkara') {
                    wrapperJenisPerkara.style.display = 'block';
                    wrapperKeperluan.style.display = 'none';

                    inputJenisPerkara.setAttribute('required', 'required');
                    inputKeperluan.removeAttribute('required');
                    inputKeperluan.value = '';

                } else if (value === 'lainnya') {
                    wrapperKeperluan.style.display = 'block';
                    wrapperJenisPerkara.style.display = 'none';

                    inputKeperluan.setAttribute('required', 'required');
                    inputJenisPerkara.removeAttribute('required');
                    inputJenisPerkara.value = '';

                } else {
                    wrapperJenisPerkara.style.display = 'none';
                    wrapperKeperluan.style.display = 'none';

                    inputJenisPerkara.removeAttribute('required');
                    inputKeperluan.removeAttribute('required');
                    inputJenisPerkara.value = '';
                    inputKeperluan.value = '';
                }
            });
        }
    });
</script>
@endpush