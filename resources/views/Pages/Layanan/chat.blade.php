@extends('Layouts.app')

@section('page_title', 'Helpdesk Lintas Satker')

@section('content')
<div class="container-fluid">
    <!-- Row Header / Breadcrumb -->
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Direktori Helpdesk Lintas Satker (Layanan Umum)</h4>
                <p class="mb-0">Hubungi Admin PTSP Mahkamah Syar'iyah lain secara instan tanpa ganti modul</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Layanan</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Helpdesk Chat</a></li>
            </ol>
        </div>
    </div>

    <!-- Filter & Pencarian Kontak -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card shadow-sm mb-0">
                <div class="card-body p-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="flaticon-381-search-2 text-primary"></i></span>
                        <input type="text" id="searchSatker" class="form-control border-start-0 text-dark" placeholder="Ketik nama daerah untuk mencari (misal: Jantho, Banda Aceh, Lhokseumawe)...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Kontak Admin Satker -->
    <div class="row" id="kontakGrid">
        
        <!-- Kontak 1: MS Banda Aceh -->
        <div class="col-xl-4 col-sm-6 item-satker">
            <div class="card text-dark shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 bg-primary-light text-primary rounded-circle me-3">
                            <i class="flaticon-381-user-7 fs-24"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold nama-satker">MS Banda Aceh</h5>
                            <span class="badge badge-xs light badge-primary">Kelas I-A</span>
                        </div>
                    </div>
                    <div class="info-kontak fs-14">
                        <p class="mb-1"><strong>Admin PTSP:</strong> <span class="text-muted">Ahmad Fauzi, S.H.</span></p>
                        <p class="mb-0"><strong>No. WhatsApp:</strong> <span class="text-success font-weight-bold">0812-6655-1122</span></p>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-xs btn-salin" data-phone="081266551122">
                            <i class="fa fa-copy me-1"></i> Salin
                        </button>
                        <!-- Langsung masuk WA dengan pesan otomatis -->
                        <a href="https://wa.me/6281266551122?text=Assalamualaikum%20Admin%20MS%20Banda%20Aceh,%20saya%20petugas%20SAPA-MS%20ingin%20berkoordinasi%20terkait%20pelayanan..." target="_blank" class="btn btn-success btn-xs text-white">
                            <i class="fa fa-whatsapp me-1"></i> Hubungi Instan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontak 2: MS Jantho -->
        <div class="col-xl-4 col-sm-6 item-satker">
            <div class="card text-dark shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 bg-info-light text-info rounded-circle me-3">
                            <i class="flaticon-381-user-7 fs-24"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold nama-satker">MS Jantho</h5>
                            <span class="badge badge-xs light badge-info">Kelas I-B</span>
                        </div>
                    </div>
                    <div class="info-kontak fs-14">
                        <p class="mb-1"><strong>Admin PTSP:</strong> <span class="text-muted">Siti Humaira, S.Sy.</span></p>
                        <p class="mb-0"><strong>No. WhatsApp:</strong> <span class="text-success font-weight-bold">0852-7711-2233</span></p>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-xs btn-salin" data-phone="085277112233">
                            <i class="fa fa-copy me-1"></i> Salin
                        </button>
                        <a href="https://wa.me/6285277112233?text=Assalamualaikum%20Admin%20MS%20Jantho,%20saya%20petugas%20SAPA-MS%20ingin%20berkoordinasi..." target="_blank" class="btn btn-success btn-xs text-white">
                            <i class="fa fa-whatsapp me-1"></i> Hubungi Instan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontak 3: MS Lhokseumawe -->
        <div class="col-xl-4 col-sm-6 item-satker">
            <div class="card text-dark shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 bg-warning-light text-warning rounded-circle me-3">
                            <i class="flaticon-381-user-7 fs-24"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold nama-satker">MS Lhokseumawe</h5>
                            <span class="badge badge-xs light badge-warning">Kelas I-B</span>
                        </div>
                    </div>
                    <div class="info-kontak fs-14">
                        <p class="mb-1"><strong>Admin PTSP:</strong> <span class="text-muted">Rahmad Dhani, A.Md.</span></p>
                        <p class="mb-0"><strong>No. WhatsApp:</strong> <span class="text-success font-weight-bold">0823-8899-0011</span></p>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-xs btn-salin" data-phone="082388990011">
                            <i class="fa fa-copy me-1"></i> Salin
                        </button>
                        <a href="https://wa.me/6282388990011?text=Assalamualaikum%20Admin%20MS%20Lhokseumawe,%20saya%20petugas%20SAPA-MS%20ingin%20berkoordinasi..." target="_blank" class="btn btn-success btn-xs text-white">
                            <i class="fa fa-whatsapp me-1"></i> Hubungi Instan
                        </a>
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
        // Fitur Live Search
        $('#searchSatker').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#kontakGrid .item-satker").filter(function() {
                $(this).toggle($(this).find('.nama-satker').text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Fitur Salin Cepat
        $('.btn-salin').on('click', function() {
            var number = $(this).data('phone');
            navigator.clipboard.writeText(number);
            
            var btn = $(this);
            btn.html('<i class="fa fa-check me-1"></i> Ok!').removeClass('btn-outline-secondary').addClass('btn-success text-white');
            
            setTimeout(function() {
                btn.html('<i class="fa fa-copy me-1"></i> Salin').removeClass('btn-success text-white').addClass('btn-outline-secondary');
            }, 1500);
        });
    });
</script>
@endpush