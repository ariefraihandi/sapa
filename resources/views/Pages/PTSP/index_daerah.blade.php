@extends('Layouts.app')

@section('content')
<div class="container-fluid px-4 py-3" style="max-width: 900px; margin: 0 auto;">
    
    <!-- HEADER PROFIL SATKER -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #047857 0%, #10b981 100%); color: white;">
        <div class="card-body p-4 d-flex align-items-center gap-4 flex-wrap">
            <div style="background: white; padding: 12px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <img src="{{ asset($satker->logo ? 'storage/'.$satker->logo : 'images/logo.png') }}" 
                     alt="Logo Satker" style="width: 70px; height: 70px; object-fit: contain;">
            </div>
            <div>
                <span class="badge bg-white text-dark mb-1" style="font-weight: 700; font-size: 0.75rem;">PROFIL LAYANAN PTSP</span>
                <h3 class="fw-bold mb-1" style="color: #ffffff;">{{ $satker->satker_name }}</h3>
                <p class="mb-0 opacity-75" style="font-size: 0.9rem;"><i class="fa-solid fa-location-dot me-1"></i>{{ $satker->satker_city ?? 'Wilayah Hukum MS Aceh' }}</p>
            </div>
        </div>
    </div>

    <!-- FORM PENGATURAN PTSP DAERAH -->
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-header bg-white p-4 border-bottom-0">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-headset text-success me-2"></i>Pengaturan Operasional PTSP</h5>
            <small class="text-muted">Perbarui data kontak penanggung jawab dan status kesiapan perangkat WA PTSP Satker Anda.</small>
        </div>

        <div class="card-body p-4 pt-0">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form action="{{ route('ptsp.profil-ptsp.update', $satker->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Penanggung Jawab PTSP</label>
                        <input type="text" name="nama_pj" class="form-control form-control-lg" 
                               value="{{ old('nama_pj', $ptsp->nama_pj ?? '') }}" 
                               placeholder="Contoh: Saifuddin, S.Ag,.M.H" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. HP / WA Penanggung Jawab</label>
                        <input type="text" name="no_hp_pj" class="form-control form-control-lg" 
                               value="{{ old('no_hp_pj', $ptsp->no_hp_pj ?? '') }}" 
                               placeholder="Contoh: 082213833201" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Nomor WhatsApp Layanan PTSP (Publik)</label>
                        <input type="text" name="no_wa_layanan" class="form-control form-control-lg" 
                               value="{{ old('no_wa_layanan', $ptsp->no_wa_layanan ?? $satker->whatsapp) }}" 
                               placeholder="Contoh: 081297545703">
                        <small class="text-muted">Nomor ini yang akan dihubungi oleh masyarakat melalui portal publik.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status Kesiapan WA PTSP</label>
                        <select name="has_whatsapp_service" class="form-select form-select-lg">
                            <option value="1" {{ (old('has_whatsapp_service', $ptsp->has_whatsapp_service ?? true)) ? 'selected' : '' }}>
                                ✅ Ada dan Siap Digunakan
                            </option>
                            <option value="0" {{ (!old('has_whatsapp_service', $ptsp->has_whatsapp_service ?? true)) ? 'selected' : '' }}>
                                ⚠️ Belum Siap / Ada Kendala
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status Panggilan Suara (Headset Call)</label>
                        <select name="is_call_able" class="form-select form-select-lg">
                            <option value="1" {{ (old('is_call_able', $ptsp->is_call_able ?? false)) ? 'selected' : '' }}>
                                🎧 Aktif (Perangkat Audio Ready)
                            </option>
                            <option value="0" {{ (!old('is_call_able', $ptsp->is_call_able ?? false)) ? 'selected' : '' }}>
                                🚫 Tidak Aktif (Tombol Telepon Merah)
                            </option>
                        </select>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-success btn-lg px-4" style="border-radius: 12px; font-weight: 700;">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection