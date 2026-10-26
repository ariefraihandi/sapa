<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PengunjungPtsp;
use App\Models\Pengaduan;
use App\Models\JenisPerkara;
use App\Models\SyaratPerkara;
use App\Models\Satker;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $title = 'Dashboard Utama';

        // Pengecekan Hak Akses Admin / MS Aceh
        $satkerName = $user->satker->satker_short_name ?? ($user->satker->satker_name ?? '');
        $isMsAceh = ($user->role === 'admin') || 
                    str_contains(strtolower($satkerName), 'mahkamah syar\'iyah aceh') || 
                    str_contains(strtolower($satkerName), 'ms aceh');

        // Query Base
        $pengunjungQuery = PengunjungPtsp::query();
        $pengaduanQuery  = Pengaduan::query();

        // Filter Satker jika User Daerah
        if (!$isMsAceh && $user->satker_id) {
            $pengunjungQuery->where('satker_id', $user->satker_id);
            $pengaduanQuery->where('satker_id', $user->satker_id);
        }

        // ================= 1. METRIK SUMMARY CARDS =================
        $totalPengunjung  = (clone $pengunjungQuery)->count();
        $totalPengaduan   = (clone $pengaduanQuery)->count();
        $pengaduanSelesai = (clone $pengaduanQuery)->where('is_tindak_lanjut', true)->count();
        $persenPengaduan  = $totalPengaduan > 0 ? round(($pengaduanSelesai / $totalPengaduan) * 100) : 0;

        // ================= 2. DATA CHART BULANAN =================
        $chartBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $currentYear = date('Y');

        $pengunjungPerBulanRaw = (clone $pengunjungQuery)
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan')
            ->toArray();

        $pengaduanPerBulanRaw = (clone $pengaduanQuery)
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan')
            ->toArray();

        $dataPengunjungPerBulan = [];
        $dataPengaduanPerBulan  = [];

        for ($i = 1; $i <= 12; $i++) {
            $dataPengunjungPerBulan[] = $pengunjungPerBulanRaw[$i] ?? 0;
            $dataPengaduanPerBulan[]  = $pengaduanPerBulanRaw[$i] ?? 0;
        }

        // Statistik Layanan (Pesan vs Telepon)
        $layananPesan   = (clone $pengunjungQuery)->where('jenis_layanan', 'pesan')->count();
        $layananTelepon = (clone $pengunjungQuery)->where('jenis_layanan', 'telepon')->count();

        // ================= 3. REKAP DATA PER SATKER =================
        $satkerListQuery = Satker::orderBy('satker_name', 'asc');
        if (!$isMsAceh && $user->satker_id) {
            $satkerListQuery->where('id', $user->satker_id);
        }
        $satkers = $satkerListQuery->get();

        // Total Master Jenis Perkara
        $totalJenisPerkaraMaster = JenisPerkara::count();

        // Aggregate Syarat Perkara per Satker
        $syaratPerSatkerRaw = SyaratPerkara::with('jenisPerkara')
            ->get()
            ->groupBy('satker_id');

        $rekapSatker = $satkers->map(function ($satker) use ($syaratPerSatkerRaw, $totalJenisPerkaraMaster) {
            // A. Pengunjung Fisik vs SAPA Online (Pesan & Telepon)
            $pengunjungFisik = PengunjungPtsp::where('satker_id', $satker->id)
                ->where('jenis_layanan', 'tatap_muka')
                ->count();

            $penggunaSapa = PengunjungPtsp::where('satker_id', $satker->id)
                ->whereIn('jenis_layanan', ['pesan', 'telepon'])
                ->count();

            // B. Cek Kesiapan Jenis Perkara Aktif
            $syaratItems = $syaratPerSatkerRaw->get($satker->id, collect());
            $jenisPerkaraGrouped = $syaratItems->groupBy('jenis_perkara_id');

            $jenisPerkaraAktifList = [];
            $totalJenisPerkaraAktif = 0;

            foreach ($jenisPerkaraGrouped as $jenisId => $items) {
                $totalDocs = $items->count();
                $totalAktif = $items->where('is_active', 1)->count();
                $first = $items->first();

                if ($totalAktif > 0 && $first && $first->jenisPerkara) {
                    $totalJenisPerkaraAktif++;
                    $jenisPerkaraAktifList[] = (object) [
                        'id' => $jenisId,
                        'nama_layanan' => $first->jenisPerkara->nama_layanan,
                        'kategori' => $first->jenisPerkara->kategori ?? 'Lainnya',
                        'total_dokumen' => $totalDocs,
                        'total_aktif' => $totalAktif,
                        'is_full' => ($totalDocs === $totalAktif)
                    ];
                }
            }

            return (object) [
                'satker_id'                => $satker->id,
                'satker_name'              => $satker->satker_name,
                'satker_short_name'        => $satker->satker_short_name ?? $satker->satker_name,
                'satker_vshort'            => $satker->satker_vshort,
                'pengunjung_fisik'         => $pengunjungFisik,
                'pengguna_sapa'            => $penggunaSapa,
                'total_interaksi'          => $pengunjungFisik + $penggunaSapa,
                'total_jenis_perkara_aktif' => $totalJenisPerkaraAktif,
                'total_jenis_perkara_master'=> $totalJenisPerkaraMaster,
                'jenis_perkara_aktif_list' => $jenisPerkaraAktifList,
            ];
        });

        // Data khusus untuk Chart Penggunaan SAPA Per Satker
        $categoriesSatker          = $rekapSatker->pluck('satker_short_name')->toArray();
        $dataPengunjungFisikSatker = $rekapSatker->pluck('pengunjung_fisik')->toArray();
        $dataPenggunaSapaSatker    = $rekapSatker->pluck('pengguna_sapa')->toArray();

        // RECENT LIST
        $recentPengunjung = (clone $pengunjungQuery)->with('satker')->latest()->take(5)->get();

        $seo = [
            'title'       => "Dashboard Utama | SAPA MS ACEH",
            'description' => "Monitoring layanan PTSP dan kesiapan syarat perkara Mahkamah Syar'iyah.",
            'keywords'    => "dashboard sapa, monitoring ptsp, syarat perkara ms aceh",
            'robots'      => 'noindex, nofollow',
            'author'      => $satkerName ?: 'Mahkamah Syar\'iyah Aceh',
        ];

        return view('Pages.Dashboard.index', compact(
            'title',
            'isMsAceh',
            'satkerName',
            'totalPengunjung',
            'totalPengaduan',
            'pengaduanSelesai',
            'persenPengaduan',
            'chartBulan',
            'dataPengunjungPerBulan',
            'dataPengaduanPerBulan',
            'layananPesan',
            'layananTelepon',
            'recentPengunjung',
            'rekapSatker',
            'categoriesSatker',
            'dataPengunjungFisikSatker',
            'dataPenggunaSapaSatker',
            'seo'
        ));
    }
}