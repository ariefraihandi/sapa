<!DOCTYPE html>
<html lang="id">

    {{-- INCLUDE HEAD & STYLES --}}
    @include('Pages.Layanan.Partials.head')

    <body>

        {{-- NAVBAR PARTIAL --}}
        @include('Pages.Layanan.Partials.navbar')

        <!-- HERO SECTION -->
        <section class="hero">
            <h1 class="hero-title">Syarat Perkara <span>{{ $satker->satker_name }}</span></h1>
            <p class="hero-subtitle">Pilih jenis perkara di bawah ini untuk melihat daftar kelengkapan dokumen persyaratan resmi.</p>
            
            <!-- Search Bar -->
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" onkeyup="filterPerkara()" placeholder="Cari Jenis Perkara (misal: Cerai Gugat, Isbat, Waris)...">
            </div>
        </section>

        <!-- MAIN CONTAINER -->
        <main class="main-container">
            
            <!-- Tombol Kembali -->
            <div style="margin-bottom: 2rem;">
                <a href="{{ route('public.persyaratan-perkara') }}" class="btn-auth btn-login" style="display: inline-flex; width: auto;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Satker
                </a>
            </div>

            <!-- GRID CONTAINER CARD PERKARA -->
            <div class="grid-container" id="perkaraGrid">
                @forelse($jenisPerkaraGrouped as $index => $item)
                    <div class="satker-card perkara-card" data-title="{{ strtolower($item->jenisPerkara->nama_layanan ?? '') }} {{ strtolower($item->jenisPerkara->kategori ?? '') }}">
                        <div>
                            <div class="card-header-icon">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            
                            <!-- Kategori -->
                            <span class="domain-badge" style="display: inline-flex; margin-bottom: 0.5rem; font-size: 0.75rem;">
                                {{ $item->jenisPerkara->kategori ?? 'Umum' }}
                            </span>

                            <!-- Nama Perkara -->
                            <h2 class="satker-title">{{ $item->jenisPerkara->nama_layanan ?? '-' }}</h2>
                            
                            <!-- Status Dokumen -->
                            <p class="satker-region">
                                <i class="fa-solid fa-file-circle-check"></i> 
                                {{ $item->dokumenList->count() }} Dokumen Persyaratan
                            </p>
                        </div>
                        
                        <!-- Tombol Aksi Buka Modal Detail -->
                        <button type="button" class="btn-wa" onclick="openDetailModal({{ $index }})" style="border: none; cursor: pointer; width: 100%;">
                            <i class="fa-solid fa-eye"></i> Lihat Syarat Dokumen
                        </button>
                    </div>

                    <!-- MODAL POPUP DETAIL DOKUMEN -->
                    <div id="modalDetail{{ $index }}" class="custom-modal-backdrop" style="display: none;">
                        <div class="custom-modal-content">
                            <div class="custom-modal-header">
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--primary); font-weight: 700; text-transform: uppercase;">
                                        {{ $item->jenisPerkara->kategori ?? 'Umum' }}
                                    </span>
                                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin: 2px 0 0;">
                                        {{ $item->jenisPerkara->nama_layanan ?? '-' }}
                                    </h3>
                                </div>
                                <button type="button" class="btn-close-custom" onclick="closeDetailModal({{ $index }})">&times;</button>
                            </div>

                            <div class="custom-modal-body">
                                @if(!empty($item->jenisPerkara->deskripsi))
                                    <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem; line-height: 1.5; background: var(--bg-accent); padding: 0.85rem; border-radius: 10px;">
                                        {{ $item->jenisPerkara->deskripsi }}
                                    </p>
                                @endif

                                <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem;">
                                    <i class="fa-solid fa-list-check text-primary" style="margin-right: 6px;"></i> Dokumen yang Wajib Dilengkapi:
                                </h4>

                                <ol style="padding-left: 1.2rem; margin: 0;">
                                    @foreach($item->dokumenList as $doc)
                                        <li style="margin-bottom: 1rem; font-size: 0.9rem; font-weight: 600; color: var(--text-main);">
                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                <span style="flex: 1; min-width: 200px; line-height: 1.4;">
                                                    {{ $doc->syarat_dokumen }}<br>
                                                </span>

                                                {{-- TOMBOL LIHAT CONTOH DOKUMEN (JIKA ADA URL) --}}
                                                @if(!empty($doc->url_dokumen))
                                                    <a href="{{ $doc->url_dokumen }}" target="_blank" rel="noopener noreferrer" 
                                                    style="display: inline-flex; align-items: center; gap: 6px; background-color: #f0fdf4; color: #047857; border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 10px; border-radius: 50px; font-size: 0.775rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease;">
                                                        <i class="fa-solid fa-file-pdf text-danger"></i> Lihat Contoh Dokumen
                                                    </a>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>

                            <div class="custom-modal-footer">
                                <button type="button" class="btn-auth btn-login" onclick="closeDetailModal({{ $index }})" style="border-radius: 10px; padding: 0.6rem 1.25rem;">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; background: #ffffff; border-radius: 20px; border: 1px solid #f1f5f9;">
                        <i class="fa-solid fa-folder-open fa-3x text-muted" style="margin-bottom: 1rem; display: block;"></i>
                        <h3 style="font-weight: 700; color: var(--text-main);">Belum Ada Syarat Perkara</h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Satuan Kerja ini belum mempublikasikan persyaratan perkara.</p>
                    </div>
                @endforelse
            </div>
        </main>

        {{-- FOOTER PARTIAL --}}
        @include('Pages.Layanan.Partials.footer')

        <!-- CSS Tambahan Khusus Modal Popup -->
        <style>
            .custom-modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }

            .custom-modal-content {
                background: #ffffff;
                width: 100%;
                max-width: 600px;
                border-radius: 20px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                overflow: hidden;
                animation: modalFadeIn 0.25s ease-out;
            }

            .custom-modal-header {
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .btn-close-custom {
                background: none;
                border: none;
                font-size: 1.75rem;
                line-height: 1;
                color: var(--text-muted);
                cursor: pointer;
            }

            .btn-close-custom:hover {
                color: var(--text-main);
            }

            .custom-modal-body {
                padding: 1.5rem;
                max-height: 65vh;
                overflow-y: auto;
            }

            .custom-modal-footer {
                padding: 1rem 1.5rem;
                background: #f8fafc;
                border-top: 1px solid #e2e8f0;
                text-align: right;
            }

            @keyframes modalFadeIn {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }
        </style>

        <!-- SCRIPT PENCARIAN REAL-TIME & MODAL CONTROLLER -->
        <script>
            function filterPerkara() {
                let input = document.getElementById('searchInput').value.toLowerCase();
                let cards = document.getElementsByClassName('perkara-card');

                for (let i = 0; i < cards.length; i++) {
                    let titleData = cards[i].getAttribute('data-title');
                    if (titleData.includes(input)) {
                        cards[i].style.display = "flex";
                    } else {
                        cards[i].style.display = "none";
                    }
                }
            }

            function openDetailModal(index) {
                document.getElementById('modalDetail' + index).style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeDetailModal(index) {
                document.getElementById('modalDetail' + index).style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        </script>
    </body>
</html>