<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Satker;
use App\Models\JenisPerkara;

class SyaratPerkaraSeeder extends Seeder
{
    public function run(): void
    {
        $satkers = Satker::where('satker_vshort', '!=', 'ms-aceh')->get();

        if ($satkers->isEmpty()) {
            $this->command->warn('Tabel satkers kosong! Jalankan SatkerSeeder terlebih dahulu.');
            return;
        }

        // Mapping Syarat per Nama Layanan
        $listSyaratMaster = [
            // ==========================================
            // A. BIDANG PERKAWINAN (PRIORITAS UTAMA)
            // ==========================================
            'Cerai Gugat' => [
                'Surat Gugatan (Bermaterai 10.000)',
                'Fotokopi KTP Penggugat (Istri)',
                'Buku Nikah / Kutipan Akta Nikah Asli',
                'Fotokopi Buku Nikah (Dilegalisir Kantor Pos)',
                'Surat Izin Atasan (Khusus PNS/TNI/Polri/BUMN)',
                'Surat Keterangan Gaib dari Keuchik (Jika suami tidak diketahui keberadaannya/Ghoib)'
            ],
            'Cerai Talak' => [
                'Surat Permohonan Cerai Talak (Bermaterai 10.000)',
                'Fotokopi KTP Pemohon (Suami)',
                'Buku Nikah / Kutipan Akta Nikah Asli',
                'Fotokopi Buku Nikah (Dilegalisir Kantor Pos)',
                'Surat Izin Atasan (Khusus PNS/TNI/Polri/BUMN)'
            ],
            'Izin Poligami' => [
                'Surat Permohonan Izin Poligami (Bermaterai 10.000)',
                'Fotokopi KTP Pemohon, Istri Pertama, dan Calon Istri',
                'Buku Nikah Asli Istri Pertama',
                'Surat Pernyataan Rela Dimadu dari Istri Pertama (Bermaterai 10.000)',
                'Surat Pernyataan Sanggup Berlaku Adil (Bermaterai 10.000)',
                'Surat Keterangan Penghasilan / Slip Gaji Pemohon',
                'Surat Keterangan Status Calon Istri dari Keuchik/KUA',
                'Daftar Rincian Harta Bersama dengan Istri Pertama'
            ],
            'Dispensasi Nikah' => [
                'Surat Permohonan Dispensasi Nikah (Diajukan oleh Orang Tua/Wali)',
                'Fotokopi KTP & Kartu Keluarga (KK) Orang Tua / Wali',
                'Fotokopi KTP/KIA & Akta Kelahiran Anak',
                'Fotokopi Ijazah Terakhir Anak',
                'Surat Penolakan Nikah dari KUA (Model N5)',
                'Surat Keterangan Sehat Anak dari Puskesmas/RSUD',
                'Fotokopi KTP dan KK Calon Suami/Istri Anak'
            ],
            'Isybat Nikah' => [
                'Surat Permohonan Isybat Nikah',
                'Fotokopi KTP & Kartu Keluarga (KK) Pemohon (Suami & Istri)',
                'Surat Keterangan dari KUA bahwa pernikahan belum tercatat',
                'Surat Keterangan Pernah Menikah dari Keuchik/Kepala Desa',
                'Fotokopi Akta Kelahiran Anak (Jika sudah memiliki anak)'
            ],
            'Pembatalan Nikah' => [
                'Surat Gugatan Pembatalan Nikah',
                'Fotokopi KTP Penggugat',
                'Kutipan Akta Nikah yang ingin dibatalkan (Asli/Fotokopi)',
                'Fotokopi KK Penggugat',
                'Bukti pendukung adanya halangan perkawinan atau penipuan'
            ],
            'Gugatan Penguasaan Anak (Hadhanah)' => [
                'Surat Gugatan Hadhanah (Bermaterai 10.000)',
                'Fotokopi KTP Penggugat',
                'Akta Cerai Asli & Fotokopi (Dilegalisir Kantor Pos)',
                'Fotokopi Akta Kelahiran Anak',
                'Fotokopi Kartu Keluarga (KK)'
            ],
            'Gugatan Nafkah Anak & Istri' => [
                'Surat Gugatan Nafkah (Bermaterai 10.000)',
                'Fotokopi KTP Penggugat',
                'Buku Nikah Asli / Akta Cerai',
                'Fotokopi Akta Kelahiran Anak (Untuk nafkah anak)',
                'Rincian estimasi biaya kebutuhan hidup anak/istri',
                'Slip Gaji / Bukti estimasi penghasilan Tergugat (Jika ada)'
            ],
            'Gugatan Harta Bersama (Gono-Gini)' => [
                'Surat Gugatan Harta Bersama (Bermaterai 10.000)',
                'Fotokopi KTP Penggugat',
                'Akta Cerai Asli / Salinan Putusan Cerai',
                'Bukti Kepemilikan Objek (Sertifikat Tanah, BPKB, Rekening Tabungan, Emas, dll)',
                'Surat Keterangan Penguasaan Objek Sengketa dari Keuchik'
            ],
            'Wali Adhal' => [
                'Surat Permohonan Wali Adhal',
                'Fotokopi KTP Calon Pengantin Wanita & Calon Suami',
                'Fotokopi KK Calon Pengantin Wanita',
                'Surat Penolakan Wali dari KUA (Model N8)',
                'Surat Keterangan Penolakan Wali dari Keuchik'
            ],
            'Penetapan Asal Usul Anak' => [
                'Surat Permohonan Asal Usul Anak',
                'Fotokopi KTP & KK Pemohon',
                'Buku Nikah / Bukti Pernikahan Pemohon',
                'Surat Keterangan Lahir / Surat dari Bidan atau Rumah Sakit'
            ],
            'Pengangkatan Anak (Adopsi)' => [
                'Surat Permohonan Pengangkatan Anak',
                'Fotokopi KTP & KK Pemohon (Orang Tua Angkat)',
                'Fotokopi KTP & KK Orang Tua Kandung',
                'Buku Nikah Pemohon',
                'Fotokopi Akta Kelahiran Anak',
                'Surat Rekomendasi dari Dinas Sosial',
                'Surat Pernyataan Penyerahan Anak dari Orang Tua Kandung (Bermaterai)',
                'Surat Keterangan Penghasilan Pemohon',
                'Surat Keterangan Catatan Kepolisian (SKCK) Pemohon',
                'Surat Keterangan Sehat Jasmani & Rohani Pemohon'
            ],

            // ==========================================
            // B. BIDANG KEWARISAN & HARTA
            // ==========================================
            'Penetapan Ahli Waris (P3HP)' => [
                'Surat Permohonan Penetapan Ahli Waris',
                'Fotokopi KTP & KK Pemohon beserta seluruh Ahli Waris',
                'Surat Keterangan Kematian Pewaris dari Keuchik/Catatan Sipil',
                'Surat Keterangan Ahli Waris dari Gampong (Disahkan Camat)',
                'Buku Nikah Pewaris (Bila Pewaris sudah menikah)',
                'Surat Keterangan Lahir/Akta Kelahiran seluruh Ahli Waris'
            ],
            'Gugatan Pembagian Waris' => [
                'Surat Gugatan Pembagian Waris (Bermaterai 10.000)',
                'Fotokopi KTP Penggugat',
                'Surat Keterangan Kematian Pewaris',
                'Silsilah / Bagan Ahli Waris (Diketahui Keuchik)',
                'Bukti Kepemilikan Harta Peninggalan (Sertifikat/BPKB/Buku Tabungan)',
                'Surat Keterangan Penguasaan Harta dari Keuchik'
            ],
            'Pembatalan / Sengketa Wasiat' => [
                'Surat Gugatan Sengketa Wasiat',
                'Fotokopi KTP Penggugat',
                'Dokumen Akta Wasiat Asli atau Fotokopi (Jika tertulis)',
                'Bukti Kepemilikan Objek Wasiat (Sertifikat/Surat Tanah)'
            ],
            'Pembatalan / Sengketa Hibah' => [
                'Surat Gugatan Sengketa Hibah',
                'Fotokopi KTP Penggugat',
                'Dokumen Akta / Surat Hibah (Asli atau Fotokopi)',
                'Bukti Kepemilikan Objek Hibah'
            ],
            'Sengketa Wakaf' => [
                'Surat Gugatan Sengketa Wakaf',
                'Fotokopi KTP Penggugat / Legalitas Nazhir Wakaf',
                'Akta Ikrar Wakaf (AIW) dari KUA',
                'Sertifikat Tanah Wakaf (Jika ada)',
                'Bukti Kepemilikan Objek Wakaf'
            ],

            // ==========================================
            // C. BIDANG EKONOMI SYARIAH
            // ==========================================
            'Sengketa Perbankan Syariah' => [
                'Surat Gugatan Ekonomi Syariah (Bermaterai 10.000)',
                'Fotokopi KTP Penggugat / Legalitas Perusahaan (Akta Pendirian)',
                'Dokumen Akad Pembiayaan / Kredit Syariah (Murabahah, Musyarakah, dll)',
                'Buku Rekening / Rekening Koran Terkait',
                'Bukti Transfer / Cicilan Terakhir',
                'Surat Peringatan (Somasi) jika ada'
            ],
            'Sengketa Asuransi Syariah' => [
                'Surat Gugatan Sengketa Asuransi',
                'Fotokopi KTP Penggugat',
                'Polis Asuransi Syariah (Asli / Fotokopi)',
                'Bukti Pembayaran Premi',
                'Surat Penolakan Klaim dari Pihak Asuransi (Jika klaim ditolak)'
            ],
            'Sengketa Pegadaian Syariah (Rahn)' => [
                'Surat Gugatan Sengketa Rahn',
                'Fotokopi KTP Penggugat',
                'Surat Bukti Rahn (SBR) / Surat Akad Gadai',
                'Bukti Pembayaran / Cicilan Perpanjangan',
                'Bukti Kepemilikan Barang Jaminan (BPKB / Kwitansi Emas dll)'
            ],
            'Sengketa Obligasi & Surat Berharga Syariah (Sukuk)' => [
                'Surat Gugatan',
                'Fotokopi KTP / Legalitas Perusahaan',
                'Dokumen Sertifikat Surat Berharga / Sukuk',
                'Bukti Pembelian / Nilai Investasi'
            ],
            'Sengketa Bisnis Syariah Lainnya (Koperasi/BMT/Umum)' => [
                'Surat Gugatan Sengketa Bisnis',
                'Fotokopi KTP / Akta Pendirian Usaha',
                'Dokumen MoU / Akad Kerjasama Bisnis',
                'Buku Simpanan/Pinjaman (Khusus Koperasi/BMT)',
                'Bukti Aliran Dana / Kwitansi / Laporan Keuangan'
            ]
        ];

        foreach ($listSyaratMaster as $namaLayanan => $syaratArray) {
            
            // 1. CARI UUID JENIS PERKARA YANG SUDAH DIBUAT DARI JenisPerkaraSeeder
            $jenisPerkara = JenisPerkara::where('nama_layanan', $namaLayanan)->first();

            if ($jenisPerkara) {
                foreach ($satkers as $satker) {
                    
                    // 2. LOOPING SETIAP DOKUMEN DAN SIMPAN SEBAGAI 1 ROW BERBEDAR + 1 UUID UNIK
                    foreach ($syaratArray as $namaDokumen) {
                        
                        $exists = DB::table('syarat_perkaras')->where([
                            'satker_id'        => $satker->id,
                            'jenis_perkara_id' => $jenisPerkara->id,
                            'syarat_dokumen'   => $namaDokumen,
                        ])->exists();

                        if (!$exists) {
                            DB::table('syarat_perkaras')->insert([
                                'id'                 => (string) Str::uuid(),         // UUID UNIK PER DOKUMEN
                                'satker_id'          => $satker->id,
                                'jenis_perkara_id'   => $jenisPerkara->id,            // REFERENSI KE PERKARA YANG SAMA
                                'syarat_dokumen'     => $namaDokumen,                 // STRING MURNI PER ROW
                                'url_dokumen'        => null,
                                'is_active'          => 0,
                                'is_approved'        => 0,
                                'catatan_verifikasi' => null,
                                'created_at'         => now(),
                                'updated_at'         => now(),
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('SUKSES! Tiap syarat dokumen dimasukkan 1-per-1 dengan UUID masing-masing!');
    }
}