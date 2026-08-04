@extends('Layouts.app')

@section('page_title', 'Persyaratan Perkara')

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Persyaratan Berperkara Lintas Satker</h4>
                <p class="mb-0">Pilih Mahkamah Syar'iyah tujuan untuk melihat detail persyaratan resmi</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">SAPA-MS</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Persyaratan</a></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card font-weight-bold p-3 bg-light mb-4">
                <span class="text-primary"><i class="flaticon-381-location-4 me-2"></i> KELOMPOK DAERAH WILAYAH HUKUM:</span>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6">
            <div class="card text-center cursor-pointer shadow-sm border-0 btn-satker" data-target="#syarat-ms-bna" style="transition: all 0.3s ease;">
                <div class="card-body p-4">
                    <div class="icon-satker mb-3 text-primary">
                        <i class="flaticon-381-home-2 fs-30"></i>
                    </div>
                    <h5 class="text-black mb-1">MS Banda Aceh</h5>
                    <p class="fs-13 text-muted mb-0">Kelas I-A • Pusat Provinsi</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card text-center cursor-pointer shadow-sm border-0 btn-satker" data-target="#syarat-ms-jth" style="transition: all 0.3s ease;">
                <div class="card-body p-4">
                    <div class="icon-satker mb-3 text-info">
                        <i class="flaticon-381-home-2 fs-30"></i>
                    </div>
                    <h5 class="text-black mb-1">MS Jantho</h5>
                    <p class="fs-13 text-muted mb-0">Kelas I-B • Kab. Aceh Besar</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card text-center cursor-pointer shadow-sm border-0 btn-satker" data-target="#syarat-ms-lsm" style="transition: all 0.3s ease;">
                <div class="card-body p-4">
                    <div class="icon-satker mb-3 text-warning">
                        <i class="flaticon-381-home-2 fs-30"></i>
                    </div>
                    <h5 class="text-black mb-1">MS Lhokseumawe</h5>
                    <p class="fs-13 text-muted mb-0">Kelas I-B • Kota Lhokseumawe</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card text-center cursor-pointer shadow-sm border-0 btn-satker" data-target="#syarat-ms-mbo" style="transition: all 0.3s ease;">
                <div class="card-body p-4">
                    <div class="icon-satker mb-3 text-danger">
                        <i class="flaticon-381-home-2 fs-30"></i>
                    </div>
                    <h5 class="text-black mb-1">MS Meulaboh</h5>
                    <p class="fs-13 text-muted mb-0">Kelas I-B • Kab. Aceh Barat</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-12">
            
            <div id="empty-state" class="card p-5 text-center shadow-sm">
                <div class="card-body">
                    <i class="flaticon-381-click fs-40 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Silakan klik salah satu Card Satker di atas untuk memunculkan syarat perkara.</h5>
                </div>
            </div>

            <div id="syarat-ms-bna" class="container-syarat d-none">
                <div class="card shadow-sm border-left border-primary border-4">
                    <div class="card-header bg-light">
                        <h4 class="card-title text-primary"><i class="flaticon-381-file-1 me-2"></i> Syarat Berperkara di MS Banda Aceh</h4>
                    </div>
                    <div class="card-body">
                        <div class="accordion accordion-primary" id="accordion-bna">
                            <div class="accordion-item mb-2 border">
                                <h2 class="accordion-header" id="heading-bna-1">
                                    <button class="accordion-button collapsed font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-bna-1">
                                        1. Cerai Gugat (Diajukan oleh Istri)
                                    </button>
                                </h2>
                                <div id="collapse-bna-1" class="accordion-collapse collapse" data-bs-parent="#accordion-bna">
                                    <div class="accordion-body text-dark">
                                        <ul>
                                            <li><i class="fa fa-check text-success me-2"></i> Surat Gugatan (Minimal 6 rangkap + softcopy).</li>
                                            <li><i class="fa fa-check text-success me-2"></i> Buku Nikah Asli / Duplikat Akta Nikah.</li>
                                            <li><i class="fa fa-check text-success me-2"></i> Fotokopi KTP Penggugat terlegalisir (KPT/Pos).</li>
                                            <li><i class="fa fa-check text-success me-2"></i> Surat Keterangan Ghaib dari Desa (Jika suami pergi tanpa kabar).</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item mb-2 border">
                                <h2 class="accordion-header" id="heading-bna-2">
                                    <button class="accordion-button collapsed font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-bna-2">
                                        2. Dispensasi Nikah (Anak di Bawah Umur)
                                    </button>
                                </h2>
                                <div id="collapse-bna-2" class="accordion-collapse collapse" data-bs-parent="#accordion-bna">
                                    <div class="accordion-body text-dark">
                                        <ul>
                                            <li><i class="fa fa-check text-success me-2"></i> Surat Permohonan dari Orang Tua anak.</li>
                                            <li><i class="fa fa-check text-success me-2"></i> Fotokopi KTP Orang Tua & Kartu Keluarga (KK).</li>
                                            <li><i class="fa fa-check text-success me-2"></i> Surat Penolakan dari KUA setempat (Model N8).</li>
                                            <li><i class="fa fa-check text-success me-2"></i> Fotokopi Akta Kelahiran Anak & KTP/Ijazah Calon Pasangan.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>

            <div id="syarat-ms-jth" class="container-syarat d-none">
                <div class="card shadow-sm border-left border-info border-4">
                    <div class="card-header bg-light">
                        <h4 class="card-title text-info"><i class="flaticon-381-file-1 me-2"></i> Syarat Berperkara di MS Jantho</h4>
                    </div>
                    <div class="card-body">
                        <div class="accordion accordion-info" id="accordion-jth">
                            <div class="accordion-item mb-2 border">
                                <h2 class="accordion-header" id="heading-jth-1">
                                    <button class="accordion-button collapsed font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-jth-1">
                                        1. Cerai Talak (Diajukan oleh Suami)
                                    </button>
                                </h2>
                                <div id="collapse-jth-1" class="accordion-collapse collapse" data-bs-parent="#accordion-jth">
                                    <div class="accordion-body text-dark">
                                        <ul>
                                            <li><i class="fa fa-check text-info me-2"></i> Surat Permohonan Cerai Talak.</li>
                                            <li><i class="fa fa-check text-info me-2"></i> Buku Nikah Asli / Duplikat Akta Nikah.</li>
                                            <li><i class="fa fa-check text-info me-2"></i> Fotokopi KTP Pemohon (Suami).</li>
                                            <li><i class="fa fa-check text-info me-2"></i> Surat Izin Atasan (Jika berstatus PNS/TNI/Polri).</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-satker').on('click', function() {
            $('.btn-satker').removeClass('border-primary border-info border-warning border-danger shadow-lg').css('transform', 'scale(1)');
            $(this).addClass('shadow-lg').css('transform', 'scale(1.03)');
            $('#empty-state').addClass('d-none');
            $('.container-syarat').addClass('d-none');
            
            var target = $(this).data('target');
            $(target).removeClass('d-none').hide().fadeIn(500);
        });

        $('.btn-satker').hover(
            function() { $(this).css('box-shadow', '0 10px 20px rgba(0,0,0,0.1)'); },
            function() { if(!$(this).hasClass('shadow-lg')) { $(this).css('box-shadow', 'none'); } }
        );
    });
</script>
@endpush