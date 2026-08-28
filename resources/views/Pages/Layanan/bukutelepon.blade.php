<!DOCTYPE html>
<html lang="id">

    <!-- INCLUDE HEAD & STYLES -->
    @include('Pages.Layanan.Partials.head')

    <body>

        <!-- NAVBAR PARTIAL -->
        @include('Pages.Layanan.Partials.navbar')

        <!-- HERO PARTIAL -->
        @include('Pages.Layanan.Partials.hero')

        <!-- MAIN DIRECTORY GRID -->
        <main class="main-container">
            <div class="grid-container" id="satkerGrid">
                @foreach(collect($daftarSatker)->sortBy('nama_satker') as $item)
                    <!-- CARD SATKER PARTIAL -->
                    @include('Pages.Layanan.Partials.card_satker', ['item' => $item])
                @endforeach
            </div>
        </main>

        <!-- FOOTER PARTIAL -->
        @include('Pages.Layanan.Partials.footer')

        <!-- JavaScript Filters & Alert Confirmations -->
        <script>
            function filterSatker() {
                let input = document.getElementById('searchInput').value.toLowerCase();
                let cards = document.getElementsByClassName('satker-card');

                for (let i = 0; i < cards.length; i++) {
                    let titleData = cards[i].getAttribute('data-title');
                    if (titleData.includes(input)) {
                        cards[i].style.display = "flex";
                    } else {
                        cards[i].style.display = "none";
                    }
                }
            }

            function showCallDisabledAlert(namaSatker) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Layanan Telepon Tidak Aktif',
                    text: 'Mohon maaf, perangkat audio / panggilan telepon di PTSP ' + namaSatker + ' sedang tidak aktif. Silakan gunakan layanan Pesan WhatsApp.',
                    confirmButtonColor: '#047857'
                });
            }

            function showWaDisabledAlert(namaSatker) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Layanan WhatsApp Belum Siap',
                    text: 'Mohon maaf, layanan pesan WhatsApp di PTSP ' + namaSatker + ' sedang mengalami kendala atau belum siap digunakan.',
                    confirmButtonColor: '#047857'
                });
            }

            // 1. MODAL FORMULIR PENGUNJUNG PTSP
            function showFormPengunjung(satkerId, namaSatker, jenisLayanan, linkWa) {
                @php
                    $listPekerjaan = \App\Models\Pekerjaan::orderBy('nama_pekerjaan', 'asc')->pluck('nama_pekerjaan')->toArray();
                @endphp

                const listPekerjaan = @json($listPekerjaan ?? []);
                
                let optionsPekerjaan = `<option value="">-- Pilih Pekerjaan --</option>`;
                if (listPekerjaan.length > 0) {
                    listPekerjaan.forEach(item => {
                        optionsPekerjaan += `<option value="${item}">${item}</option>`;
                    });
                }

                Swal.fire({
                    title: '<span style="color:#047857; font-size: 1.2rem;">Formulir Pengunjung PTSP</span>',
                    html: `
                        <div style="text-align: left; font-size: 0.85rem; color: #475569;">
                            <p style="margin-bottom: 12px; text-align: center;">Silakan isi data identitas Anda sebelum terhubung dengan Petugas PTSP <b>${namaSatker}</b>.</p>
                            
                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">Nama Lengkap <span style="color:red">*</span></label>
                                <input id="sw_nama" class="swal2-input" placeholder="Contoh: Ahmad Abdullah" style="width: 100%; margin: 0; height: 38px; font-size: 0.85rem;">
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">Nomor WhatsApp / HP <span style="color:red">*</span></label>
                                <input id="sw_hp" class="swal2-input" placeholder="Contoh: 081234567890" style="width: 100%; margin: 0; height: 38px; font-size: 0.85rem;">
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">Jenis Kelamin <span style="color:red">*</span></label>
                                <select id="sw_jk" class="swal2-select" style="width: 100%; margin: 0; height: 38px; font-size: 0.85rem;">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">Pekerjaan</label>
                                <select id="sw_pekerjaan" class="form-select" style="width: 100%;">
                                    ${optionsPekerjaan}
                                </select>
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">NIK / No. KTP</label>
                                <input id="sw_nik" class="swal2-input" placeholder="16 Digit NIK (Opsional)" maxlength="16" style="width: 100%; margin: 0; height: 38px; font-size: 0.85rem;">
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">Ringkasan Keperluan</label>
                                <textarea id="sw_keperluan" class="swal2-textarea" placeholder="Tuliskan singkat perihal konsultasi Anda..." style="width: 100%; margin: 0; height: 50px; font-size: 0.85rem;"></textarea>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-paper-plane me-1"></i> Simpan & Lanjutkan',
                    cancelButtonText: 'Batal',
                    focusConfirm: false,
                    didOpen: () => {
                        if (typeof $ !== 'undefined' && $.fn.select2) {
                            $('#sw_pekerjaan').select2({
                                dropdownParent: $('.swal2-container'),
                                placeholder: "-- Pilih Pekerjaan --",
                                allowClear: true,
                                width: '100%'
                            });
                        }
                    },
                    preConfirm: () => {
                        const nama = document.getElementById('sw_nama').value.trim();
                        const hp = document.getElementById('sw_hp').value.trim();
                        const jk = document.getElementById('sw_jk').value;
                        const pekerjaan = $('#sw_pekerjaan').val() || '';
                        const nik = document.getElementById('sw_nik').value.trim();
                        const keperluan = document.getElementById('sw_keperluan').value.trim();

                        if (!nama || !hp || !jk) {
                            Swal.showValidationMessage('Nama, No. WhatsApp, dan Jenis Kelamin wajib diisi!');
                            return false;
                        }

                        return {
                            satker_id: satkerId,
                            jenis_layanan: jenisLayanan,
                            nama_responden: nama,
                            no_hp: hp,
                            jenis_kelamin: jk,
                            pekerjaan: pekerjaan,
                            nik: nik,
                            keperluan: keperluan
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const data = result.value;

                        fetch('{{ route("public.pengunjung.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                const sapaan = (data.jenis_kelamin === 'L') ? 'Bapak' : 'Ibu';
                                let txtPesan = "Assalamualaikum Admin " + namaSatker + ", saya " + data.nama_responden + " ingin berkonsultasi";
                                
                                if (jenisLayanan === 'telepon') {
                                    txtPesan += " Melalui Telepon";
                                }

                                const finalWaUrl = linkWa + "?text=" + encodeURIComponent(txtPesan);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terima Kasih!',
                                    html: `Terima kasih, data Anda telah berhasil tersimpan.<br><br>Mohon tunggu sebentar, Admin PTSP <b>${namaSatker}</b> akan segera menghubungi ${sapaan} <b>${data.nama_responden}</b>.`,
                                    confirmButtonColor: '#25D366',
                                    confirmButtonText: '<i class="fa-brands fa-whatsapp me-1"></i> Hubungi Admin PTSP',
                                    allowOutsideClick: false
                                }).then((resKonfirm) => {
                                    if (resKonfirm.isConfirmed) {
                                        window.open(finalWaUrl, '_blank');
                                    }
                                });
                            } else {
                                Swal.fire('Gagal!', 'Gagal menyimpan data pengunjung.', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error!', 'Terjadi kesalahan sistem/jaringan.', 'error');
                        });
                    }
                });
            }

            // 2. MODAL FORMULIR PENGADUAN PTSP
            function showFormPengaduan(satkerId, namaSatker, linkWa) {
                Swal.fire({
                    title: '<span style="color:#d97706; font-size: 1.2rem;"><i class="fa-solid fa-triangle-exclamation me-1"></i> Form Pengaduan PTSP</span>',
                    html: `
                        <div style="text-align: left; font-size: 0.85rem; color: #475569;">
                            <p style="margin-bottom: 12px; text-align: center;">Sampaikan laporan pengaduan Anda mengenai layanan PTSP <b>${namaSatker}</b>.</p>
                            
                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">Nama Pelapor <span style="color:red">*</span></label>
                                <input id="sw_p_nama" class="swal2-input" placeholder="Nama Lengkap Anda" style="width: 100%; margin: 0; height: 38px; font-size: 0.85rem;">
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">Nomor WhatsApp / HP <span style="color:red">*</span></label>
                                <input id="sw_p_hp" class="swal2-input" placeholder="Contoh: 081234567890" style="width: 100%; margin: 0; height: 38px; font-size: 0.85rem;">
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">NIK / No. KTP</label>
                                <input id="sw_p_nik" class="swal2-input" placeholder="16 Digit NIK (Opsional)" maxlength="16" style="width: 100%; margin: 0; height: 38px; font-size: 0.85rem;">
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label style="font-weight: 600; display: block; margin-bottom: 2px;">Uraian Pengaduan <span style="color:red">*</span></label>
                                <textarea id="sw_p_uraian" class="swal2-textarea" placeholder="Tuliskan secara ringkas dan jelas detail pengaduan Anda..." style="width: 100%; margin: 0; height: 75px; font-size: 0.85rem;"></textarea>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-paper-plane me-1"></i> Kirim Pengaduan',
                    cancelButtonText: 'Batal',
                    focusConfirm: false,
                    preConfirm: () => {
                        const nama = document.getElementById('sw_p_nama').value.trim();
                        const hp = document.getElementById('sw_p_hp').value.trim();
                        const nik = document.getElementById('sw_p_nik').value.trim();
                        const uraian = document.getElementById('sw_p_uraian').value.trim();

                        if (!nama || !hp || !uraian) {
                            Swal.showValidationMessage('Nama, No. WhatsApp, dan Uraian Pengaduan wajib diisi!');
                            return false;
                        }

                        return {
                            satker_id: satkerId,
                            nama_pelapor: nama,
                            no_hp: hp,
                            nik: nik,
                            uaraian_pengaduan: uraian
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const data = result.value;

                        fetch('{{ route("public.pengaduan.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                let txtPesan = "Assalamualaikum Admin " + namaSatker + ", saya " + data.nama_pelapor + " ingin menyampaikan Pengaduan:\n\n" + data.uaraian_pengaduan;
                                const finalWaUrl = linkWa + "?text=" + encodeURIComponent(txtPesan);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pengaduan Terkirim!',
                                    html: `Laporan pengaduan Anda telah tercatat dalam sistem.<br><br>Guna penanganan cepat, Anda juga dapat meneruskan laporan langsung ke Admin PTSP <b>${namaSatker}</b>.`,
                                    confirmButtonColor: '#25D366',
                                    confirmButtonText: '<i class="fa-brands fa-whatsapp me-1"></i> Hubungi Admin PTSP',
                                    allowOutsideClick: false
                                }).then((resKonfirm) => {
                                    if (resKonfirm.isConfirmed) {
                                        window.open(finalWaUrl, '_blank');
                                    }
                                });
                            } else {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan pengaduan.', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error!', 'Terjadi kesalahan sistem/jaringan.', 'error');
                        });
                    }
                });
            }
        </script>
    </body>
</html>