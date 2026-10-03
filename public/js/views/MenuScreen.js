export function renderMenuScreen(containerEl, onSelectKonsultasi, onSelectPengaduan) {
    containerEl.innerHTML = `
        <p style="text-align: center; font-size: 13px; color: #475569; margin-top: 0; margin-bottom: 14px;">
            Silakan pilih jenis layanan yang Anda butuhkan:
        </p>
        <button class="ptsp-menu-btn" id="ptspBtnSelectKonsultasi">
            <span class="ptsp-menu-icon">💬</span>
            <div>
                <span class="ptsp-menu-title">Layanan Informasi / Konsultasi</span>
                <span class="ptsp-menu-desc">Tanya jawab persyaratan, perkara, & layanan PTSP</span>
            </div>
        </button>
        <button class="ptsp-menu-btn" id="ptspBtnSelectPengaduan">
            <span class="ptsp-menu-icon">⚠️</span>
            <div>
                <span class="ptsp-menu-title">Laporan Pengaduan</span>
                <span class="ptsp-menu-desc">Sampaikan pengaduan layanan atau perilaku petugas</span>
            </div>
        </button>
    `;

    containerEl.querySelector('#ptspBtnSelectKonsultasi').addEventListener('click', () => {
        if (typeof onSelectKonsultasi === 'function') onSelectKonsultasi();
    });

    containerEl.querySelector('#ptspBtnSelectPengaduan').addEventListener('click', () => {
        if (typeof onSelectPengaduan === 'function') onSelectPengaduan();
    });
}