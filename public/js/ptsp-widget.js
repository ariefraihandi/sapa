(function () {
    const currentScript = document.currentScript;
    const satkerId = currentScript.getAttribute('data-satker-id');
    const serverUrl = currentScript.getAttribute('data-server-url') || 'https://sapa.ms-aceh.go.id';

    if (!satkerId) {
        console.error('PTSP Widget Error: Atribut "data-satker-id" belum diisi.');
        return;
    }

    const currentOrigin = window.location.origin;

    // 1. Inject CSS Style
    const style = document.createElement('style');
    style.textContent = `
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
            width: 360px; max-width: 90vw; background: #ffffff; border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18); display: none; overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .ptsp-header { background: #006633; color: #ffffff; padding: 14px 16px; font-weight: 600; font-size: 14px; text-align: center; }
        .ptsp-body { padding: 16px; max-height: 80vh; overflow-y: auto; }
        .ptsp-form-group { margin-bottom: 12px; }
        .ptsp-form-group label { display: block; font-size: 12px; font-weight: 600; color: #333; margin-bottom: 4px; }
        .ptsp-input, .ptsp-select, .ptsp-textarea {
            width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #cccccc;
            border-radius: 6px; box-sizing: border-box; outline: none;
        }
        .ptsp-input:focus, .ptsp-select:focus, .ptsp-textarea:focus { border-color: #006633; }
        .ptsp-row { display: flex; gap: 8px; }
        .ptsp-col { flex: 1; }
        .ptsp-btn-submit {
            width: 100%; background: #25D366; color: white; border: none; padding: 10px;
            border-radius: 6px; font-weight: bold; font-size: 14px; cursor: pointer; margin-top: 6px;
        }
        .ptsp-btn-submit:hover { background: #20ba5a; }
    `;
    document.head.appendChild(style);

    // 2. Inject HTML Modal & Bubble
    const container = document.createElement('div');
    container.innerHTML = `
        <div class="ptsp-modal" id="ptspModal">
            <div class="ptsp-header" id="ptspHeaderTitle">🏛️ Layanan SAPA</div>
            <form class="ptsp-body" id="ptspForm">
                <div class="ptsp-form-group">
                    <label>Jenis Akses Layanan *</label>
                    <select class="ptsp-select" id="jenis_layanan" required>
                        <option value="pesan">WhatsApp Chat / Pesan</option>
                        <option value="telepon">Telepon Direct</option>
                    </select>
                </div>
                <div class="ptsp-row">
                    <div class="ptsp-col ptsp-form-group" style="flex: 2;">
                        <label>Nama Lengkap *</label>
                        <input type="text" class="ptsp-input" id="nama_responden" maxlength="100" placeholder="Nama Anda" required />
                    </div>
                    <div class="ptsp-col ptsp-form-group" style="flex: 1;">
                        <label>Gender *</label>
                        <select class="ptsp-select" id="jenis_kelamin" required>
                            <option value="">Pilih</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="ptsp-row">
                    <div class="ptsp-col ptsp-form-group">
                        <label>No. HP / WhatsApp *</label>
                        <input type="text" class="ptsp-input" id="no_hp" maxlength="20" placeholder="0812..." required />
                    </div>
                    <div class="ptsp-col ptsp-form-group">
                        <label>NIK (16 Digit) *</label>
                        <input type="text" class="ptsp-input" id="nik" minlength="16" maxlength="16" pattern="[0-9]{16}" placeholder="16 digit NIK" title="NIK harus berupa 16 digit angka" required />
                    </div>
                </div>
                <div class="ptsp-row">
                    <div class="ptsp-col ptsp-form-group">
                        <label>Pekerjaan</label>
                        <select class="ptsp-select" id="pekerjaan">
                            <option value="">-- Loading --</option>
                        </select>
                    </div>
                    <div class="ptsp-col ptsp-form-group">
                        <label>Pendidikan</label>
                        <select class="ptsp-select" id="pendidikan">
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
                    <textarea class="ptsp-textarea" id="keperluan" rows="2" maxlength="500" placeholder="Tuliskan keperluan Anda..." required></textarea>
                </div>
                <!-- Honeypot Field -->
                <input type="text" id="ptsp_hp_check" style="display:none !important;" tabindex="-1" autocomplete="off" />
                
                <button type="submit" class="ptsp-btn-submit" id="ptspSubmitBtn">Lanjutkan ke Petugas</button>
            </form>
        </div>
        <div class="ptsp-bubble" id="ptspBubble">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.82.46 3.53 1.27 5L2 22l5.18-1.24C8.61 21.55 10.26 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
        </div>
    `;
    document.body.appendChild(container);

    let isInitialized = false;

    function isSafeUrl(url) {
        try {
            const parsed = new URL(url);
            const allowedHosts = ['wa.me', 'api.whatsapp.com', 'web.whatsapp.com'];
            return allowedHosts.some(host => parsed.hostname === host || parsed.hostname.endsWith('.' + host));
        } catch (e) {
            return false;
        }
    }

    // 3. Event Toggle Modal & Load Init Data
    const bubble = document.getElementById('ptspBubble');
    const modal = document.getElementById('ptspModal');

    bubble.addEventListener('click', () => {
        const isOpening = (modal.style.display !== 'block');
        modal.style.display = isOpening ? 'block' : 'none';

        if (isOpening && !isInitialized) {
            const initUrl = `${serverUrl}/api/ptsp/init-data?satker_id=${encodeURIComponent(satkerId)}&client_domain=${encodeURIComponent(currentOrigin)}`;

            fetch(initUrl)
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        document.getElementById('ptspHeaderTitle').textContent = `🏛️ Layanan SAPA\n${res.satker_name || ''}`;

                        const pekerjaanSelect = document.getElementById('pekerjaan');
                        pekerjaanSelect.textContent = '';

                        const defaultOpt = document.createElement('option');
                        defaultOpt.value = '';
                        defaultOpt.textContent = '-- Pilih Pekerjaan --';
                        pekerjaanSelect.appendChild(defaultOpt);

                        if (Array.isArray(res.pekerjaan_list) && res.pekerjaan_list.length > 0) {
                            res.pekerjaan_list.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item;
                                opt.textContent = item;
                                pekerjaanSelect.appendChild(opt);
                            });
                        }
                        isInitialized = true;
                    } else {
                        alert('⚠️ Akses Layanan Ditolak: ' + (res.message || 'Domain tidak valid.'));
                        modal.style.display = 'none';
                    }
                })
                .catch(err => console.error('Gagal mengambil data awal widget:', err));
        }
    });

    // Validasi Input NIK
    document.getElementById('nik').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // 4. Submit Handler
    document.getElementById('ptspForm').addEventListener('submit', function (e) {
        e.preventDefault();

        if (document.getElementById('ptsp_hp_check').value !== '') {
            return;
        }

        const nikVal = document.getElementById('nik').value;
        if (nikVal.length !== 16) {
            alert('⚠️ NIK harus diisi tepat 16 digit angka.');
            return;
        }

        const submitBtn = document.getElementById('ptspSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan Data...';

        const payload = {
            satker_id: satkerId,
            client_domain: currentOrigin,
            jenis_layanan: document.getElementById('jenis_layanan').value,
            nama_responden: document.getElementById('nama_responden').value,
            jenis_kelamin: document.getElementById('jenis_kelamin').value,
            no_hp: document.getElementById('no_hp').value,
            nik: nikVal,
            pekerjaan: document.getElementById('pekerjaan').value || null,
            pendidikan: document.getElementById('pendidikan').value || null,
            keperluan: document.getElementById('keperluan').value
        };

        fetch(`${serverUrl}/api/ptsp/store-pengunjung`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (payload.jenis_layanan === 'telepon' && res.phone_number) {
                    const safePhone = String(res.phone_number).replace(/[^0-9+]/g, '');
                    window.location.href = `tel:${safePhone}`;
                } else if (res.redirect_url) {
                    if (isSafeUrl(res.redirect_url)) {
                        window.open(res.redirect_url, '_blank');
                    } else {
                        alert('⚠️ Akses ditolak: URL tujuan tidak aman.');
                    }
                }
                modal.style.display = 'none';
                document.getElementById('ptspForm').reset();
            } else {
                alert('⚠️ Gagal menyimpan data: ' + (res.message || 'Periksa kembali isian Anda.'));
            }
        })
        .catch(err => {
            alert('⚠️ Gagal memproses permintaan.');
            console.error(err);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Lanjutkan ke Petugas';
        });
    });
})();