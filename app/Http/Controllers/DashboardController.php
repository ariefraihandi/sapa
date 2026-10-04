<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PengunjungPtsp;
use App\Models\Pengaduan;
use App\Models\JenisPerkara;
use App\Models\SyaratPerkara;

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
        $syaratQuery     = SyaratPerkara::query();

        // Filter Satker jika User Daerah
        if (!$isMsAceh && $user->satker_id) {
            $pengunjungQuery->where('satker_id', $user->satker_id);
            $pengaduanQuery->where('satker_id', $user->satker_id);
            $syaratQuery->where('satker_id', $user->satker_id);
        }

        // ================= 1. METRIK SUMMARY CARDS =================
        $totalPengunjung  = (clone $pengunjungQuery)->count();
        $totalPengaduan   = (clone $pengaduanQuery)->count();
        $pengaduanSelesai = (clone $pengaduanQuery)->where('is_tindak_lanjut', true)->count();
        $totalSyarat      = (clone $syaratQuery)->count();

        // Persentase Tindak Lanjut Pengaduan
        $persenPengaduan = $totalPengaduan > 0 ? round(($pengaduanSelesai / $totalPengaduan) * 100) : 0;

        // ================= 2. DATA CHART (BULANAN TH SEKARANG) =================
        $chartBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $currentYear = date('Y');

        // Optimasi Query Chart: Ambil Agregat per Bulan sekaligus (Mencegah Loop 24x Query)
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

        // ================= 3. RECENT LIST =================
        $recentPengunjung = (clone $pengunjungQuery)->with('satker')->latest()->take(5)->get();
        $recentPengaduan  = (clone $pengaduanQuery)->with('satker')->latest()->take(5)->get();

        // DYNAMIC META SEO UNTUK DASHBOARD UTAMA
        $namaSatkerText = $satkerName ?: 'Mahkamah Syar\'iyah Aceh';

        $seo = [
            'title'       => "Dashboard - {$namaSatkerText} | SAPA MS ACEH",
            'description' => "Ringkasan metrik statistik pengunjung PTSP, laporan pengaduan, dan rekapitulasi layanan pada {$namaSatkerText}.",
            'keywords'    => "dashboard sapa ms, statistik ptsp, monitoring layanan hukum, rekap pengaduan ms aceh",
            'robots'      => 'noindex, nofollow', // Halaman ringkasan eksekutif internal admin, wajib noindex
            'author'      => $namaSatkerText,
        ];

        return view('Pages.Dashboard.index', compact(
            'title',
            'isMsAceh',
            'satkerName',
            'totalPengunjung',
            'totalPengaduan',
            'pengaduanSelesai',
            'persenPengaduan',
            'totalSyarat',
            'chartBulan',
            'dataPengunjungPerBulan',
            'dataPengaduanPerBulan',
            'layananPesan',
            'layananTelepon',
            'recentPengunjung',
            'recentPengaduan',
            'seo'
        ));
    }
}