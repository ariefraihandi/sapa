@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Row Header / Breadcrumb -->
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Layanan Dokumen & Insidentil</h4>
                <p class="mb-0">Pusat validasi, legalisir, dan pemulihan dokumen hukum lintas satker</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">SAPA-MS</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Dokumen</a></li>
            </ol>
        </div>
    </div>

    <!-- MAIN INTERFACE: Kiri (Validasi/Bencana) & Kanan (Leges) -->
    <div class="row">
        <!-- KOLOM KIRI: VALIDASI & JALUR KHUSUS BENCANA (MENGGUNAKAN NAV TABS) -->        
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow-sm text-dark">
                <div class="card-header bg-primary">
                    <h4 class="card-title text-white"><i class="flaticon-381-file-1 me-2"></i> Pemulihan & Validasi Dokumen</h4>
                </div>
                <div class="card-body">
                    <!-- Pembungkus Khusus Tab Template Gymove -->
                    <div class="custom-tab-1">
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold active" data-bs-toggle="tab" href="#reguler">
                                    <i class="flaticon-381-search-1 me-2 text-primary"></i> Validasi Reguler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" data-bs-toggle="tab" href="#bencana">
                                    <i class="flaticon-381-warning-1 me-2 text-danger"></i> Jalur Khusus Pasca-Bencana
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <!-- TAB 1: VALIDASI REGULER -->
                            <div class="tab-pane fade show active" id="reguler" role="tabpanel">
                                <p>Gunakan fitur ini untuk memvalidasi nomor Akta Cerai atau mengoordinasikan permintaan salinan putusan standar.</p>
                                <form action="#" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold">Nomor Akta Cerai / Nomor Perkara</label>
                                        <input type="text" class="form-control" placeholder="Contoh: 0123/AC/2026/MS.Bna">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold">Satker Asal Penerbit</label>
                                        <select class="form-control text-dark" id="satker_asal">
                                            <option value="">-- Pilih Mahkamah Syar'iyah Asal --</option>
                                            <option value="MS.Bna">MS Banda Aceh</option>
                                            <option value="MS.Lsm">MS Lhokseumawe</option>
                                            <option value="MS.Jth">MS Jantho</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="flaticon-381-search-1 me-2"></i> Cek & Validasi Data</button>
                                </form>
                            </div>

                            <!-- TAB 2: JALUR KHUSUS PASCA-BENCANA -->
                            <div class="tab-pane fade" id="bencana" role="tabpanel">
                                <div class="alert alert-danger light border-0 mb-3 fs-14">
                                    <strong>💡 Layanan Afirmatif:</strong> Membantu masyarakat memperoleh kembali salinan Akta Cerai/Putusan yang rusak atau hanyut akibat banjir/bencana alam secara lintas satker.
                                </div>
                                <form action="#" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold">Nama Pemegang Dokumen (Warga)</label>
                                        <input type="text" class="form-control" placeholder="Nama lengkap sesuai KTP/Buku Nikah">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label font-weight-bold">Jenis Dokumen Yang Hilang</label>
                                            <select class="form-control text-dark">
                                                <option value="AC">Akta Cerai Hanyut/Rusak</option>
                                                <option value="PT">Salinan Putusan Hanyut/Rusak</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label font-weight-bold">Satker Penerbit Asal</label>
                                            <select class="form-control text-dark">
                                                <option value="MS.Bna">MS Banda Aceh</option>
                                                <option value="MS.Jth">MS Jantho</option>
                                                <option value="MS.Lsm">MS Lhokseumawe</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold">Estimasi Tahun Perkara / Penerbitan</label>
                                        <input type="number" class="form-control" placeholder="Contoh: 2024" min="2000" max="2026">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold">Unggah Surat Keterangan Hilang Bencana (Desa/Polsek)</label>
                                        <input type="file" class="form-control">
                                        <small class="text-danger">* Wajib melampirkan bukti surat keterangan dari Kepala Desa atau Polsek setempat.</small>
                                    </div>
                                    <button type="submit" class="btn btn-danger"><i class="flaticon-381-send-1 me-2"></i> Ajukan Pemulihan Dokumen</button>
                                </form>
                            </div>
                        </div>
                    </div> <!-- End custom-tab-1 -->
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: PENCATATAN LEGES (KODE AWAL ANDA) -->
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary">
                    <h4 class="card-title text-white">Pencatatan Leges (Legalisir)</h4>
                </div>
                <div class="card-body">
                    <p class="text-dark">Pencatatan administrasi untuk legalisir dokumen resmi peradilan yang diajukan oleh masyarakat secara lintas wilayah.</p>
                    <form action="#" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label text-dark font-weight-bold">Nama Pemohon</label>
                            <input type="text" class="form-control" placeholder="Nama lengkap pemohon legalisir">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label text-dark font-weight-bold">Jenis Dokumen yang di-Leges</label>
                            <select class="form-control text-dark">
                                <option value="akta_cerai">Salinan Akta Cerai</option>
                                <option value="putusan">Salinan Putusan / Penetapan</option>
                                <option value="lainnya">Dokumen Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label text-dark font-weight-bold">Unggah Bukti Fisik (Opsional)</label>
                            <input type="file" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-secondary"><i class="flaticon-381-save me-2"></i> Simpan Data Leges</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection