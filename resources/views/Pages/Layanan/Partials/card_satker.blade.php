@php
    // Membersihkan nomor telepon untuk link WhatsApp
    $no_ptsp_clean = preg_replace('/[^0-9]/', '', $item['no_ptsp']);
    if (str_starts_with($no_ptsp_clean, '0')) {
        $no_ptsp_clean = '62' . substr($no_ptsp_clean, 1);
    }
    $link_wa = !empty($no_ptsp_clean) ? "https://wa.me/" . $no_ptsp_clean : "#";
@endphp

<div class="satker-card" data-title="{{ strtolower($item['nama_satker']) }} {{ strtolower($item['wilayah_kerja'] ?? '') }}">
    <div>
        <div class="card-header-icon">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        
        <!-- Nama Satker -->
        <h2 class="satker-title">{{ $item['nama_satker'] }}</h2>
        
        <!-- Wilayah Kerja / Kabupaten -->
        <p class="satker-region">
            <i class="fa-solid fa-location-dot"></i> 
            {{ $item['wilayah_kerja'] ?? 'Provinsi Aceh' }}
        </p>
    </div>
    
    <!-- Tombol WA dengan Konfirmasi SweetAlert -->
    <a href="javascript:void(0);" 
       data-link="{{ $link_wa }}" 
       data-satker="{{ $item['nama_satker'] }}"
       class="btn-wa btn-wa-confirm">
        <i class="fa-brands fa-whatsapp"></i> Hubungi PTSP
    </a>
</div>