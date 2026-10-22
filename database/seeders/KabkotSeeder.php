<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Satker;
use App\Models\Kabkot;

class KabkotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pemetaan Kabupaten/Kota di Aceh ke satker_vshort Mahkamah Syar'iyah
        $mapping = [
            'ms-bna' => ['Kota Banda Aceh'],
            'ms-jth' => ['Kabupaten Aceh Besar'],
            'ms-sgl' => ['Kabupaten Pidie'],
            'ms-mrd' => ['Kabupaten Pidie Jaya'],
            'ms-bir' => ['Kabupaten Bireuen'],
            'ms-lsm' => ['Kota Lhokseumawe'],
            'ms-lsk' => ['Kabupaten Aceh Utara'],
            'ms-idi' => ['Kabupaten Aceh Timur'],
            'ms-lgs' => ['Kota Langsa'],
            'ms-ksg' => ['Kabupaten Aceh Tamiang'],
            'ms-tkn' => ['Kabupaten Aceh Tengah'],
            'ms-str' => ['Kabupaten Bener Meriah'],
            'ms-bkj' => ['Kabupaten Gayo Lues'],
            'ms-ktc' => ['Kabupaten Aceh Tenggara'],
            'ms-mbo' => ['Kabupaten Aceh Barat'],
            'ms-skm' => ['Kabupaten Nagan Raya'],
            'ms-bpd' => ['Kabupaten Aceh Barat Daya'],
            'ms-ttn' => ['Kabupaten Aceh Selatan'],
            'ms-sub' => ['Kota Subulussalam'],
            'ms-skl' => ['Kabupaten Aceh Singkil'],
            'ms-snb' => ['Kabupaten Simeulue'],
            'ms-sbg' => ['Kota Sabang'],
            'ms-clg' => ['Kabupaten Aceh Jaya'],
            
            // MS Aceh (Tingkat Banding/Provinsi) - opsional jika ada warga luar provinsi
            'ms-aceh' => ['Luar Provinsi Aceh / Lainnya'],
        ];

        foreach ($mapping as $vshort => $kabkots) {
            $satker = Satker::where('satker_vshort', $vshort)->first();

            if ($satker) {
                foreach ($kabkots as $namaKabkot) {
                    Kabkot::firstOrCreate(
                        ['nama_kabkot' => $namaKabkot],
                        ['satker_id'   => $satker->id]
                    );
                }
            }
        }
    }
}