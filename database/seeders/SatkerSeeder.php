<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Satker;

class SatkerSeeder extends Seeder
{
    public function run(): void
    {
        $satkers = [
            // Tingkat Banding (Provinsi)
            [
                'satker_name'       => "Mahkamah Syar'iyah Aceh",
                'satker_short_name' => "MS Aceh",
                'satker_vshort'     => "ms-aceh",
            ],
            // Tingkat Pertama (Kabupaten/Kota)
            [
                'satker_name'       => "Mahkamah Syar'iyah Banda Aceh",
                'satker_short_name' => "MS Banda Aceh",
                'satker_vshort'     => "ms-bna",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Sabang",
                'satker_short_name' => "MS Sabang",
                'satker_vshort'     => "ms-sbg",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Jantho",
                'satker_short_name' => "MS Jantho",
                'satker_vshort'     => "ms-jth",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Sigli",
                'satker_short_name' => "MS Sigli",
                'satker_vshort'     => "ms-sgl",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Meureudu",
                'satker_short_name' => "MS Meureudu",
                'satker_vshort'     => "ms-mrd",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Bireuen",
                'satker_short_name' => "MS Bireuen",
                'satker_vshort'     => "ms-bir",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Lhokseumawe",
                'satker_short_name' => "MS Lhokseumawe",
                'satker_vshort'     => "ms-lsm",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Lhoksukon",
                'satker_short_name' => "MS Lhoksukon",
                'satker_vshort'     => "ms-lsn",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Langsa",
                'satker_short_name' => "MS Langsa",
                'satker_vshort'     => "ms-lgs",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Idi",
                'satker_short_name' => "MS Idi",
                'satker_vshort'     => "ms-idi",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Kualasimpang",
                'satker_short_name' => "MS Kualasimpang",
                'satker_vshort'     => "ms-ksg",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Takengon",
                'satker_short_name' => "MS Takengon",
                'satker_vshort'     => "ms-tkn",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Simpang Tiga Redelong",
                'satker_short_name' => "MS Simpang Tiga Redelong",
                'satker_vshort'     => "ms-str",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Meulaboh",
                'satker_short_name' => "MS Meulaboh",
                'satker_vshort'     => "ms-mbo",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Calang",
                'satker_short_name' => "MS Calang",
                'satker_vshort'     => "ms-clg",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Suka Makmue",
                'satker_short_name' => "MS Suka Makmue",
                'satker_vshort'     => "ms-skm",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Tapaktuan",
                'satker_short_name' => "MS Tapaktuan",
                'satker_vshort'     => "ms-ttn",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Blangpidie",
                'satker_short_name' => "MS Blangpidie",
                'satker_vshort'     => "ms-bpd",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Sinabang",
                'satker_short_name' => "MS Sinabang",
                'satker_vshort'     => "ms-snb",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Singkil",
                'satker_short_name' => "MS Singkil",
                'satker_vshort'     => "ms-skl",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Subulussalam",
                'satker_short_name' => "MS Subulussalam",
                'satker_vshort'     => "ms-sub",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Kutacane",
                'satker_short_name' => "MS Kutacane",
                'satker_vshort'     => "ms-ktc",
            ],
            [
                'satker_name'       => "Mahkamah Syar'iyah Blangkejeren",
                'satker_short_name' => "MS Blangkejeren",
                'satker_vshort'     => "ms-bkp",
            ],
        ];

        foreach ($satkers as $data) {
            Satker::updateOrCreate(
                ['satker_vshort' => $data['satker_vshort']], // Acuan pencarian data
                [
                    'satker_name'       => $data['satker_name'],
                    'satker_short_name' => $data['satker_short_name'],
                    'logo'              => 'logo.png',
                ]
            );
        }
    }
}