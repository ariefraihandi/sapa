<?php

namespace App\Http\Controllers;

use App\Models\Satker;
use App\Models\JenisPerkara;
use App\Models\SyaratPerkara;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function bukuTelepon()
    {
        // Data direktori Mahkamah Syar'iyah Se-Aceh
        $daftarSatker = [
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
                'hp_pj'              => 'Faisal Reza (085260026237), Mukhsin Sardi (085277574246)',
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
                'timestamp'          => '28/07/2026 11:09:52',
                'nama_satker'        => 'MS Jantho',
                'wilayah_kerja'      => 'Kabupaten Aceh Besar',
                'penanggung_jawab'   => 'Akmal Hakim BS dan Sufriadi',
                'hp_pj'              => '08126971651 dan 081362535020',
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
                'timestamp'          => '28/07/2026 11:31:59',
                'nama_satker'        => 'MS Lhoksukon',
                'wilayah_kerja'      => 'Kabupaten Aceh Utara',
                'penanggung_jawab'   => 'Fadhlullah, S. H.',
                'hp_pj'              => '081360133524',
                'status_pemasangan'  => 'Belum',
                'no_ptsp'            => 'Ada dan Siap Digunakan',
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

        // Kirim variabel $daftarSatker ke view
        return view('Pages.Bukutamu.index', compact('daftarSatker'));
    }

    public function persyaratanPerkara()
    {
        $satkers = Satker::orderBy('satker_name', 'asc')->get();

        $daftarSatker = $satkers->map(function ($s) {
            return [
                'id'            => $s->id,
                'nama_satker'   => $s->satker_name,
                'satker_vshort' => $s->satker_vshort,
                'wilayah_kerja' => $s->satker_city ?? 'Provinsi Aceh',
            ];
        });

        return view('Pages.Layanan.persyaratan', compact('daftarSatker'));
    }

    public function detailPersyaratanPerkara($satker_vshort)
    {
        // 1. Cari Satker berdasarkan kode vshort atau ID
        $satker = Satker::where('satker_vshort', $satker_vshort)
            ->orWhere('id', $satker_vshort)
            ->firstOrFail();

        // 2. Ambil SELURUH Jenis Perkara
        $jenisPerkaraList = JenisPerkara::orderBy('kategori', 'asc')
            ->orderBy('nama_layanan', 'asc')
            ->get();

        // 3. Mapping Jenis Perkara & Ambil Syarat yang Approved milik Satker ini
        $jenisPerkaraGrouped = $jenisPerkaraList->map(function ($jenis) use ($satker) {
            // Ambil dokumen syarat khusus Satker ini & yang statusnya aktif/approved
            $dokumenList = SyaratPerkara::where('satker_id', $satker->id)
                ->where('jenis_perkara_id', $jenis->id)
                ->where('is_approved', 1)
                ->where('is_active', 1)
                ->get();

            return (object) [
                'jenisPerkara' => $jenis,
                'dokumenList'  => $dokumenList,
                'is_tayang'    => $dokumenList->count() > 0 // Hanya tayang jika ada dokumen valid
            ];
        })->filter(function ($item) {
            // Filter hanya Jenis Perkara yang MEMILIKI dokumen tayang/approved
            return $item->is_tayang;
        });

        return view('Pages.Layanan.persyaratan_detail', compact('satker', 'jenisPerkaraGrouped'));
    }

}