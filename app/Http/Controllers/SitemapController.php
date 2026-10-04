<?php

namespace App\Http\Controllers;

use App\Models\Satker;
use App\Models\JenisPerkara;
use App\Models\SyaratPerkara;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        // 1. URL Statis Publik
        $staticUrls = [
            route('buku-tamu-s'),
            route('buku-tamu'),
            route('public.persyaratan-perkara'),
        ];

        // 2. Data Satker untuk URL Dinamis
        $satkers = Satker::orderBy('satker_name', 'asc')->get();

        // 3. Mengambil Pasangan Satker & Jenis Perkara yang memiliki Syarat Aktif (Approved)
        $syaratPerkaraActive = SyaratPerkara::where('is_approved', 1)
            ->where('is_active', 1)
            ->select('satker_id', 'jenis_perkara_id')
            ->groupBy('satker_id', 'jenis_perkara_id')
            ->get();

        // Render view XML dengan header 'text/xml'
        $content = view('sitemap', compact('staticUrls', 'satkers', 'syaratPerkaraActive'))->render();

        return response($content, 200)->header('Content-Type', 'text/xml');
    }
}