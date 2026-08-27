@php
    // Membersihkan nomor telepon untuk link WhatsApp
    $no_ptsp_clean = preg_replace('/[^0-9]/', '', $item['no_ptsp'] ?? '');
    if (str_starts_with($no_ptsp_clean, '0')) {
        $no_ptsp_clean = '62' . substr($no_ptsp_clean, 1);
    }
    
    $link_wa_chat = !empty($no_ptsp_clean) ? "https://wa.me/" . $no_ptsp_clean : "#";
    $has_whatsapp_service = $item['has_whatsapp_service'] ?? false; // <-- Status WA
    $is_call_able = $item['is_call_able'] ?? false;
    $satker_vshort = $item['satker_vshort'] ?? $item['id'];
@endphp

<div class="satker-card" data-title="{{ strtolower($item['nama_satker']) }} {{ strtolower($item['wilayah_kerja'] ?? '') }}">
    <div>
        <div class="card-header-icon">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        
        <!-- Nama Satker -->
        <h2 class="satker-title">{{ $item['nama_satker'] }}</h2>      
    </div>
    
    <!-- GROUPING TOMBOL AKSI -->
    <div class="card-actions-group" style="display: flex; flex-direction: column; gap: 8px; margin-top: 1rem;">
        
        <!-- ROW 1: SEJAJAR PESAN WA & TELEPON WA -->
        <div style="display: flex; gap: 8px;">
            <!-- Tombol Kirim Pesan WA (Hijau jika siap, Merah jika ada kendala/0) -->
            @if($has_whatsapp_service)
                <a href="javascript:void(0);" 
                   data-link="{{ $link_wa_chat }}" 
                   data-satker="{{ $item['nama_satker'] }}"
                   class="btn-wa btn-wa-confirm" 
                   style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.8rem; justify-content: center; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <i class="fa-brands fa-whatsapp"></i> Pesan
                </a>
            @else
                <button type="button" 
                        class="btn-wa-disabled" 
                        onclick="showWaDisabledAlert('{{ $item['nama_satker'] }}')"
                        style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.8rem; justify-content: center; display: flex; align-items: center; gap: 6px; border-radius: 12px; font-weight: 600; color: white; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); border: none; cursor: pointer; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);">
                    <i class="fa-solid fa-triangle-exclamation"></i> Pesan
                </button>
            @endif

            <!-- Tombol Telepon WA (Merah jika non-active, Hijau jika active) -->
            @if($is_call_able)
                <a href="javascript:void(0);" 
                   data-link="{{ $link_wa_chat }}" 
                   data-satker="{{ $item['nama_satker'] }}"
                   class="btn-wa btn-wa-confirm" 
                   style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.8rem; justify-content: center; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <i class="fa-solid fa-phone"></i> Telepon
                </a>
            @else
                <button type="button" 
                        class="btn-call-disabled" 
                        onclick="showCallDisabledAlert('{{ $item['nama_satker'] }}')"
                        style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.8rem; justify-content: center; display: flex; align-items: center; gap: 6px; border-radius: 12px; font-weight: 600; color: white; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); border: none; cursor: pointer; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);">
                    <i class="fa-solid fa-phone-slash"></i> Telepon
                </button>
            @endif
        </div>

        <!-- ROW 2: TOMBOL PERSYARATAN PERKARA -->
        <a href="{{ url('/layanan/persyaratan-perkara/' . $satker_vshort) }}" 
           class="btn-wa" 
           style="background: #ffffff; color: var(--primary); border: 1.5px solid var(--primary); padding: 0.6rem 1rem; font-size: 0.825rem; box-shadow: none;">
            <i class="fa-solid fa-list-check"></i> Syarat Perkara
        </a>

        <!-- ROW 3: TOMBOL PENGADUAN -->
        <a href="{{ url('/layanan/helpdesk') }}" 
           class="btn-wa" 
           style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; padding: 0.55rem 1rem; font-size: 0.8rem; box-shadow: none;">
            <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i> Pengaduan
        </a>

    </div>
</div>