<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class JenisPerkaraSeeder extends Seeder
{
    public function run(): void
    {
        $jenisPerkaras = [
            // ==========================================
            // A. BIDANG PERKAWINAN
            // ==========================================
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Cerai Gugat',
                'deskripsi' => 'Gugatan perceraian yang diajukan oleh Istri terhadap Suami di Mahkamah Syar\'iyah.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Cerai Talak',
                'deskripsi' => 'Permohonan ikrar talak yang diajukan oleh Suami terhadap Istri.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Izin Poligami',
                'deskripsi' => 'Permohonan izin dari Suami untuk beristri lebih dari satu orang.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Dispensasi Nikah',
                'deskripsi' => 'Permohonan izin menikah bagi calon pengantin yang usianya di bawah 19 tahun.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Isybat Nikah',
                'deskripsi' => 'Permohonan pengesahan pernikahan siri atau nikah yang tidak tercatat secara negara.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Pembatalan Nikah',
                'deskripsi' => 'Gugatan pembatalan perkawinan karena terdapat halangan syar\'i, pemalsuan identitas, atau penipuan.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Gugatan Penguasaan Anak (Hadhanah)',
                'deskripsi' => 'Gugatan hak pemeliharaan dan pengasuhan anak di bawah umur akibat perceraian.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Gugatan Nafkah Anak & Istri',
                'deskripsi' => 'Gugatan tuntutan pemenuhan nafkah lampau (madhiyah), iddah, mut\'ah, atau nafkah anak.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Gugatan Harta Bersama (Gono-Gini)',
                'deskripsi' => 'Gugatan pembagian harta yang diperoleh selama masa perkawinan.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Wali Adhal',
                'deskripsi' => 'Permohonan penetapan wali hakim karena wali nasab menolak/enggan menjadi wali nikah.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Penetapan Asal Usul Anak',
                'deskripsi' => 'Permohonan status hukum anak secara perdata dengan ayah biologisnya.',
            ],
            [
                'kategori' => 'Perkawinan',
                'nama_layanan' => 'Pengangkatan Anak (Adopsi)',
                'deskripsi' => 'Permohonan pengesahan pengangkatan anak (adopsi).',
            ],

            // ==========================================
            // B. BIDANG KEWARISAN, WASIAT, HIBAH & WAKAF
            // ==========================================
            [
                'kategori' => 'Kewarisan & Harta',
                'nama_layanan' => 'Penetapan Ahli Waris (P3HP)',
                'deskripsi' => 'Permohonan penetapan porsi dan susunan ahli waris yang sah.',
            ],
            [
                'kategori' => 'Kewarisan & Harta',
                'nama_layanan' => 'Gugatan Pembagian Waris',
                'deskripsi' => 'Sengketa pembagian harta peninggalan antar ahli waris.',
            ],
            [
                'kategori' => 'Kewarisan & Harta',
                'nama_layanan' => 'Pembatalan / Sengketa Wasiat',
                'deskripsi' => 'Gugatan pembatalan atau tuntutan atas pemberian wasiat.',
            ],
            [
                'kategori' => 'Kewarisan & Harta',
                'nama_layanan' => 'Pembatalan / Sengketa Hibah',
                'deskripsi' => 'Gugatan pembatalan atau sengketa pemberian hibah.',
            ],
            [
                'kategori' => 'Kewarisan & Harta',
                'nama_layanan' => 'Sengketa Wakaf',
                'deskripsi' => 'Gugatan sengketa terkait peruntukan, pengelolaan, atau kepemilikan aset wakaf.',
            ],

            // ==========================================
            // C. BIDANG EKONOMI SYARIAH
            // ==========================================
            [
                'kategori' => 'Ekonomi Syariah',
                'nama_layanan' => 'Sengketa Perbankan Syariah',
                'deskripsi' => 'Sengketa perdata, wanprestasi, atau bagi hasil pada perbankan syariah.',
            ],
            [
                'kategori' => 'Ekonomi Syariah',
                'nama_layanan' => 'Sengketa Asuransi Syariah',
                'deskripsi' => 'Sengketa klaim atau polis asuransi berbasis syariah.',
            ],
            [
                'kategori' => 'Ekonomi Syariah',
                'nama_layanan' => 'Sengketa Pegadaian Syariah (Rahn)',
                'deskripsi' => 'Sengketa penebusan, lelang jaminan, atau akad gadai pada pegadaian syariah.',
            ],
            [
                'kategori' => 'Ekonomi Syariah',
                'nama_layanan' => 'Sengketa Obligasi & Surat Berharga Syariah (Sukuk)',
                'deskripsi' => 'Sengketa investasi dan kepemilikan surat berharga syariah.',
            ],
            [
                'kategori' => 'Ekonomi Syariah',
                'nama_layanan' => 'Sengketa Bisnis Syariah Lainnya (Koperasi/BMT/Umum)',
                'deskripsi' => 'Gugatan pelanggaran kontrak kerjasama (Mudharabah/Musyarakah/Murabahah) non-perbankan.',
            ],
        ];

        foreach ($jenisPerkaras as $item) {
            $exists = DB::table('jenis_perkaras')->where('nama_layanan', $item['nama_layanan'])->exists();

            if (!$exists) {
                DB::table('jenis_perkaras')->insert([
                    'id'           => (string) Str::uuid(),
                    'kategori'     => $item['kategori'],
                    'nama_layanan' => $item['nama_layanan'],
                    'deskripsi'    => $item['deskripsi'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        $this->command->info('Master Jenis Perkara berhasil di-seed dengan UUID!');
    }
}