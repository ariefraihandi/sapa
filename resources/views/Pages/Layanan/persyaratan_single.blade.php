<!DOCTYPE html>
<html lang="id">

    {{-- INCLUDE HEAD & STYLES --}}
    @include('Pages.Layanan.Partials.head')

    <body>

        {{-- NAVBAR PARTIAL --}}
        @include('Pages.Layanan.Partials.navbar')

        <!-- HERO SECTION -->
        <section class="hero">
            <h1 class="hero-title">
                Persyaratan <span>{{ $jenisPerkara->nama_layanan }}</span>
            </h1>
            <p class="hero-subtitle">
                {{ $satker->satker_name }} — Informasi Resmi Kelengkapan Dokumen Perkara
            </p>
        </section>

        <!-- MAIN CONTAINER -->
        <main class="main-container">

            {{-- TOMBOL KEMBALI & INFORMASI RINGKAS --}}
            <div style="margin-bottom: 2rem;" class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <a href="{{ route('public.persyaratan-perkara.detail', $satker->satker_vshort) }}" class="btn-auth btn-login" style="display: inline-flex; width: auto; align-items: center; text-decoration: none;">
                    <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar Perkara
                </a>

                <button type="button" onclick="copyCurrentUrl(this)" class="btn-auth" style="display: inline-flex; width: auto; align-items: center; background: #ffffff; color: var(--text-main); border: 1px solid #cbd5e1; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600; cursor: pointer;">
                    <i class="fa-solid fa-link me-2 text-primary"></i> Salin Link Halaman
                </button>
            </div>

            {{-- CARD UTAMA DETAIL SYARAT --}}
            <div style="background: #ffffff; border-radius: 20px; border: 1px solid #f1f5f9; padding: 2rem; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); max-width: 850px; margin: 0 auto;">
                
                {{-- BADGE KATEGORI & SATKER --}}
                <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
                    <span class="domain-badge" style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
                        <i class="fa-solid fa-scale-balanced me-1"></i> {{ $jenisPerkara->kategori ?? 'Umum' }}
                    </span>
                    <span style="background: #f1f5f9; color: #475569; font-size: 0.8rem; font-weight: 700; padding: 4px 12px; border-radius: 50px;">
                        <i class="fa-solid fa-building-columns me-1"></i> {{ $satker->satker_name }}
                    </span>
                </div>

                {{-- NAMA PERKARA --}}
                <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 1rem; line-height: 1.3;">
                    {{ $jenisPerkara->nama_layanan }}
                </h2>

                {{-- DESKRIPSI (JIKA ADA) --}}
                @if(!empty($jenisPerkara->deskripsi))
                    <div style="font-size: 0.925rem; color: var(--text-muted); line-height: 1.6; background: var(--bg-accent, #f8fafc); padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 4px solid var(--primary, #0284c7);">
                        {{ $jenisPerkara->deskripsi }}
                    </div>
                @endif

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 1.5rem 0;">

                {{-- SUBTITLE DOKUMEN --}}
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-list-check text-primary"></i> Dokumen yang Wajib Dilengkapi:
                </h3>

                {{-- DAFTAR DOKUMEN SYARAT --}}
                @if($dokumenList->count() > 0)
                    <ol style="padding-left: 1.2rem; margin: 0 0 2rem 0;">
                        @foreach($dokumenList as $doc)
                            <li style="margin-bottom: 1.25rem; font-size: 0.95rem; font-weight: 600; color: var(--text-main);">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <span style="flex: 1; min-width: 220px; line-height: 1.5;">
                                        {{ $doc->syarat_dokumen }}
                                    </span>

                                    {{-- TOMBOL DOKUMEN CONTOH/PENDUKUNG (JIKA ADA URL) --}}
                                    @if(!empty($doc->url_dokumen))
                                        <a href="{{ $doc->url_dokumen }}" target="_blank" rel="noopener noreferrer" 
                                           style="display: inline-flex; align-items: center; gap: 6px; background-color: #f0fdf4; color: #047857; border: 1px solid rgba(16, 185, 129, 0.3); padding: 5px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease;">
                                            <i class="fa-solid fa-file-pdf text-danger"></i> Lihat Contoh Dokumen
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <div style="text-align: center; padding: 2rem; background: #fffbe3; border-radius: 12px; color: #856404; margin-bottom: 2rem; font-weight: 600;">
                        Belum ada dokumen persyaratan yang diunggah untuk perkara ini.
                    </div>
                @endif

                {{-- FOOTER ACTION BUTTONS --}}
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #f8fafc; padding: 1.25rem; border-radius: 14px; border: 1px solid #e2e8f0;">
                    
                    {{-- TOMBOL DOWNLOAD PDF --}}
                    <a href="{{ route('public.persyaratan-perkara.download-pdf', ['satker_vshort' => $satker->satker_vshort, 'jenis_perkara_id' => $jenisPerkara->id]) }}" 
                       target="_blank" 
                       style="display: inline-flex; align-items: center; gap: 8px; background-color: #ef4444; color: #ffffff; padding: 0.65rem 1.25rem; border-radius: 10px; font-size: 0.9rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);">
                        <i class="fa-solid fa-file-pdf"></i> Download PDF Persyaratan
                    </a>

                    {{-- INFORMASI/AKSI TAMBAHAN --}}
                    <span style="font-size: 0.825rem; color: var(--text-muted);">
                        <i class="fa-solid fa-circle-info me-1"></i> Dokumen resmi {{ $satker->satker_name }}
                    </span>
                </div>

            </div>
        </main>

        {{-- FOOTER PARTIAL --}}
        @include('Pages.Layanan.Partials.footer')

        {{-- SCRIPT COPY URL --}}
        <script>
            function copyCurrentUrl(btnElement) {
                const url = window.location.href;
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(url).then(() => showCopySuccess(btnElement));
                } else {
                    let textArea = document.createElement("textarea");
                    textArea.value = url;
                    textArea.style.position = "fixed";
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        showCopySuccess(btnElement);
                    } catch (err) {
                        alert('Gagal menyalin link');
                    }
                    document.body.removeChild(textArea);
                }
            }

            function showCopySuccess(btnElement) {
                const originalText = btnElement.innerHTML;
                btnElement.innerHTML = '<i class="fa-solid fa-check text-success me-2"></i> Link Tersalin!';
                btnElement.style.background = '#dcfce7';
                btnElement.style.borderColor = '#86efac';

                setTimeout(() => {
                    btnElement.innerHTML = originalText;
                    btnElement.style.background = '#ffffff';
                    btnElement.style.borderColor = '#cbd5e1';
                }, 2000);
            }
        </script>
    </body>
</html>