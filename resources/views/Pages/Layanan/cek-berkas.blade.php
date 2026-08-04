@extends('Layouts.app')

@section('page_title', 'Cek Berkas Mandiri')

@section('content')
<div class="container-fluid">
    <!-- Row Header / Breadcrumb -->
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Verifikasi Berkas Mandiri (Pra-Perkara)</h4>
                <p class="mb-0">Periksa dan validasi dokumen pendaftaran yang diunggah oleh masyarakat</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">SAPA-MS</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Cek Berkas</a></li>
            </ol>
        </div>
    </div>

    <!-- Tabel Daftar Berkas Masuk -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <h4 class="card-title text-white"><i class="flaticon-381-file-1 me-2"></i> Antrean Berkas Masuk</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md text-dark">
                            <thead>
                                <tr class="font-weight-bold">
                                    <th><strong>No. Tiket</strong></th>
                                    <th><strong>Nama Pemohon</strong></th>
                                    <th><strong>Jenis Perkara</strong></th>
                                    <th><strong>Tgl Upload</strong></th>
                                    <th><strong>Status</strong></th>
                                    <th><strong>Aksi</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Contoh Data 1 -->
                                <tr>
                                    <td><strong>#SAPA-8921</strong></td>
                                    <td>Cut Nyak Aminah</td>
                                    <td>Cerai Gugat</td>
                                    <td>07 Juli 2026</td>
                                    <td><span class="badge light badge-warning">Pending Review</span></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-xs shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#modalDetailBerkas">
                                            <i class="fa fa-eye me-1"></i> Periksa Berkas
                                        </button>
                                    </td>
                                </tr>
                                <!-- Contoh Data 2 -->
                                <tr>
                                    <td><strong>#SAPA-8920</strong></td>
                                    <td>Teuku Muhammad</td>
                                    <td>Dispensasi Nikah</td>
                                    <td>06 Juli 2026</td>
                                    <td><span class="badge light badge-success">Berkas Lengkap</span></td>
                                    <td>
                                        <button type="button" class="btn btn-light btn-xs text-dark" disabled>
                                            <i class="fa fa-check me-1"></i> Terverifikasi
                                        </button>
                                    </td>
                                </tr>
                                <!-- Contoh Data 3 -->
                                <tr>
                                    <td><strong>#SAPA-8919</strong></td>
                                    <td>Siti Rahmah</td>
                                    <td>Cerai Talak</td>
                                    <td>05 Juli 2026</td>
                                    <td><span class="badge light badge-danger">Ditolak / Perbaikan</span></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-xs shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#modalDetailBerkas">
                                            <i class="fa fa-eye me-1"></i> Periksa Berkas
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DETAIL & VERIFIKASI DOKUMEN -->
<!-- ========================================== -->
<div class="modal fade" id="modalDetailBerkas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg text-dark">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold text-primary"><i class="flaticon-381-folder-1 me-2"></i> Detail Dokumen Pemohon (#SAPA-8921)</h5>
                <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#modalDetailBerkas" aria-label="Close"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Biodata Ringkas -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Nama Lengkap</p>
                            <h6 class="font-weight-bold">Cut Nyak Aminah</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Kontak WhatsApp</p>
                            <h6 class="font-weight-bold text-success"><i class="fa fa-whatsapp"></i> 0812-3456-7890</h6>
                        </div>
                    </div>
                    
                    <hr>

                    <!-- Daftar File Ungguhan -->
                    <h5 class="font-weight-bold mb-3"><i class="flaticon-381-file me-2"></i> File yang Diunggah</h5>
                    <div class="list-group mb-4">
                        <!-- File 1 -->
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa fa-file-pdf-o text-danger me-2 fs-16"></i>
                                <span>1. Scan KTP Pemohon (Terlegalisir Pos)</span>
                            </div>
                            <a href="#" class="btn btn-outline-primary btn-xs"><i class="fa fa-download"></i> Lihat File</a>
                        </div>
                        <!-- File 2 -->
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa fa-file-image-o text-primary me-2 fs-16"></i>
                                <span>2. Buku Nikah Asli (Halaman Depan & Isi)</span>
                            </div>
                            <a href="#" class="btn btn-outline-primary btn-xs"><i class="fa fa-download"></i> Lihat File</a>
                        </div>
                        <!-- File 3 -->
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa fa-file-word-o text-info me-2 fs-16"></i>
                                <span>3. Draf Awal Surat Gugatan</span>
                            </div>
                            <a href="#" class="btn btn-outline-primary btn-xs"><i class="fa fa-download"></i> Lihat File</a>
                        </div>
                    </div>

                    <!-- Form Keputusan Petugas -->
                    <div class="bg-light p-3 rounded">
                        <h5 class="font-weight-bold mb-3"><i class="flaticon-381-edit-1 me-2"></i> Tindakan Verifikasi Petugas</h5>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Status Verifikasi Berkas</label>
                            <select class="form-control text-dark" id="status_verifikasi">
                                <option value="lengkap">Berkas Lengkap (Kirim Notifikasi Siap ke Kantor)</option>
                                <option value="tolak">Berkas Belum Lengkap / Perlu Perbaikan</option>
                            </select>
                        </div>
                        <div class="form-group mb-0 d-none" id="wrapper_catatan">
                            <label class="form-label font-weight-bold">Catatan Perbaikan Dokumen</label>
                            <textarea class="form-control text-dark" rows="3" placeholder="Contoh: Foto Buku Nikah buram, mohon upload ulang halaman ke-2 dengan jelas."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-danger light shadow-none" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i> Simpan Hasil Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Logika muncul/sembunyi catatan penolakan secara interaktif
        $('#status_verifikasi').on('change', function() {
            if ($(this).val() === 'tolak') {
                $('#wrapper_catatan').removeClass('d-none').hide().fadeIn(300);
            } else {
                $('#wrapper_catatan').fadeOut(300, function() {
                    $(this).addClass('d-none');
                });
            }
        });
    });
</script>
@endpush