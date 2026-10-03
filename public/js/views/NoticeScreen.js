export function renderNoticeScreen(containerEl, onContinue) {
    containerEl.innerHTML = `
        <div class="ptsp-notice-box">
            <strong>ℹ️ Informasi Jam Layanan</strong><br><br>
            Saat ini layanan sedang berada di <strong>luar jam operasional</strong>.<br>
            <small style="color: #666; display:block; margin: 6px 0;">(Senin–Kamis: 08.00–16.30 WIB | Jumat: 08.00–17.00 WIB)</small>
            Pesan Anda akan kami respon setelah jam kerja dimulai.
        </div>
        <button type="button" class="ptsp-btn-next" id="ptspBtnContinue">Tetap Lanjutkan</button>
    `;

    containerEl.querySelector('#ptspBtnContinue').addEventListener('click', () => {
        if (typeof onContinue === 'function') onContinue();
    });
}