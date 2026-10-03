(function () {
    // 1. Dapatkan Script Element & Atribut Satker
    const scriptEl = document.currentScript || document.querySelector('script[data-satker-id]');
    if (!scriptEl) {
        console.error('PTSP Widget Error: Script tag tidak ditemukan.');
        return;
    }

    const satkerId = scriptEl.getAttribute('data-satker-id');
    const serverUrl = scriptEl.getAttribute('data-server-url') || 'http://sapa.test';

    if (!satkerId) {
        console.error('PTSP Widget Error: Atribut "data-satker-id" belum diisi.');
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

    // 4. INJECT CSS STYLES
    const style = document.createElement('style');
    style.innerHTML = `
        .ptsp-bubble {
            position: fixed; bottom: 20px; right: 20px; z-index: 999999;
            width: 60px; height: 60px; background-color: #25D366;
            border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: transform 0.2s;
        }
        .ptsp-bubble:hover { transform: scale(1.08); }
        .ptsp-modal {
            position: fixed; bottom: 90px; right: 20px; z-index: 999999;
            width: 380px; max-width: 90vw; background: #ffffff; border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18); display: none; overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .ptsp-header { background: #006633; color: #ffffff; padding: 14px 16px; font-weight: 600; font-size: 14px; text-align: center; white-space: pre-line; }
        .ptsp-body { padding: 16px; max-height: 75vh; overflow-y: auto; }
        .ptsp-form-group { margin-bottom: 12px; }
        .ptsp-form-group label { display: block; font-size: 12px; font-weight: 600; color: #333; margin-bottom: 4px; }
        .ptsp-input, .ptsp-select, .ptsp-textarea {
            width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #cccccc;
            border-radius: 6px; box-sizing: border-box; outline: none;
        }
        .ptsp-input:focus, .ptsp-select:focus, .ptsp-textarea:focus { border-color: #006633; }
        .ptsp-row { display: flex; gap: 8px; }
        .ptsp-col { flex: 1; }
        
        .ptsp-menu-btn {
            width: 100%; padding: 14px; margin-bottom: 10px; border: 1px solid #e2e8f0;
            border-radius: 8px; background: #f8fafc; text-align: left; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; gap: 12px;
        }
        .ptsp-menu-btn:hover { background: #f1f5f9; border-color: #006633; }
        .ptsp-menu-icon { font-size: 20px; width: 32px; text-align: center; }
        .ptsp-menu-title { font-weight: bold; font-size: 13px; color: #1e293b; display: block; }
        .ptsp-menu-desc { font-size: 11px; color: #64748b; display: block; margin-top: 2px; }

        .ptsp-btn-submit, .ptsp-btn-next {
            width: 100%; background: #25D366; color: white; border: none; padding: 10px;
            border-radius: 6px; font-weight: bold; font-size: 14px; cursor: pointer; margin-top: 6px;
        }
        .ptsp-btn-submit:hover, .ptsp-btn-next:hover { background: #20ba5a; }
        .ptsp-btn-danger { background: #d97706; }
        .ptsp-btn-danger:hover { background: #b45309; }
        
        .ptsp-btn-back {
            background: none; border: none; color: #64748b; font-size: 12px; cursor: pointer;
            padding: 4px 0; margin-bottom: 10px; display: inline-flex; align-items: center; gap: 4px;
        }
        .ptsp-btn-back:hover { color: #006633; text-decoration: underline; }

        .ptsp-notice-box {
            background: #fff3cd; color: #856404; border: 1px solid #ffeeba;
            padding: 12px; border-radius: 8px; font-size: 13px; line-height: 1.5;
            margin-bottom: 14px; text-align: center;
        }
    `;
    document.head.appendChild(style);

    // 5. INJECT MODAL HTML CONTAINER
    const container = document.createElement('div');
    container.innerHTML = `
        <div class="ptsp-modal" id="ptspModal">
            <div class="ptsp-header" id="ptspHeaderTitle">🏛️ Layanan SAPA</div>
            <div class="ptsp-body" id="ptspScreenContainer"></div>
        </div>
        <div class="ptsp-bubble" id="ptspBubble" style="display: none;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.82.46 3.53 1.27 5L2 22l5.18-1.24C8.61 21.55 10.26 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
        </div>
    `;
    document.body.appendChild(container);

    const bubble = document.getElementById('ptspBubble');
    const modal = document.getElementById('ptspModal');
    const screenContainer = document.getElementById('ptspScreenContainer');
    let isDomainValid = false;

    // 6. VIEW RENDERING RENDERERS
    function renderNoticeScreen() {
        screenContainer.innerHTML = `
            <div class="ptsp-notice-box">
                <strong>ℹ️ Informasi Jam Layanan</strong><br><br>
                Saat ini layanan sedang berada di <strong>luar jam operasional</strong>.<br>
                <small style="color: #666; display:block; margin: 6px 0;">(Senin–Kamis: 08.00–16.30 WIB | Jumat: 08.00–17.00 WIB)</small>
                Pesan Anda akan kami respon setelah jam kerja dimulai.
            </div>
            <button type="button" class="ptsp-btn-next" id="ptspBtnContinue">Tetap Lanjutkan</button>
        `;
        screenContainer.querySelector('#ptspBtnContinue').addEventListener('click', renderMenuScreen);
    }

    function renderMenuScreen() {
        screenContainer.innerHTML = `
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
        screenContainer.querySelector('#ptspBtnSelectKonsultasi').addEventListener('click', renderKonsultasiForm);
        screenContainer.querySelector('#ptspBtnSelectPengaduan').addEventListener('click', renderPengaduanForm);
    }

    function renderKonsultasiForm() {
        const pekerjaanOptions = daftarPekerjaan.map(item => `<option value="${item}">${item}</option>`).join('');

        screenContainer.innerHTML = `
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
                    <textarea class="ptsp-textarea" id="ptsp_keperluan" rows="2" placeholder="Tuliskan keperluan/pertanyaan Anda..." required></textarea>
                </div>

                <button type="submit" class="ptsp-btn-submit" id="ptspSubmitKonsultasiBtn">Ajukan Pertanyaan</button>
            </form>
        `;

        screenContainer.querySelector('#ptspBtnBackKonsultasi').addEventListener('click', renderMenuScreen);

        const hpInput = screenContainer.querySelector('#ptsp_no_hp');
        hpInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        const form = screenContainer.querySelector('#ptspFormKonsultasiInner');
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const noHpVal = hpInput.value;
            if (!noHpVal.startsWith('08') || noHpVal.length < 10) {
                hpInput.focus();
                return;
            }

            const submitBtn = screenContainer.querySelector('#ptspSubmitKonsultasiBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Menganalisis & mencari jawaban...';

            const payload = {
                satker_id: satkerId,
                jenis_layanan: screenContainer.querySelector('#ptsp_jenis_layanan').value,
                jenis_kelamin: screenContainer.querySelector('#ptsp_jenis_kelamin').value,
                nama_responden: screenContainer.querySelector('#ptsp_nama_responden').value,
                no_hp: noHpVal,
                pekerjaan: screenContainer.querySelector('#ptsp_pekerjaan').value || null,
                pendidikan: screenContainer.querySelector('#ptsp_pendidikan').value || null,
                keperluan: screenContainer.querySelector('#ptsp_keperluan').value
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

        screenContainer.querySelector('#ptspBtnBackToMenu').addEventListener('click', renderMenuScreen);
        screenContainer.querySelector('#ptspBtnLanjutWa').addEventListener('click', () => {
            if (payload.jenis_layanan === 'telepon' && resData.phone_number) {
                window.location.href = `tel:${resData.phone_number}`;
            } else if (resData.redirect_url) {
                window.open(resData.redirect_url, '_blank');
            }
        });
    }

    function renderPengaduanForm() {
        screenContainer.innerHTML = `
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

        screenContainer.querySelector('#ptspBtnBackPengaduan').addEventListener('click', renderMenuScreen);

        screenContainer.querySelectorAll('.ptsp-input-hp, #ptsp_p_nik').forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });

        const form = screenContainer.querySelector('#ptspFormPengaduanInner');
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const hpInput = screenContainer.querySelector('#ptsp_p_hp');
            const noHpVal = hpInput.value;

            if (!noHpVal.startsWith('08') || noHpVal.length < 10) {
                hpInput.focus();
                return;
            }

            const submitBtn = screenContainer.querySelector('#ptspSubmitPengaduanBtn');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Menyimpan Data...';

            const payload = {
                satker_id: satkerId,
                nama_pelapor: screenContainer.querySelector('#ptsp_p_nama').value,
                no_hp: noHpVal,
                nik: screenContainer.querySelector('#ptsp_p_nik').value || null,
                uaraian_pengaduan: screenContainer.querySelector('#ptsp_p_uraian').value
            };

            storePengaduan(payload)
                .then(res => {
                    if (res.status === 'success') {
                        if (res.redirect_url) window.open(res.redirect_url, '_blank');
                        modal.style.display = 'none';
                    }
                })
                .catch(err => console.error('Submit Pengaduan Error:', err))
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Lanjutkan ke Petugas';
                });
        });
    }

    // 7. INISIALISASI WIDGET
    fetchInitData()
        .then(res => {
            if (res.status === 'success') {
                isDomainValid = true;
                document.getElementById('ptspHeaderTitle').innerText = `🏛️ Layanan SAPA\n${res.satker_name}`;
                bubble.style.display = 'flex';
            }
        })
        .catch(err => {
            isDomainValid = false;
            bubble.style.display = 'none';
            modal.style.display = 'none';
            console.error('PTSP Widget Error:', err.message);
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