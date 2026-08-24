@php
    $namaSatker = $item['nama_satker'] ?? $item['satker_name'] ?? '-';
    $wilayah = $item['wilayah_kerja'] ?? $item['satker_city'] ?? 'Provinsi Aceh';
    $satkerId = $item['satker_vshort'] ?? $item['id'] ?? null;
@endphp

<div class="satker-card" data-title="{{ strtolower($namaSatker) }} {{ strtolower($wilayah) }}">
    <div>
        <div class="card-header-icon">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        
        <h2 class="satker-title">{{ $namaSatker }}</h2>
        
        <p class="satker-region">
            <i class="fa-solid fa-location-dot"></i> 
            {{ $wilayah }}
        </p>
    </div>
    
    <a href="{{ url('/layanan/persyaratan-perkara/' . $satkerId) }}" 
       class="btn-wa" style="background: linear-gradient(135deg, #047857 0%, #10b981 100%);">
        <i class="fa-solid fa-folder-open"></i> Lihat Syarat Perkara
    </a>
</div>