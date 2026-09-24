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

    // 1. Inject CSS Style
    const style = document.createElement('style');
    style.innerHTML = `
        /* Container Bubble Wrapper untuk Tooltip Hover */
        .ptsp-bubble-container {
            position: fixed; bottom: 20px; right: 20px; z-index: 999999;
            display: none; align-items: center; gap: 10px;
        }

        /* Tooltip Teks Hover */
        .ptsp-tooltip {
            background-color: #333333; color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15); opacity: 0; visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
            white-space: nowrap; pointer-events: none;
        }

        .ptsp-bubble-container:hover .ptsp-tooltip {
            opacity: 1; visibility: visible;
        }

        /* Bubble Button */
        .ptsp-bubble {
            width: 60px; height: 60px; background-color: #25D366;
            border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: transform 0.2s ease, background-color 0.2s;
        }
        .ptsp-bubble:hover { transform: scale(1.08); background-color: #20ba5a; }

        /* Modal Box */
        .ptsp-modal {
            position: fixed; bottom: 20px; right: 20px; z-index: 999999;
            width: 360px; max-width: 90vw; background: #ffffff; border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18); display: none; overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Header Modal dengan Tombol Close */
        .ptsp-header {
            background: #006633; color: #ffffff; padding: 14px 16px;
            font-weight: 600; font-size: 14px; text-align: center; white-space: pre-line;
            position: relative;
        }

        .ptsp-btn-close {
            position: absolute; top: 10px; right: 12px;
            background: transparent; border: none; color: #ffffff;
            font-size: 20px; font-weight: bold; cursor: pointer; line-height: 1;
            opacity: 0.8; transition: opacity 0.2s;
        }
        .ptsp-btn-close:hover { opacity: 1; }

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

    // 2. Inject HTML Modal, Bubble & Tooltip Hover
    const container = document.createElement('div');
    container.innerHTML = `
        <div class="ptsp-modal" id="ptspModal">
            <div class="ptsp-header" id="ptspHeaderTitle">
                🏛️ Layanan SAPA
                <button type="button" class="ptsp-btn-close" id="ptspBtnClose" title="Tutup">&times;</button>
            </div>
            <form class="ptsp-body" id="ptspForm">
                <div class="ptsp-row">
                    <div class="ptsp-col ptsp-form-group">
                        <label>Jenis Layanan *</label>
                        <select class="ptsp-select" id="ptsp_jenis_layanan" required>
                            <option value="pesan">WhatsApp</option>
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

                <div class="ptsp-row">
                    <div class="ptsp-col ptsp-form-group">
                        <label>No. HP / WhatsApp *</label>
                        <input type="text" class="ptsp-input" id="ptsp_no_hp" placeholder="0812..." required />
                    </div>
                    <div class="ptsp-col ptsp-form-group">
                        <label>NIK (16 Digit) *</label>
                        <input type="text" class="ptsp-input" id="ptsp_nik" minlength="16" maxlength="16" pattern="[0-9]{16}" placeholder="16 digit NIK" title="NIK harus berupa 16 digit angka" required />
                    </div>
                </div>

                <div class="ptsp-row">
                    <div class="ptsp-col ptsp-form-group">
                        <label>Pekerjaan</label>
                        <select class="ptsp-select" id="ptsp_pekerjaan">
                            <option value="">-- Pilih Pekerjaan --</option>
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

                <button type="submit" class="ptsp-btn-submit" id="ptspSubmitBtn">Lanjutkan ke Petugas</button>
            </form>
        </div>

        <!-- Container Bubble & Tooltip Hover -->
        <div class="ptsp-bubble-container" id="ptspBubbleContainer">
            <div class="ptsp-tooltip">Layanan Whatsapp</div>
            <div class="ptsp-bubble" id="ptspBubble">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.82.46 3.53 1.27 5L2 22l5.18-1.24C8.61 21.55 10.26 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
            </div>
        </div>
    `;
    document.body.appendChild(container);

    const pekerjaanSelect = document.getElementById('ptsp_pekerjaan');
    daftarPekerjaan.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item;
        opt.textContent = item;
        pekerjaanSelect.appendChild(opt);
    });

    const bubbleContainer = document.getElementById('ptspBubbleContainer');
    const bubble = document.getElementById('ptspBubble');
    const modal = document.getElementById('ptspModal');
    const btnClose = document.getElementById('ptspBtnClose');
    let isDomainValid = false;

    // 3. Validasi Domain & Inisialisasi Saat Halaman Dimuat
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
                document.getElementById('ptspHeaderTitle').innerHTML = `🏛️ Layanan SAPA\n${res.satker_name} <button type="button" class="ptsp-btn-close" id="ptspBtnClose">&times;</button>`;
                
                // Binding ulang event close button setelah innerHTML ter-update
                document.getElementById('ptspBtnClose').addEventListener('click', closeModal);
                
                // Tampilkan container bubble
                bubbleContainer.style.display = 'flex';
            }
        })
        .catch(err => {
            isDomainValid = false;
            bubbleContainer.style.display = 'none';
            modal.style.display = 'none';
            alert('⚠️ PTSP Widget Error: ' + err.message);
        });

    // Fungsi Buka Modal (Sembunyikan Bubble)
    function openModal() {
        if (!isDomainValid) return;
        modal.style.display = 'block';
        bubbleContainer.style.display = 'none'; // Sembunyikan bubble saat modal terbuka
    }

    // Fungsi Tutup Modal (Munculkan Bubble Kembali)
    function closeModal() {
        modal.style.display = 'none';
        bubbleContainer.style.display = 'flex'; // Tampilkan bubble kembali saat modal ditutup
    }

    // Event Listeners
    bubble.addEventListener('click', openModal);
    if (btnClose) btnClose.addEventListener('click', closeModal);

    document.getElementById('ptsp_nik').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Submit Handler
    document.getElementById('ptspForm').addEventListener('submit', function (e) {
        e.preventDefault();
        
        if (!isDomainValid) {
            alert('⚠️ Akses ditolak. Domain tidak valid.');
            return;
        }

        const nikVal = document.getElementById('ptsp_nik').value;
        if (nikVal.length !== 16) {
            alert('⚠️ NIK harus diisi tepat 16 digit angka.');
            return;
        }

        const submitBtn = document.getElementById('ptspSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Menyimpan Data...';

        const payload = {
            satker_id: satkerId,
            jenis_layanan: document.getElementById('ptsp_jenis_layanan').value,
            jenis_kelamin: document.getElementById('ptsp_jenis_kelamin').value,
            nama_responden: document.getElementById('ptsp_nama_responden').value,
            no_hp: document.getElementById('ptsp_no_hp').value,
            nik: nikVal,
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
            if (!res.ok || data.status === 'error') {
                throw new Error(data.message || 'Gagal menyimpan data.');
            }
            return data;
        })
        .then(res => {
            if (res.status === 'success') {
                if (payload.jenis_layanan === 'telepon' && res.phone_number) {
                    window.location.href = `tel:${res.phone_number}`;
                } else if (res.redirect_url) {
                    window.open(res.redirect_url, '_blank');
                }
                closeModal();
                document.getElementById('ptspForm').reset();
            }
        })
        .catch(err => {
            alert('⚠️ Gagal: ' + err.message);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Lanjutkan ke Petugas';
        });
    });
})();