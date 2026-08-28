@php
    $no_ptsp_clean = preg_replace('/[^0-9]/', '', $item['no_ptsp'] ?? '');
    if (str_starts_with($no_ptsp_clean, '0')) {
        $no_ptsp_clean = '62' . substr($no_ptsp_clean, 1);
    }
    
    $link_wa_chat = !empty($no_ptsp_clean) ? "https://wa.me/" . $no_ptsp_clean : "#";
    $has_whatsapp_service = $item['has_whatsapp_service'] ?? false;
    $is_call_able = $item['is_call_able'] ?? false;
    $satker_vshort = $item['satker_vshort'] ?? $item['id'];
@endphp

<div class="satker-card" data-title="{{ strtolower($item['nama_satker']) }} {{ strtolower($item['wilayah_kerja'] ?? '') }}">
    <div>
        <div class="card-header-icon">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <h2 class="satker-title">{{ $item['nama_satker'] }}</h2>      
    </div>
    
    <div class="card-actions-group" style="display: flex; flex-direction: column; gap: 8px; margin-top: 1rem;">
        <div style="display: flex; gap: 8px;">
            <!-- Tombol Pesan WA -->
            @if($has_whatsapp_service)
                <button type="button" 
                        onclick="showFormPengunjung('{{ $item['id'] }}', '{{ $item['nama_satker'] }}', 'pesan', '{{ $link_wa_chat }}')"
                        style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.8rem; justify-content: center; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-brands fa-whatsapp"></i> Pesan
                </button>
            @else
                <button type="button" 
                        onclick="showWaDisabledAlert('{{ $item['nama_satker'] }}')"
                        style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.8rem; justify-content: center; display: flex; align-items: center; gap: 6px; border-radius: 12px; font-weight: 600; color: white; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); border: none; cursor: pointer; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);">
                    <i class="fa-solid fa-triangle-exclamation"></i> Pesan
                </button>
            @endif

            <!-- Tombol Telepon WA -->
            @if($is_call_able)
                <button type="button" 
                        onclick="showFormPengunjung('{{ $item['id'] }}', '{{ $item['nama_satker'] }}', 'telepon', '{{ $link_wa_chat }}')"
                        style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.8rem; justify-content: center; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-phone"></i> Telepon
                </button>
            @else
                <button type="button" 
                        onclick="showCallDisabledAlert('{{ $item['nama_satker'] }}')"
                        style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.8rem; justify-content: center; display: flex; align-items: center; gap: 6px; border-radius: 12px; font-weight: 600; color: white; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); border: none; cursor: pointer; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);">
                    <i class="fa-solid fa-phone-slash"></i> Telepon
                </button>
            @endif
        </div>

        <!-- Tombol Syarat Perkara -->
        <a href="{{ url('/layanan/persyaratan-perkara/' . $satker_vshort) }}" 
           class="btn-wa" 
           style="background: #ffffff; color: var(--primary); border: 1.5px solid var(--primary); padding: 0.6rem 1rem; font-size: 0.825rem; box-shadow: none;">
            <i class="fa-solid fa-list-check"></i> Syarat Perkara
        </a>

        <!-- Tombol Pengaduan (MEMUAT MODAL SWEETALERT2) -->
        <button type="button" 
                onclick="showFormPengaduan('{{ $item['id'] }}', '{{ $item['nama_satker'] }}', '{{ $link_wa_chat }}')"
                class="btn-wa" 
                style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; padding: 0.55rem 1rem; font-size: 0.8rem; box-shadow: none; cursor: pointer; width: 100%; justify-content: center;">
            <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i> Pengaduan
        </button>
    </div>
</div>