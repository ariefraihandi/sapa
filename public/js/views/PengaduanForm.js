import { storePengaduan } from '../ptsp-api.js';

export function renderPengaduanForm(containerEl, { serverUrl, satkerId, onBack, onSuccess }) {
    containerEl.innerHTML = `
        <button type="button" class="ptsp-btn-back" id="ptspBtnBackPengaduan">← Kembali ke Pilihan Menu</button>
        <form id="ptspFormPengaduanInner">
            <div class="ptsp-form-group">
                <label>Nama Pelapor *</label>
                <input type="text" class="ptsp-input" id="ptsp_p_nama" placeholder="Nama Lengkap Anda" required />
            </div>

            <div class="ptsp-row">
                <div class="ptsp-col ptsp-form-group">
                    <label>No. HP / WhatsApp *</label>
                    <input type="text" class="ptsp-input ptsp-input-hp" id="ptsp_p_hp" placeholder="08..." maxlength="15" required />
                </div>
                <div class="ptsp-col ptsp-form-group">
                    <label>NIK / No. KTP (Opsional)</label>
                    <input type="text" class="ptsp-input" id="ptsp_p_nik" placeholder="16 Digit NIK" maxlength="16" />
                </div>
            </div>

            <div class="ptsp-form-group">
                <label>Uraian Pengaduan *</label>
                <textarea class="ptsp-textarea" id="ptsp_p_uraian" rows="3" placeholder="Tuliskan detail pengaduan Anda secara ringkas dan jelas..." required></textarea>
            </div>

            <button type="submit" class="ptsp-btn-submit ptsp-btn-danger" id="ptspSubmitPengaduanBtn">Lanjutkan ke Petugas</button>
        </form>
    `;

    // Event Back
    containerEl.querySelector('#ptspBtnBackPengaduan').addEventListener('click', () => {
        if (typeof onBack === 'function') onBack();
    });

    // Sanitasi Angka Input
    containerEl.querySelectorAll('.ptsp-input-hp, #ptsp_p_nik').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // Submit Handler
    const form = containerEl.querySelector('#ptspFormPengaduanInner');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const hpInput = containerEl.querySelector('#ptsp_p_hp');
        const noHpVal = hpInput.value;

        if (!noHpVal.startsWith('08') || noHpVal.length < 10) {
            hpInput.focus();
            return;
        }

        const submitBtn = containerEl.querySelector('#ptspSubmitPengaduanBtn');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Menyimpan Data...';

        const payload = {
            satker_id: satkerId,
            nama_pelapor: containerEl.querySelector('#ptsp_p_nama').value,
            no_hp: noHpVal,
            nik: containerEl.querySelector('#ptsp_p_nik').value || null,
            uaraian_pengaduan: containerEl.querySelector('#ptsp_p_uraian').value
        };

        storePengaduan(serverUrl, payload)
            .then(res => {
                if (res.status === 'success' && typeof onSuccess === 'function') {
                    onSuccess(res, payload);
                }
            })
            .catch(err => console.error('Submit Pengaduan Error:', err))
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Lanjutkan ke Petugas';
            });
    });
}