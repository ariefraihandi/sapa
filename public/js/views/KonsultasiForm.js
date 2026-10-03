import { storeKonsultasi } from '../ptsp-api.js';

const daftarPekerjaan = [
    'Pegawai Negeri Sipil',
    'Karyawan swasta',
    'Pedagang',
    'Petani/pekebun',
    'Nelayan/perikanan',
    'Mengurus rumah tangga',
    'Pelajar/Mahasiswa',
    'Karyawan Honorer',
    'Buruh harian lepas',
    'Lainnya'
];

export function renderKonsultasiForm(containerEl, { serverUrl, satkerId, onBack }) {
    const pekerjaanOptions = daftarPekerjaan
        .map(item => `<option value="${item}">${item}</option>`)
        .join('');

    containerEl.innerHTML = `
        <button type="button" class="ptsp-btn-back" id="ptspBtnBackKonsultasi">← Kembali ke Pilihan Menu</button>
        <form id="ptspFormKonsultasiInner">
            <div class="ptsp-row">
                <div class="ptsp-col ptsp-form-group">
                    <label>Jenis Layanan *</label>
                    <select class="ptsp-select" id="ptsp_jenis_layanan" required>
                        <option value="pesan" selected>WhatsApp</option>
                        <option value="telepon">Telepon (WA)</option>
                    </select>
                </div>
                <div class="ptsp-col ptsp-form-group">
                    <label>Jenis Kelamin *</label>
                    <select class="ptsp-select" id="ptsp_jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="ptsp-form-group">
                <label>Nama Lengkap *</label>
                <input type="text" class="ptsp-input" id="ptsp_nama_responden" placeholder="Nama Anda" required />
            </div>

            <div class="ptsp-form-group">
                <label>No. HP / WhatsApp *</label>
                <input type="text" class="ptsp-input ptsp-input-hp" id="ptsp_no_hp" placeholder="08..." maxlength="15" required />
            </div>

            <div class="ptsp-row">
                <div class="ptsp-col ptsp-form-group">
                    <label>Pekerjaan</label>
                    <select class="ptsp-select" id="ptsp_pekerjaan">
                        <option value="">-- Pilih --</option>
                        ${pekerjaanOptions}
                    </select>
                </div>
                <div class="ptsp-col ptsp-form-group">
                    <label>Pendidikan</label>
                    <select class="ptsp-select" id="ptsp_pendidikan">
                        <option value="">-- Pilih --</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA/SMK</option>
                        <option value="D3">D3</option>
                        <option value="S1">S1/D4</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
                </div>
            </div>

            <div class="ptsp-form-group">
                <label>Keperluan / Pertanyaan Anda *</label>
                <textarea class="ptsp-textarea" id="ptsp_keperluan" rows="2" placeholder="Tuliskan pertanyaan/keperluan Anda..." required></textarea>
            </div>

            <button type="submit" class="ptsp-btn-submit" id="ptspSubmitKonsultasiBtn">Ajukan Pertanyaan</button>
        </form>
    `;

    // Event Back
    containerEl.querySelector('#ptspBtnBackKonsultasi').addEventListener('click', () => {
        if (typeof onBack === 'function') onBack();
    });

    // Sanitasi Input No HP
    const hpInput = containerEl.querySelector('#ptsp_no_hp');
    hpInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Submit Form Konsultasi
    const form = containerEl.querySelector('#ptspFormKonsultasiInner');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const noHpVal = hpInput.value;
        if (!noHpVal.startsWith('08') || noHpVal.length < 10) {
            hpInput.focus();
            return;
        }

        const submitBtn = containerEl.querySelector('#ptspSubmitKonsultasiBtn');
        submitBtn.disabled = true;
        
        // Indikator Loading
        submitBtn.innerHTML = '⏳ Menganalisis & mencari jawaban...';

        const payload = {
            satker_id: satkerId,
            jenis_layanan: containerEl.querySelector('#ptsp_jenis_layanan').value,
            jenis_kelamin: containerEl.querySelector('#ptsp_jenis_kelamin').value,
            nama_responden: containerEl.querySelector('#ptsp_nama_responden').value,
            no_hp: noHpVal,
            pekerjaan: containerEl.querySelector('#ptsp_pekerjaan').value || null,
            pendidikan: containerEl.querySelector('#ptsp_pendidikan').value || null,
            keperluan: containerEl.querySelector('#ptsp_keperluan').value
        };

        storeKonsultasi(serverUrl, payload)
            .then(res => {
                if (res.status === 'success') {
                    // Delay singkat agar animasi loading terasa, lalu tampilkan hasil
                    setTimeout(() => {
                        renderSuggestionsResult(containerEl, res, payload, onBack);
                    }, 400);
                }
            })
            .catch(err => {
                console.error('Submit Konsultasi Error:', err);
                submitBtn.disabled = false;
                submitBtn.innerText = 'Ajukan Pertanyaan';
            });
    });
}

// Tampilan Hasil Saran Jawaban & Tombol WA
function renderSuggestionsResult(containerEl, resData, payload, onBack) {
    const hasSuggestions = resData.has_suggestions && resData.suggestions && resData.suggestions.length > 0;

    let resultHtml = '';

    if (hasSuggestions) {
        const listHtml = resData.suggestions.map(item => `
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; margin-bottom: 8px;">
                <strong style="font-size: 13px; color: #1e293b; display: block; margin-bottom: 4px;">📌 ${item.nama_layanan}</strong>
                <span style="font-size: 11px; color: #64748b; display: block; margin-bottom: 6px;">Kategori: ${item.kategori}</span>
                <a href="${item.url}" target="_blank" style="font-size: 12px; color: #006633; font-weight: 600; text-decoration: underline;">
                    📄 Lihat Detail Persyaratan &rarr;
                </a>
            </div>
        `).join('');

        resultHtml = `
            <h4 style="margin: 0 0 6px 0; font-size: 13px; color: #334155;">Apakah ini informasi yang Anda maksud?</h4>
            <p style="font-size: 11px; color: #64748b; margin-bottom: 10px;">Sistem menemukan informasi persyaratan perkara yang sesuai:</p>
            <div style="max-height: 200px; overflow-y: auto; margin-bottom: 12px;">
                ${listHtml}
            </div>
        `;
    } else {
        resultHtml = `
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center; margin-bottom: 12px;">
                <span style="font-size: 20px; display: block; margin-bottom: 4px;">🔍</span>
                <strong style="font-size: 12px; color: #334155; display: block;">Jawaban Otomatis Tidak Ditemukan</strong>
                <p style="font-size: 11px; color: #64748b; margin: 4px 0 0 0;">
                    Pertanyaan Anda telah dicatat. Silakan hubungi petugas untuk informasi lebih mendalam.
                </p>
            </div>
        `;
    }

    containerEl.innerHTML = `
        <button type="button" class="ptsp-btn-back" id="ptspBtnBackToMenu">← Kembali ke Pilihan Menu</button>
        <div style="padding: 4px;">
            ${resultHtml}

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 12px 0;" />
            
            <p style="font-size: 11px; color: #64748b; text-align: center; margin-bottom: 8px;">
                ${hasSuggestions ? 'Belum menemukan jawaban? Hubungi petugas kami:' : 'Hubungi petugas kami sekarang:'}
            </p>
            
            <button id="ptspBtnLanjutWa" class="ptsp-btn-submit" style="width: 100%; border: none;">
                💬 Hubungi Petugas via WhatsApp
            </button>
        </div>
    `;

    // Listener Tombol Kembali
    containerEl.querySelector('#ptspBtnBackToMenu').addEventListener('click', () => {
        if (typeof onBack === 'function') onBack();
    });

    // Listener Tombol Pengalihan ke WA
    containerEl.querySelector('#ptspBtnLanjutWa').addEventListener('click', () => {
        if (payload.jenis_layanan === 'telepon' && resData.phone_number) {
            window.location.href = `tel:${resData.phone_number}`;
        } else if (resData.redirect_url) {
            window.open(resData.redirect_url, '_blank');
        }
    });
}