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
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Satker</label>
                            <select name="satker_id" id="satker_id" class="form-select" required>
                                <option value="all" selected>-- Seluruh Satker (MS Aceh & Satker Daerah) --</option>
                                @foreach($satkers as $stk)
                                    <option value="{{ $stk->id }}">{{ $stk->satker_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Tipe Laporan</label>
                        <select name="tipe_periode" id="tipe_periode" class="form-select" required onchange="toggleFilterPeriode()">
                            <option value="bulanan" selected>Laporan Bulanan</option>
                            <option value="triwulan">Laporan Triwulan</option>
                        </select>
                    </div>

                    <div class="mb-3" id="container_bulanan">
                        <label class="form-label fw-bold">Pilih Bulan</label>
                        <select name="bulan" id="bulan" class="form-select">
                            @php
                                $daftarBulan = [ 1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember' ];
                                $currentMonth = date('n');
                            @endphp
                            @foreach($daftarBulan as $key => $namaBulan)
                                <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>
                                    {{ $namaBulan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="container_triwulan">
                        <label class="form-label fw-bold">Pilih Triwulan</label>
                        <select name="triwulan" id="triwulan" class="form-select">
                            <option value="1">Triwulan I (Januari - Maret)</option>
                            <option value="2">Triwulan II (April - Juni)</option>
                            <option value="3">Triwulan III (Juli - September)</option>
                            <option value="4">Triwulan IV (Oktober - Desember)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Tahun</label>
                        <select name="tahun" id="tahun" class="form-select" required>
                            @php
                                $tahunSekarang = (int) date('Y');
                                $startTahun = $tahunSekarang - 5;
                                $endTahun = $tahunSekarang + 5;
                            @endphp
                            @for($t = $startTahun; $t <= $endTahun; $t++)
                                <option value="{{ $t }}" {{ $t == $tahunSekarang ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="samarkan_detail" value="1" id="samarkan_detail">
                        <label class="form-check-label text-danger fw-bold" for="samarkan_detail">
                            <i class="fas fa-user-shield me-1"></i> Samarkan Detail Pengunjung (NIK, No.HP, Email, Nama)
                        </label>
                    </div>

                    @if($isMsAceh)
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

@push('scripts')
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