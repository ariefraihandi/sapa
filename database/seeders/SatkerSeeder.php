<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Satker;
use App\Models\PtspDaerah;

class SatkerSeeder extends Seeder
{
    /**
     * Helper untuk menyamakan format nomor HP ke format standar 628xxxxxxxxxx
     */
    private function formatPhoneNumber(?string $number): ?string
    {
        if (empty($number)) {
            return null;
        }

        // Hapus semua karakter selain angka
        $clean = preg_replace('/[^0-9]/', '', $number);

        // Jika diawali '0', ubah menjadi '62'
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        return $clean;
    }

    public function run(): void
    {
        // 1. Data mentah lengkap dari survey PTSP (22 Satker)
        $daftarSatkerRaw = [
            [
                'timestamp'          => '04/08/2026 10:00:00',
                'nama_satker'        => 'MS Aceh',
                'wilayah_kerja'      => 'Provinsi Aceh (Tingkat Banding)',
                'penanggung_jawab'   => 'Petugas PTSP MS Aceh',
                'hp_pj'              => '081360945465',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '+62 813-6094-5465',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tersedia & Aktif',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 11:40:04',
                'nama_satker'        => 'MS Banda Aceh',
                'wilayah_kerja'      => 'Kota Banda Aceh',
                'penanggung_jawab'   => 'Panitera dan Sekretaris',
                'hp_pj'              => '085260268937',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '082274448844',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 13:19:17',
                'nama_satker'        => 'MS Bireuen',
                'wilayah_kerja'      => 'Kabupaten Bireuen',
                'penanggung_jawab'   => 'Saifuddin, S.Ag,.M.H',
                'hp_pj'              => '082213833201',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '081297545703',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 13:46:15',
                'nama_satker'        => 'MS Blangkejeren',
                'wilayah_kerja'      => 'Kabupaten Gayo Lues',
                'penanggung_jawab'   => 'Sukna, S.Ag',
                'hp_pj'              => '+62 853-6041-8472',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '085321317500',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tersedia & Aktif',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 15:23:46',
                'nama_satker'        => 'MS Blangpidie',
                'wilayah_kerja'      => 'Kabupaten Aceh Barat Daya',
                'penanggung_jawab'   => 'Faisal Reza, S.H.I dan Mukhsin Sardi, S.E',
                'hp_pj'              => '085260026237',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '081397362200',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 14:35:20',
                'nama_satker'        => 'MS Calang',
                'wilayah_kerja'      => 'Kabupaten Aceh Jaya',
                'penanggung_jawab'   => 'Afwan Zahri, S.H.I., M.H.',
                'hp_pj'              => '085277756189',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '085122296489',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tersedia & Aktif',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '05/08/2026 11:04:43',
                'nama_satker'        => 'MS Idi',
                'wilayah_kerja'      => 'Kabupaten Aceh Timur',
                'penanggung_jawab'   => 'Ilyas, S.Ag., M.H.',
                'hp_pj'              => '0811360738911',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '085268343401',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tidak Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 11:09:52',
                'nama_satker'        => 'MS Jantho',
                'wilayah_kerja'      => 'Kabupaten Aceh Besar',
                'penanggung_jawab'   => 'Akmal Hakim BS dan Sufriadi',
                'hp_pj'              => '08126971651',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '08116813336',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tersedia & Aktif',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 10:58:11',
                'nama_satker'        => 'MS Kota Subulussalam',
                'wilayah_kerja'      => 'Kota Subulussalam',
                'penanggung_jawab'   => 'Rafsanzani',
                'hp_pj'              => '0895428487678',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '082386750423',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tersedia & Aktif',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 11:30:03',
                'nama_satker'        => 'MS Kuala Simpang',
                'wilayah_kerja'      => 'Kabupaten Aceh Tamiang',
                'penanggung_jawab'   => 'Yusnidar, S. H',
                'hp_pj'              => '085260309975',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '081168821818',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tersedia & Aktif',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 11:13:32',
                'nama_satker'        => 'MS Kutacane',
                'wilayah_kerja'      => 'Kabupaten Aceh Tenggara',
                'penanggung_jawab'   => 'Suherdi, S. Ag.',
                'hp_pj'              => '081358573180',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '081262026161',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tersedia & Aktif',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 11:03:27',
                'nama_satker'        => 'MS Langsa',
                'wilayah_kerja'      => 'Kota Langsa',
                'penanggung_jawab'   => 'Anny Suryani, S. Ag., M. H.',
                'hp_pj'              => '085261537484',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '085262567199',
                'status_ptsp'        => 'Ada, Namun Mengalami Kendala',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '04/08/2026 15:11:34',
                'nama_satker'        => 'MS Lhokseumawe',
                'wilayah_kerja'      => 'Kota Lhokseumawe',
                'penanggung_jawab'   => 'Fauzi, S.Ag',
                'hp_pj'              => '081263838956',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '081263838956',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 11:31:59',
                'nama_satker'        => 'MS Lhoksukon',
                'wilayah_kerja'      => 'Kabupaten Aceh Utara',
                'penanggung_jawab'   => 'Fadhlullah, S. H.',
                'hp_pj'              => '081360133524',
                'status_pemasangan'  => 'Belum',
                'no_ptsp'            => '081360133524',
                'status_ptsp'        => 'Tidak Tersedia',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => '-',
            ],
            [
                'timestamp'          => '28/07/2026 10:47:17',
                'nama_satker'        => 'MS Meulaboh',
                'wilayah_kerja'      => 'Kabupaten Aceh Barat',
                'penanggung_jawab'   => 'Antoni Sujarwo, SH.,MH',
                'hp_pj'              => '081266407959',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '085275434241',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 11:08:23',
                'nama_satker'        => 'MS Meureudu',
                'wilayah_kerja'      => 'Kabupaten Pidie Jaya',
                'penanggung_jawab'   => 'Dedy Afrizal, S.H.I., M.H.',
                'hp_pj'              => '085260450444',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '085285486212',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tersedia & Aktif',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 16:46:39',
                'nama_satker'        => 'MS Sabang',
                'wilayah_kerja'      => 'Kota Sabang',
                'penanggung_jawab'   => 'Chairunnisa Husaini, S.H., M.H.',
                'hp_pj'              => '082360524575',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '081361524526',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '04/08/2026 15:49:07',
                'nama_satker'        => 'MS Simpang Tiga Redelong',
                'wilayah_kerja'      => 'Kabupaten Bener Meriah',
                'penanggung_jawab'   => 'Muhammad Firdaus, SH',
                'hp_pj'              => '085261355000',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '082181429202',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 14:05:49',
                'nama_satker'        => 'MS Sinabang',
                'wilayah_kerja'      => 'Kabupaten Simeulue',
                'penanggung_jawab'   => 'Sayed Tarmizi',
                'hp_pj'              => '082361500463',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '085285304781',
                'status_ptsp'        => 'Ada, Namun Mengalami Kendala',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 11:18:07',
                'nama_satker'        => 'MS Singkil',
                'wilayah_kerja'      => 'Kabupaten Aceh Singkil',
                'penanggung_jawab'   => 'Tengku Tuti Handayani, S.H.',
                'hp_pj'              => '085277948800',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '081277770165',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tersedia & Aktif',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 11:31:07',
                'nama_satker'        => 'MS Suka Makmue',
                'wilayah_kerja'      => 'Kabupaten Nagan Raya',
                'penanggung_jawab'   => 'Syahrul, S.H.I.',
                'hp_pj'              => '085260369625',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '085372682384',
                'status_ptsp'        => 'Ada dan Siap Digunakan',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 10:25:26',
                'nama_satker'        => 'MS Takengon',
                'wilayah_kerja'      => 'Kabupaten Aceh Tengah',
                'penanggung_jawab'   => 'Ghazali Mahmudi, S.H',
                'hp_pj'              => '081274330865',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '085260603700',
                'status_ptsp'        => 'Ada, Namun Mengalami Kendala',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tidak Tersedia',
            ],
            [
                'timestamp'          => '28/07/2026 15:01:23',
                'nama_satker'        => 'MS Tapaktuan',
                'wilayah_kerja'      => 'Kabupaten Aceh Selatan',
                'penanggung_jawab'   => 'Syahrul Muhajir S.H.I',
                'hp_pj'              => '085260024234',
                'status_pemasangan'  => 'Sudah',
                'no_ptsp'            => '081376208374',
                'status_ptsp'        => 'Ada, Namun Mengalami Kendala',
                'layanan_informasi'  => 'Tidak Tersedia',
                'status_posbakum'    => 'Tersedia',
            ],
        ];

        // 2. Map variasi penulisan nama Satker ke vshort (slug)
        $vshortMapping = [
            'MS Aceh'                      => 'ms-aceh',
            'MS Banda Aceh'                => 'ms-bna',
            'Mahkamah Syar\'iyah Banda Aceh'=> 'ms-bna',
            'MS Sabang'                    => 'ms-sbg',
            'Mahkamah Syar\'iyah Sabang'   => 'ms-sbg',
            'MS Jantho'                    => 'ms-jth',
            'MS Sigli'                     => 'ms-sgl',
            'MS Meureudu'                  => 'ms-mrd',
            'Mahkamah Syar\'iyah Meureudu' => 'ms-mrd',
            'MS Bireuen'                   => 'ms-bir',
            'Mahkamah Syar\'iyah Bireuen'  => 'ms-bir',
            'MS Lhokseumawe'               => 'ms-lsm',
            'Mahkamah Syar\'iyah Lhokseumawe' => 'ms-lsm',
            'MS Lhoksukon'                 => 'ms-lsn',
            'Mahkamah Syar\'iyah Lhoksukon'=> 'ms-lsn',
            'MS Langsa'                    => 'ms-lgs',
            'Mahkamah Syar\'iyah Langsa'   => 'ms-lgs',
            'MS Idi'                       => 'ms-idi',
            'Mahkamah Syar\'iyah Idi'      => 'ms-idi',
            'MS Kuala Simpang'             => 'ms-ksg',
            'Mahkamah Syar’iyah Kuala Simpang' => 'ms-ksg',
            'MS Kualasimpang'              => 'ms-ksg',
            'MS Takengon'                  => 'ms-tkn',
            'Mahkamah Syar\'iyah Takengon' => 'ms-tkn',
            'MS Simpang Tiga Redelong'     => 'ms-str',
            'Mahkamah Syariyah Simpang Tiga Redelong' => 'ms-str',
            'MS Meulaboh'                  => 'ms-mbo',
            'Mahkamah Syar\'iyah Meulaboh'  => 'ms-mbo',
            'MS Calang'                    => 'ms-clg',
            'Mahkamah Syar\'iyah Calang'   => 'ms-clg',
            'MS Suka Makmue'               => 'ms-skm',
            'Mahkamah Syar\'iyah Suka Makmue' => 'ms-skm',
            'MS Tapaktuan'                 => 'ms-ttn',
            'Mahkamah Syar\'iyah Tapaktuan' => 'ms-ttn',
            'MS Blangpidie'                => 'ms-bpd',
            'MS BLANGPIDIE'                => 'ms-bpd',
            'MS Sinabang'                  => 'ms-snb',
            'MS Singkil'                   => 'ms-skl',
            'Mahkamah Syar\'iyah Singkil'  => 'ms-skl',
            'MS Subulussalam'              => 'ms-sub',
            'MS Kota Subulussalam'         => 'ms-sub',
            'Mahkamah Syar\'iyah Kota Subulussalam' => 'ms-sub',
            'MS Kutacane'                  => 'ms-ktc',
            'Mahkamah Syar\'iyah Kutacane' => 'ms-ktc',
            'MS Blangkejeren'              => 'ms-bkj',
            'Mahkamah Syar’iyah Blangkejeren' => 'ms-bkj',
        ];

        // 3. Eksekusi database insert / update
        foreach ($daftarSatkerRaw as $raw) {
            $namaSatker = trim($raw['nama_satker']);
            $vshort = $vshortMapping[$namaSatker] ?? null;

            if ($vshort) {
                $noPtspFormatted = $this->formatPhoneNumber($raw['no_ptsp']);
                $hpPjFormatted   = $this->formatPhoneNumber($raw['hp_pj']);

                // Bersihkan nama tampilan satker (misal "MS Banda Aceh")
                $cleanShortName = str_replace(['Mahkamah Syar\'iyah ', 'Mahkamah Syariyah '], 'MS ', $namaSatker);
                if (!str_starts_with($cleanShortName, 'MS ')) {
                    $cleanShortName = 'MS ' . $cleanShortName;
                }

                // Update / Insert data ke tabel Satker
                $satker = Satker::updateOrCreate(
                    ['satker_vshort' => $vshort],
                    [
                        'satker_name'       => "Mahkamah Syar'iyah " . str_replace(['MS ', 'Mahkamah Syar\'iyah ', 'Mahkamah Syariyah '], '', $namaSatker),
                        'satker_short_name' => $cleanShortName,
                        'whatsapp'          => $noPtspFormatted,
                        'telepon'           => null,
                        'logo'              => 'logo.png',
                    ]
                );

                // Update / Insert data ke tabel PtspDaerah
                PtspDaerah::updateOrCreate(
                    ['satker_id' => $satker->id],
                    [
                        'nama_pj'              => $raw['penanggung_jawab'],
                        'no_hp_pj'             => $hpPjFormatted,
                        'has_whatsapp_service' => ($raw['status_ptsp'] === 'Ada dan Siap Digunakan'),
                        'no_wa_layanan'        => $noPtspFormatted,
                        'is_call_able'         => ($raw['layanan_informasi'] === 'Tersedia & Aktif'),
                    ]
                );
            }
        }
    }
}