(function () {
    const currentScript = document.currentScript;
    const satkerId = currentScript.getAttribute('data-satker-id');
    const serverUrl = currentScript.getAttribute('data-server-url') || 'http://sapa.test';

    if (!satkerId) {
        console.error('PTSP Widget Error: Atribut "data-satker-id" belum diisi.');
        return;
    }

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

    // Cek Jam Kerja (WIB / GMT+7)
    function isJamKerjaWIB() {
        const now = new Date();
        const utcHours = now.getUTCHours();
        const wibHours = (utcHours + 7) % 24;
        const wibMinutes = now.getUTCMinutes();
        const day = now.getUTCDay(); // 0 = Minggu, 1 = Senin, ..., 5 = Jumat, 6 = Sabtu

        const totalMenit = wibHours * 60 + wibMinutes;
        const jamMulai = 8 * 60; // 08:00 WIB

        if (day === 0 || day === 6) return false;

        // Senin - Kamis (08:00 - 16:30)
        if (day >= 1 && day <= 4) {
            const jamSelesaiKamis = 16 * 60 + 30;
            return totalMenit >= jamMulai && totalMenit <= jamSelesaiKamis;
        }

        // Jumat (08:00 - 17:00)
        if (day === 5) {
            const jamSelesaiJumat = 17 * 60;
            return totalMenit >= jamMulai && totalMenit <= jamSelesaiJumat;
        }

        return false;
    }

    // 1. Inject CSS
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
        
        /* Tombol Pilihan Utama */
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

    // 2. Inject HTML Modal
    const container = document.createElement('div');
    container.innerHTML = `
        <div class="ptsp-modal" id="ptspModal">
            <div class="ptsp-header" id="ptspHeaderTitle">🏛️ Layanan SAPA</div>
            
            <!-- 1. Tampilan Peringatan Luar Jam Kerja -->
            <div class="ptsp-body" id="ptspNoticeScreen" style="display: none;">
                <div class="ptsp-notice-box">
                    <strong>ℹ️ Informasi Jam Layanan</strong><br><br>
                    Saat ini layanan sedang berada di <strong>luar jam operasional</strong>.<br>
                    <small style="color: #666; display:block; margin: 6px 0;">(Senin–Kamis: 08.00–16.30 WIB | Jumat: 08.00–17.00 WIB)</small>
                    Pesan Anda akan kami respon setelah jam kerja dimulai.
                </div>
                <button type="button" class="ptsp-btn-next" id="ptspBtnContinue">Tetap Lanjutkan</button>
            </div>

            <!-- 2. Screen Pilihan Menu Utama -->
            <div class="ptsp-body" id="ptspMenuScreen" style="display: none;">
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
            </div>

            <!-- 3. Form Konsultasi / Informasi -->
            <form class="ptsp-body" id="ptspFormKonsultasi" style="display: none;">
                <button type="button" class="ptsp-btn-back ptspBtnGoMenu">← Kembali ke Pilihan Menu</button>
                
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
                        <select class="ptsp-select ptsp-pekerjaan-select" id="ptsp_pekerjaan">
                            <option value="">-- Pilih --</option>
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
                    <label>Keperluan / Informasi yang Dibutuhkan *</label>
                    <textarea class="ptsp-textarea" id="ptsp_keperluan" rows="2" placeholder="Tuliskan keperluan Anda..." required></textarea>
                </div>

                <button type="submit" class="ptsp-btn-submit" id="ptspSubmitKonsultasiBtn">Lanjutkan ke Petugas</button>
            </form>

            <!-- 4. Form Pengaduan -->
            <form class="ptsp-body" id="ptspFormPengaduan" style="display: none;">
                <button type="button" class="ptsp-btn-back ptspBtnGoMenu">← Kembali ke Pilihan Menu</button>
                
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
        </div>

        <div class="ptsp-bubble" id="ptspBubble" style="display: none;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.82.46 3.53 1.27 5L2 22l5.18-1.24C8.61 21.55 10.26 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
        </div>
    `;
    document.body.appendChild(container);

    // Populate Select Pekerjaan
    const pekerjaanSelect = document.getElementById('ptsp_pekerjaan');
    daftarPekerjaan.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item;
        opt.textContent = item;
        pekerjaanSelect.appendChild(opt);
    });

    // Element References
    const bubble = document.getElementById('ptspBubble');
    const modal = document.getElementById('ptspModal');
    const noticeScreen = document.getElementById('ptspNoticeScreen');
    const menuScreen = document.getElementById('ptspMenuScreen');
    const formKonsultasi = document.getElementById('ptspFormKonsultasi');
    const formPengaduan = document.getElementById('ptspFormPengaduan');
    
    let isDomainValid = false;

    // Helper Pindah Screen
    function showScreen(screenEl) {
        noticeScreen.style.display = 'none';
        menuScreen.style.display = 'none';
        formKonsultasi.style.display = 'none';
        formPengaduan.style.display = 'none';

        screenEl.style.display = 'block';
    }

    // Init Data & Domain Check
    fetch(`${serverUrl}/api/ptsp/init-data?satker_id=${satkerId}`)
        .then(async res => {
            const data = await res.json();
            if (!res.ok || data.status === 'error') {
                throw new Error(data.message || 'Akses Widget Ditolak.');
            }
            return data;
        })
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

    // Toggle Floating Bubble
    bubble.addEventListener('click', () => {
        if (!isDomainValid) return;

        if (modal.style.display !== 'block') {
            modal.style.display = 'block';
            
            if (!isJamKerjaWIB()) {
                showScreen(noticeScreen);
            } else {
                showScreen(menuScreen);
            }
        } else {
            modal.style.display = 'none';
        }
    });

    // Event "Tetap Lanjutkan"
    document.getElementById('ptspBtnContinue').addEventListener('click', () => {
        showScreen(menuScreen);
    });

    // Event Menu Selection
    document.getElementById('ptspBtnSelectKonsultasi').addEventListener('click', () => {
        showScreen(formKonsultasi);
    });

    document.getElementById('ptspBtnSelectPengaduan').addEventListener('click', () => {
        showScreen(formPengaduan);
    });

    // Event Back to Menu
    document.querySelectorAll('.ptspBtnGoMenu').forEach(btn => {
        btn.addEventListener('click', () => {
            showScreen(menuScreen);
        });
    });

    // Sanitasi Input Hanya Angka
    document.querySelectorAll('.ptsp-input-hp, #ptsp_p_nik').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // --- SUBMIT KONSULTASI ---
    formKonsultasi.addEventListener('submit', function (e) {
        e.preventDefault();
        
        if (!isDomainValid) return;

        const hpInput = document.getElementById('ptsp_no_hp');
        const noHpVal = hpInput.value;

        if (!noHpVal.startsWith('08') || noHpVal.length < 10) {
            hpInput.focus();
            return;
        }

        const submitBtn = document.getElementById('ptspSubmitKonsultasiBtn');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Menyimpan Data...';

        const payload = {
            satker_id: satkerId,
            jenis_layanan: document.getElementById('ptsp_jenis_layanan').value,
            jenis_kelamin: document.getElementById('ptsp_jenis_kelamin').value,
            nama_responden: document.getElementById('ptsp_nama_responden').value,
            no_hp: noHpVal,
            pekerjaan: document.getElementById('ptsp_pekerjaan').value || null,
            pendidikan: document.getElementById('ptsp_pendidikan').value || null,
            keperluan: document.getElementById('ptsp_keperluan').value
        };

        fetch(`${serverUrl}/api/ptsp/store-pengunjung`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal menyimpan data.');
            return data;
        })
        .then(res => {
            if (res.status === 'success') {
                if (payload.jenis_layanan === 'telepon' && res.phone_number) {
                    window.location.href = `tel:${res.phone_number}`;
                } else if (res.redirect_url) {
                    window.open(res.redirect_url, '_blank');
                }
                modal.style.display = 'none';
                formKonsultasi.reset();
            }
        })
        .catch(err => console.error('Submit Konsultasi Error:', err))
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Lanjutkan ke Petugas';
        });
    });

    // --- SUBMIT PENGADUAN ---
    formPengaduan.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!isDomainValid) return;

        const hpInput = document.getElementById('ptsp_p_hp');
        const noHpVal = hpInput.value;

        if (!noHpVal.startsWith('08') || noHpVal.length < 10) {
            hpInput.focus();
            return;
        }

        const submitBtn = document.getElementById('ptspSubmitPengaduanBtn');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Menyimpan Data...';

        const payload = {
            satker_id: satkerId,
            nama_pelapor: document.getElementById('ptsp_p_nama').value,
            no_hp: noHpVal,
            nik: document.getElementById('ptsp_p_nik').value || null,
            uaraian_pengaduan: document.getElementById('ptsp_p_uraian').value
        };

        fetch(`${serverUrl}/api/ptsp/store-pengaduan`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal mengirim pengaduan.');
            return data;
        })
        .then(res => {
            if (res.status === 'success') {
                // Direct Redirect ke WhatsApp tanpa alert
                if (res.redirect_url) {
                    window.open(res.redirect_url, '_blank');
                }
                modal.style.display = 'none';
                formPengaduan.reset();
            }
        })
        .catch(err => console.error('Submit Pengaduan Error:', err))
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Lanjutkan ke Petugas';
        });
    });
})();