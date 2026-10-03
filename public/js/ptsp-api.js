// Cek Jam Kerja (WIB / GMT+7)
export function isJamKerjaWIB() {
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

// Init Data Satker & Domain Check
export async function fetchInitData(serverUrl, satkerId) {
    const res = await fetch(`${serverUrl}/api/ptsp/init-data?satker_id=${satkerId}`);
    const data = await res.json();
    if (!res.ok || data.status === 'error') {
        throw new Error(data.message || 'Akses Widget Ditolak.');
    }
    return data;
}

// Store Data Konsultasi / Pemohon Informasi
export async function storeKonsultasi(serverUrl, payload) {
    const res = await fetch(`${serverUrl}/api/ptsp/store-pengunjung`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (!res.ok || data.status === 'error') {
        throw new Error(data.message || 'Gagal menyimpan data.');
    }
    return data;
}

// Store Data Pengaduan
export async function storePengaduan(serverUrl, payload) {
    const res = await fetch(`${serverUrl}/api/ptsp/store-pengaduan`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (!res.ok || data.status === 'error') {
        throw new Error(data.message || 'Gagal mengirim pengaduan.');
    }
    return data;
}