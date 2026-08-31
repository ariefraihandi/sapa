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
        $user = Auth::user();
        $title = 'Dashboard Utama';

        // Pengecekan Hak Akses Admin / MS Aceh
        $satkerName = $user->satker->satker_name ?? '';
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
        $totalPengunjung = (clone $pengunjungQuery)->count();
        $totalPengaduan  = (clone $pengaduanQuery)->count();
        $pengaduanSelesai = (clone $pengaduanQuery)->where('is_tindak_lanjut', true)->count();
        $totalSyarat     = (clone $syaratQuery)->count();

        // Persentase Tindak Lanjut Pengaduan
        $persenPengaduan = $totalPengaduan > 0 ? round(($pengaduanSelesai / $totalPengaduan) * 100) : 0;

        // ================= 2. DATA CHART (BULANAN TH 2026) =================
        $chartBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $dataPengunjungPerBulan = [];
        $dataPengaduanPerBulan  = [];

        for ($i = 1; $i <= 12; $i++) {
            $dataPengunjungPerBulan[] = (clone $pengunjungQuery)->whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->count();
            $dataPengaduanPerBulan[]  = (clone $pengaduanQuery)->whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->count();
        }

        // Statistik Layanan (Pesan vs Telepon)
        $layananPesan   = (clone $pengunjungQuery)->where('jenis_layanan', 'pesan')->count();
        $layananTelepon = (clone $pengunjungQuery)->where('jenis_layanan', 'telepon')->count();

        // ================= 3. RECENT LIST =================
        $recentPengunjung = (clone $pengunjungQuery)->with('satker')->latest()->take(5)->get();
        $recentPengaduan  = (clone $pengaduanQuery)->with('satker')->latest()->take(5)->get();

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
            'recentPengaduan'
        ));
    }
}