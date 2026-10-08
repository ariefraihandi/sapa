# Panduan Pembuatan Frontend Dashboard (Untuk AI Assistant)

**Konteks Project:**
Sistem ini merupakan bot WhatsApp berbasis `whatsapp-web.js` dan Express.js (Node.js) yang dijalankan melalui Docker. Bot menyimpan data riwayat pengguna dan FAQ/Sapaan ke dalam basis data SQLite lokal (microservice). 

**Tugas Anda (AI Berikutnya):**
Bantu *User* membuat antarmuka Web (seperti panel Admin / Dashboard) menggunakan framework pilihan User (misalnya Laravel, CodeIgniter, React, atau native PHP) untuk mengonsumsi REST API yang disediakan oleh Node.js ini.

---

## Spesifikasi REST API yang Tersedia

Base URL Node.js: `http://localhost:8001` *(Sesuaikan IP/Host jika Web dan Node.js berada di server terpisah)*.

### 1. Endpoint Manajemen Sapaan & FAQ
Semua data FAQ (termasuk "Sapaan Awal" dengan *keyword* khusus `[SAPAAN_AWAL]`) berada di tabel yang sama.

#### A. Ambil Semua FAQ & Sapaan
- **Endpoint**: `GET /api/faqs`
- **Response**:
  ```json
  {
    "status": 200,
    "data": [
      {
        "id": 1,
        "keyword": "[SAPAAN_AWAL]",
        "answer": "Halo! Selamat datang...",
        "is_active": 1
      },
      {
        "id": 2,
        "keyword": "jadwal sidang",
        "answer": "Klik link berikut...",
        "is_active": 1
      }
    ]
  }
  ```

#### B. Tambah FAQ Baru
- **Endpoint**: `POST /api/faqs`
- **Request Body (JSON / Form Data)**:
  - `keyword` (string): Kata kunci (huruf kecil disarankan).
  - `answer` (string): Balasan teks untuk pengguna.
  - `is_active` (integer): `1` (Aktif) atau `0` (Non-aktif).
- **Response**: `{"status": 200, "message": "FAQ ditambahkan"}`

#### C. Ubah (Update) FAQ / Sapaan Awal
Sangat penting untuk antarmuka mengizinkan pengeditan pada ID yang memiliki *keyword* `[SAPAAN_AWAL]`.
- **Endpoint**: `PUT /api/faqs/{id}`
- **Request Body (JSON / Form Data)**: `keyword`, `answer`, `is_active`
- **Response**: `{"status": 200, "message": "FAQ diperbarui"}`

#### D. Hapus FAQ
*Constraint UI: Pastikan memberikan peringatan atau mencegah penghapusan jika record tersebut memiliki keyword `[SAPAAN_AWAL]`.*
- **Endpoint**: `DELETE /api/faqs/{id}`
- **Response**: `{"status": 200, "message": "FAQ dihapus"}`

---

### 2. Endpoint Data Pengguna (Riwayat Sapaan)
- **Endpoint**: `GET /api/bot_users`
- **Deskripsi**: Menampilkan nomor WhatsApp yang pernah berinteraksi, dan waktu `last_chat` mereka. Data ini diurutkan berdasarkan chat terbaru. Bot hanya mengirimkan sapaan ulang jika `last_chat` ini berumur lebih dari 24 jam.
- **Response**:
  ```json
  {
    "status": 200,
    "data": [
      {
        "no_hp": "6281234567890",
        "last_chat": "2026-10-08 14:30:00"
      }
    ]
  }
  ```

---

## Rekomendasi Tampilan (UI) yang Harus Anda Buat:

1. **Halaman Pengaturan Sapaan Otomatis**
   - Ambil data dari `/api/faqs` dan *filter* (pisahkan) *record* yang memiliki `keyword == '[SAPAAN_AWAL]'`.
   - Buat satu kotak *textarea* besar khusus untuk mengedit isi "Sapaan Awal" ini saja (simpan via `PUT /api/faqs/{id}`).

2. **Halaman Manajemen FAQ**
   - Sebuah tabel *DataTables* yang melist sisa data `/api/faqs` (kecuali `[SAPAAN_AWAL]`).
   - Tombol "Tambah FAQ" yang membuka modal berisi form `keyword`, `answer`, dan *checkbox* Aktif.
   - Kolom Aksi dengan tombol Edit (`PUT`) dan Hapus (`DELETE`).

3. **Halaman Log/Riwayat Pengguna Bot**
   - Sebuah tabel yang mengambil data dari `GET /api/bot_users` untuk memonitor nomor-nomor HP mana saja yang terdaftar dan kapan mereka terakhir *chat*.

---
**Instruksi Akhir untuk AI:** Saat User meminta Anda untuk mengimplementasikan *Frontend* ini, tanyakan terlebih dahulu apa *stack/framework* yang digunakan User (contoh: PHP Murni, Laravel, React, Vue, dll), lalu berikan *source code* halamannya berikut logika pemanggilan API (`cURL` / `Fetch` / `Axios`) berdasarkan panduan ini.
