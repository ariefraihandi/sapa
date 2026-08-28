<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pekerjaan;

class PekerjaanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Akuntan', 'Anggota BPK', 'Anggota DPD', 'Anggota DPR RI', 'Anggota DPRD Kab/Kota',
            'Anggota DPRD Propinsi', 'Anggota Kabinet /Kementerian', 'Anggota Mahkamah Konstitusi',
            'Apoteker', 'Arsitek', 'Belum/tidak bekerja', 'Bidan', 'Bupati', 'Buruh harian lepas',
            'Buruh nelayan / perikanan', 'Buruh peternakan', 'Buruh tani / perkebunan', 'Dokter',
            'Dosen', 'Duta Besar', 'Gubernur', 'Guru', 'Imam masjid', 'Industri', 'Juru masak',
            'Karyawan BUMD', 'Karyawan BUMN', 'Karyawan Honorer', 'Karyawan swasta', 'Kepala Desa',
            'Kepolisian RI', 'Konstruksi', 'Konsultan', 'Mekanik', 'Mengurus rumah tangga',
            'Nelayan/perikanan', 'Notaris', 'Paraji', 'Paranormal', 'Pastur', 'Pedagang',
            'Pegawai Negeri Sipil', 'Pejabat Negara', 'Pelajar/Mahasiswa', 'Pelaut',
            'Pembantu rumah tangga', 'Penata busana', 'Penata rambut', 'Penata rias', 'Pendeta',
            'Peneliti', 'Pengacara', 'Pensiun', 'Penterjemah', 'Penyiar radio', 'Penyiar televisi',
            'Perancang busana', 'Perangkat Desa', 'Perawat', 'Perdagangan', 'Petani/pekebun',
            'Peternak', 'Pialang', 'Pilot', 'Presiden', 'Promotor acara', 'Psikiater/psikolog',
            'Seniman', 'Sopir', 'Tabib', 'Tentara Nasional Indonesia', 'Transportasi',
            'Tukang jahit', 'Tukang batu', 'Tukang cukur', 'Tukang gigi', 'Tukang kayu',
            'Tukang las/pandai besi', 'Tukang listrik', 'Tukang sol sepatu', 'Ustadz/mubaligh',
            'Wakil Bupati', 'Wakil Gubernur', 'Wakil Presiden', 'Wakil Walikota', 'Walikota',
            'Wartawan', 'Lainnya'
        ];

        foreach ($data as $item) {
            Pekerjaan::firstOrCreate(['nama_pekerjaan' => $item]);
        }
    }
}