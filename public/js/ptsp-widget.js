(function () {
    // 1. Dapatkan Script Element & Atribut Satker
    const scriptEl = document.currentScript || document.querySelector('script[data-satker-id]');
    if (!scriptEl) {
        console.error('SAPA Widget Error: Script tag tidak ditemukan.');
        return;
    }

    const satkerId = scriptEl.getAttribute('data-satker-id');
    const serverUrl = scriptEl.getAttribute('data-server-url') || 'http://sapa.test';

    if (!satkerId) {
        console.error('SAPA Widget Error: Atribut "data-satker-id" belum diisi.');
        return;
    }

    const daftarPekerjaan = [
        'Pegawai Negeri Sipil', 'Karyawan swasta', 'Pedagang', 'Petani/pekebun',
        'Nelayan/perikanan', 'Mengurus rumah tangga', 'Pelajar/Mahasiswa',
        'Karyawan Honorer', 'Buruh harian lepas', 'Lainnya'
    ];

    // 2. HELPER JAM KERJA (WIB)
    function isJamKerjaWIB() {
        const now = new Date();
        const utcHours = now.getUTCHours();
        const wibHours = (utcHours + 7) % 24;
        const wibMinutes = now.getUTCMinutes();
        const day = now.getUTCDay();

        const totalMenit = wibHours * 60 + wibMinutes;
        const jamMulai = 8 * 60; // 08:00 WIB

        if (day === 0 || day === 6) return false;

        if (day >= 1 && day <= 4) {
            const jamSelesaiKamis = 16 * 60 + 30;
            return totalMenit >= jamMulai && totalMenit <= jamSelesaiKamis;
        }

        if (day === 5) {
            const jamSelesaiJumat = 17 * 60;
            return totalMenit >= jamMulai && totalMenit <= jamSelesaiJumat;
        }

        return false;
    }

    // 3. API FETCH FUNCTIONS
    async function fetchInitData() {
        const res = await fetch(`${serverUrl}/api/ptsp/init-data?satker_id=${satkerId}`);
        const data = await res.json();
        if (!res.ok || data.status === 'error') throw new Error(data.message || 'Akses Widget Ditolak.');
        return data;
    }

    async function storeKonsultasi(payload) {
        const res = await fetch(`${serverUrl}/api/ptsp/store-pengunjung`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal menyimpan data.');
        return data;
    }

    async function storePengaduan(payload) {
        const res = await fetch(`${serverUrl}/api/ptsp/store-pengaduan`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal mengirim pengaduan.');
        return data;
    }

    // 4. BUAT HOST ELEMENT UNTUK SHADOW DOM
    const widgetHost = document.createElement('div');
    widgetHost.id = 'sapa-ptsp-widget-root';
    document.body.appendChild(widgetHost);

    // Attach Shadow Root (Open)
    const shadowRoot = widgetHost.attachShadow({ mode: 'open' });

    // 5. INJECT ISOLATED CSS VIA SHADOW DOM
    const style = document.createElement('style');
    style.innerHTML = `
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }
        .sapa-ptsp-bubble {
            position: fixed; bottom: 20px; right: 20px; z-index: 999999;
            width: 60px; height: 60px; background-color: #25D366;
            border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: transform 0.2s;
        }
        .sapa-ptsp-bubble:hover { transform: scale(1.08); }
        .sapa-ptsp-modal {
            position: fixed; bottom: 90px; right: 20px; z-index: 999999;
            width: 380px; max-width: 90vw; background: #ffffff; border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18); display: none; overflow: hidden;
            color: #333333;
        }
        .sapa-ptsp-header { 
            background: #006633; color: #ffffff; padding: 14px 16px; 
            font-weight: 600; font-size: 14px; text-align: center; white-space: pre-line; 
        }
        .sapa-ptsp-body { padding: 16px; max-height: 75vh; overflow-y: auto; }
        .sapa-ptsp-form-group { margin-bottom: 12px; }
        .sapa-ptsp-form-group label { display: block; font-size: 12px; font-weight: 600; color: #333; margin-bottom: 4px; }
        .sapa-ptsp-input, .sapa-ptsp-select, .sapa-ptsp-textarea {
            width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #cccccc;
            border-radius: 6px; box-sizing: border-box; outline: none; background-color: #ffffff; color: #333333;
        }
        .sapa-ptsp-input:focus, .sapa-ptsp-select:focus, .sapa-ptsp-textarea:focus { border-color: #006633; }
        .sapa-ptsp-row { display: flex; gap: 8px; }
        .sapa-ptsp-col { flex: 1; }
        
        .sapa-ptsp-menu-btn {
            width: 100%; padding: 14px; margin-bottom: 10px; border: 1px solid #e2e8f0;
            border-radius: 8px; background: #f8fafc; text-align: left; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; gap: 12px;
        }
        .sapa-ptsp-menu-btn:hover { background: #f1f5f9; border-color: #006633; }
        .sapa-ptsp-menu-icon { font-size: 20px; width: 32px; text-align: center; }
        .sapa-ptsp-menu-title { font-weight: bold; font-size: 13px; color: #1e293b; display: block; }
        .sapa-ptsp-menu-desc { font-size: 11px; color: #64748b; display: block; margin-top: 2px; }

        .sapa-ptsp-btn-submit, .sapa-ptsp-btn-next {
            width: 100%; background: #25D366; color: white; border: none; padding: 10px;
            border-radius: 6px; font-weight: bold; font-size: 14px; cursor: pointer; margin-top: 6px;
        }
        .sapa-ptsp-btn-submit:hover, .sapa-ptsp-btn-next:hover { background: #20ba5a; }
        .sapa-ptsp-btn-danger { background: #d97706; }
        .sapa-ptsp-btn-danger:hover { background: #b45309; }
        
        .sapa-ptsp-btn-back {
            background: none; border: none; color: #64748b; font-size: 12px; cursor: pointer;
            padding: 4px 0; margin-bottom: 10px; display: inline-flex; align-items: center; gap: 4px;
        }
        .sapa-ptsp-btn-back:hover { color: #006633; text-decoration: underline; }

        .sapa-ptsp-notice-box {
            background: #fff3cd; color: #856404; border: 1px solid #ffeeba;
            padding: 12px; border-radius: 8px; font-size: 13px; line-height: 1.5;
            margin-bottom: 14px; text-align: center;
        }
        .sapa-ptsp-error-text {
            color: #dc2626; font-size: 11px; margin-top: 4px; display: none;
        }
    `;
    shadowRoot.appendChild(style);

    // 6. INJECT MODAL HTML CONTAINER INSIDE SHADOW DOM
    const container = document.createElement('div');
    container.innerHTML = `
        <div class="sapa-ptsp-modal" id="sapaPtspModal">
            <div class="sapa-ptsp-header" id="sapaPtspHeaderTitle">🏛️ Layanan SAPA</div>
            <div class="sapa-ptsp-body" id="sapaPtspScreenContainer"></div>
        </div>
        <div class="sapa-ptsp-bubble" id="sapaPtspBubble" style="display: none;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.82.46 3.53 1.27 5L2 22l5.18-1.24C8.61 21.55 10.26 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
        </div>
    `;
    shadowRoot.appendChild(container);

    const bubble = shadowRoot.getElementById('sapaPtspBubble');
    const modal = shadowRoot.getElementById('sapaPtspModal');
    const screenContainer = shadowRoot.getElementById('sapaPtspScreenContainer');
    let isDomainValid = false;

    // 7. VIEW RENDERING RENDERERS
    function renderNoticeScreen() {
        screenContainer.innerHTML = `
            <div class="sapa-ptsp-notice-box">
                <strong>ℹ️ Informasi Jam Layanan</strong><br><br>
                Saat ini layanan sedang berada di <strong>luar jam operasional</strong>.<br>
                <small style="color: #666; display:block; margin: 6px 0;">(Senin–Kamis: 08.00–16.30 WIB | Jumat: 08.00–17.00 WIB)</small>
                Pesan Anda akan kami respon setelah jam kerja dimulai.
            </div>
            <button type="button" class="sapa-ptsp-btn-next" id="sapaPtspBtnContinue">Tetap Lanjutkan</button>
        `;
        screenContainer.querySelector('#sapaPtspBtnContinue').addEventListener('click', renderMenuScreen);
    }

    function renderMenuScreen() {
        screenContainer.innerHTML = `
            <p style="text-align: center; font-size: 13px; color: #475569; margin-top: 0; margin-bottom: 14px;">
                Silakan pilih jenis layanan yang Anda butuhkan:
            </p>
            <button class="sapa-ptsp-menu-btn" id="sapaPtspBtnSelectKonsultasi">
                <span class="sapa-ptsp-menu-icon">💬</span>
                <div>
                    <span class="sapa-ptsp-menu-title">Layanan Informasi / Konsultasi</span>
                    <span class="sapa-ptsp-menu-desc">Tanya jawab persyaratan, perkara, & layanan PTSP</span>
                </div>
            </button>
            <button class="sapa-ptsp-menu-btn" id="sapaPtspBtnSelectPengaduan">
                <span class="sapa-ptsp-menu-icon">⚠️</span>
                <div>
                    <span class="sapa-ptsp-menu-title">Laporan Pengaduan</span>
                    <span class="sapa-ptsp-menu-desc">Sampaikan pengaduan layanan atau perilaku petugas</span>
                </div>
            </button>
        `;
        screenContainer.querySelector('#sapaPtspBtnSelectKonsultasi').addEventListener('click', renderKonsultasiForm);
        screenContainer.querySelector('#sapaPtspBtnSelectPengaduan').addEventListener('click', renderPengaduanForm);
    }

    function renderKonsultasiForm() {
        const pekerjaanOptions = daftarPekerjaan.map(item => `<option value="${item}">${item}</option>`).join('');

        screenContainer.innerHTML = `
            <button type="button" class="sapa-ptsp-btn-back" id="sapaPtspBtnBackKonsultasi">← Kembali ke Pilihan Menu</button>
            <form id="sapaPtspFormKonsultasiInner">
                <div class="sapa-ptsp-row">
                    <div class="sapa-ptsp-col sapa-ptsp-form-group">
                        <label>Jenis Layanan *</label>
                        <select class="sapa-ptsp-select" id="ptsp_jenis_layanan" required>
                            <option value="pesan" selected>WhatsApp</option>
                            <option value="telepon">Telepon (WA)</option>
                        </select>
                    </div>
                    <div class="sapa-ptsp-col sapa-ptsp-form-group">
                        <label>Jenis Kelamin *</label>
                        <select class="sapa-ptsp-select" id="ptsp_jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="sapa-ptsp-form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" class="sapa-ptsp-input" id="ptsp_nama_responden" placeholder="Nama Anda" required />
                </div>

                <div class="sapa-ptsp-form-group">
                    <label>No. HP / WhatsApp *</label>
                    <input type="text" class="sapa-ptsp-input sapa-ptsp-input-hp" id="ptsp_no_hp" placeholder="08..." maxlength="15" required />
                </div>

                <div class="sapa-ptsp-row">
                    <div class="sapa-ptsp-col sapa-ptsp-form-group">
                        <label>Pekerjaan *</label>
                        <select class="sapa-ptsp-select" id="ptsp_pekerjaan" required>
                            <option value="">-- Pilih --</option>
                            ${pekerjaanOptions}
                        </select>
                    </div>
                    <div class="sapa-ptsp-col sapa-ptsp-form-group">
                        <label>Pendidikan *</label>
                        <select class="sapa-ptsp-select" id="ptsp_pendidikan" required>
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

                <div class="sapa-ptsp-form-group">
                    <label>Keperluan / Pertanyaan Anda *</label>
                    <textarea class="sapa-ptsp-textarea" id="ptsp_keperluan" rows="2" placeholder="Tuliskan keperluan/pertanyaan Anda..." required></textarea>
                </div>

                <div id="ptsp_konsultasi_error" class="sapa-ptsp-error-text"></div>

                <button type="submit" class="sapa-ptsp-btn-submit" id="sapaPtspSubmitKonsultasiBtn">Ajukan Pertanyaan</button>
            </form>
        `;

        screenContainer.querySelector('#sapaPtspBtnBackKonsultasi').addEventListener('click', renderMenuScreen);

        const hpInput = screenContainer.querySelector('#ptsp_no_hp');
        hpInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        const form = screenContainer.querySelector('#sapaPtspFormKonsultasiInner');
        const errorBox = screenContainer.querySelector('#ptsp_konsultasi_error');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            errorBox.style.display = 'none';

            // Semakan Validasi Input Kosong (Trim Space)
            const jenisLayanan = screenContainer.querySelector('#ptsp_jenis_layanan').value.trim();
            const jenisKelamin = screenContainer.querySelector('#ptsp_jenis_kelamin').value.trim();
            const namaResponden = screenContainer.querySelector('#ptsp_nama_responden').value.trim();
            const noHpVal = hpInput.value.trim();
            const pekerjaan = screenContainer.querySelector('#ptsp_pekerjaan').value.trim();
            const pendidikan = screenContainer.querySelector('#ptsp_pendidikan').value.trim();
            const keperluan = screenContainer.querySelector('#ptsp_keperluan').value.trim();

            if (!jenisLayanan || !jenisKelamin || !namaResponden || !noHpVal || !pekerjaan || !pendidikan || !keperluan) {
                errorBox.innerText = '⚠️ Silakan lengkapi semua kolom yang wajib diisi!';
                errorBox.style.display = 'block';
                return;
            }

            if (!noHpVal.startsWith('08') || noHpVal.length < 10) {
                errorBox.innerText = '⚠️ Nomor HP/WhatsApp tidak valid (minimal 10 digit dan diawali 08).';
                errorBox.style.display = 'block';
                hpInput.focus();
                return;
            }

            const submitBtn = screenContainer.querySelector('#sapaPtspSubmitKonsultasiBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Menganalisis & mencari jawaban...';

            const payload = {
                satker_id: satkerId,
                jenis_layanan: jenisLayanan,
                jenis_kelamin: jenisKelamin,
                nama_responden: namaResponden,
                no_hp: noHpVal,
                pekerjaan: pekerjaan,
                pendidikan: pendidikan,
                keperluan: keperluan
            };

            storeKonsultasi(payload)
                .then(res => {
                    if (res.status === 'success') {
                        setTimeout(() => {
                            renderSuggestionsResult(res, payload);
                        }, 400);
                    }
                })
                .catch(err => {
                    console.error('Submit Konsultasi Error:', err);
                    errorBox.innerText = '⚠️ Gagal mengirim data. Silakan coba lagi.';
                    errorBox.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Ajukan Pertanyaan';
                });
        });
    }

    function renderSuggestionsResult(resData, payload) {
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

        screenContainer.innerHTML = `
            <button type="button" class="sapa-ptsp-btn-back" id="sapaPtspBtnBackToMenu">← Kembali ke Pilihan Menu</button>
            <div style="padding: 4px;">
                ${resultHtml}
                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 12px 0;" />
                <p style="font-size: 11px; color: #64748b; text-align: center; margin-bottom: 8px;">
                    ${hasSuggestions ? 'Belum menemukan jawaban? Hubungi petugas kami:' : 'Hubungi petugas kami sekarang:'}
                </p>
                <button id="sapaPtspBtnLanjutWa" class="sapa-ptsp-btn-submit" style="width: 100%; border: none;">
                    💬 Hubungi Petugas via WhatsApp
                </button>
            </div>
        `;

        screenContainer.querySelector('#sapaPtspBtnBackToMenu').addEventListener('click', renderMenuScreen);
        screenContainer.querySelector('#sapaPtspBtnLanjutWa').addEventListener('click', () => {
            if (payload.jenis_layanan === 'telepon' && resData.phone_number) {
                window.location.href = `tel:${resData.phone_number}`;
            } else if (resData.redirect_url) {
                window.open(resData.redirect_url, '_blank');
            }
        });
    }

    function renderPengaduanForm() {
        screenContainer.innerHTML = `
            <button type="button" class="sapa-ptsp-btn-back" id="sapaPtspBtnBackPengaduan">← Kembali ke Pilihan Menu</button>
            <form id="sapaPtspFormPengaduanInner">
                <div class="sapa-ptsp-form-group">
                    <label>Nama Pelapor *</label>
                    <input type="text" class="sapa-ptsp-input" id="ptsp_p_nama" placeholder="Nama Lengkap Anda" required />
                </div>

                <div class="sapa-ptsp-row">
                    <div class="sapa-ptsp-col sapa-ptsp-form-group">
                        <label>No. HP / WhatsApp *</label>
                        <input type="text" class="sapa-ptsp-input sapa-ptsp-input-hp" id="ptsp_p_hp" placeholder="08..." maxlength="15" required />
                    </div>
                    <div class="sapa-ptsp-col sapa-ptsp-form-group">
                        <label>NIK / No. KTP (Opsional)</label>
                        <input type="text" class="sapa-ptsp-input" id="ptsp_p_nik" placeholder="16 Digit NIK" maxlength="16" />
                    </div>
                </div>

                <div class="sapa-ptsp-form-group">
                    <label>Uraian Pengaduan *</label>
                    <textarea class="sapa-ptsp-textarea" id="ptsp_p_uraian" rows="3" placeholder="Tuliskan detail pengaduan Anda secara ringkas dan jelas..." required></textarea>
                </div>

                <div id="ptsp_pengaduan_error" class="sapa-ptsp-error-text"></div>

                <button type="submit" class="sapa-ptsp-btn-submit sapa-ptsp-btn-danger" id="sapaPtspSubmitPengaduanBtn">Lanjutkan ke Petugas</button>
            </form>
        `;

        screenContainer.querySelector('#sapaPtspBtnBackPengaduan').addEventListener('click', renderMenuScreen);

        screenContainer.querySelectorAll('.sapa-ptsp-input-hp, #ptsp_p_nik').forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });

        const form = screenContainer.querySelector('#sapaPtspFormPengaduanInner');
        const errorBox = screenContainer.querySelector('#ptsp_pengaduan_error');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            errorBox.style.display = 'none';

            const namaPelapor = screenContainer.querySelector('#ptsp_p_nama').value.trim();
            const hpInput = screenContainer.querySelector('#ptsp_p_hp');
            const noHpVal = hpInput.value.trim();
            const nikVal = screenContainer.querySelector('#ptsp_p_nik').value.trim();
            const uraianPengaduan = screenContainer.querySelector('#ptsp_p_uraian').value.trim();

            if (!namaPelapor || !noHpVal || !uraianPengaduan) {
                errorBox.innerText = '⚠️ Silakan lengkapi semua kolom yang wajib diisi!';
                errorBox.style.display = 'block';
                return;
            }

            if (!noHpVal.startsWith('08') || noHpVal.length < 10) {
                errorBox.innerText = '⚠️ Nomor HP/WhatsApp tidak valid (minimal 10 digit dan diawali 08).';
                errorBox.style.display = 'block';
                hpInput.focus();
                return;
            }

            const submitBtn = screenContainer.querySelector('#sapaPtspSubmitPengaduanBtn');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Menyimpan Data...';

            const payload = {
                satker_id: satkerId,
                nama_pelapor: namaPelapor,
                no_hp: noHpVal,
                nik: nikVal || null,
                uaraian_pengaduan: uraianPengaduan
            };

            storePengaduan(payload)
                .then(res => {
                    if (res.status === 'success') {
                        if (res.redirect_url) window.open(res.redirect_url, '_blank');
                        modal.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Submit Pengaduan Error:', err);
                    errorBox.innerText = '⚠️ Gagal mengirim pengaduan. Silakan coba lagi.';
                    errorBox.style.display = 'block';
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Lanjutkan ke Petugas';
                });
        });
    }

    // 8. INISIALISASI WIDGET
    fetchInitData()
        .then(res => {
            if (res.status === 'success') {
                isDomainValid = true;
                shadowRoot.getElementById('sapaPtspHeaderTitle').innerText = `🏛️ Layanan SAPA\n${res.satker_name}`;
                bubble.style.display = 'flex';
            }
        })
        .catch(err => {
            isDomainValid = false;
            bubble.style.display = 'none';
            modal.style.display = 'none';
            console.error('SAPA Widget Error:', err.message);
        });

    // Toggle Modal
    bubble.addEventListener('click', () => {
        if (!isDomainValid) return;

        if (modal.style.display !== 'block') {
            modal.style.display = 'block';
            if (!isJamKerjaWIB()) {
                renderNoticeScreen();
            } else {
                renderMenuScreen();
            }
        } else {
            modal.style.display = 'none';
        }
    });
})();