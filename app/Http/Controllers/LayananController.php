<?php

namespace App\Http\Controllers;

use App\Models\Satker;
use App\Models\JenisPerkara;
use App\Models\SyaratPerkara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LayananController extends Controller
{
    public function bukuTelepon()
    {
        // Ambil seluruh data Satker beserta relasi data PTSP Daerahnya
        $satkers = Satker::with('ptspDaerah')
            ->orderBy('satker_name', 'asc')
            ->get();

        // Format data agar sesuai dengan variabel yang dibutuhkan oleh Blade View
        $daftarSatker = $satkers->map(function ($satker) {
            return [
                'id'                   => $satker->id,
                'nama_satker'          => $satker->satker_short_name ?? $satker->satker_name,
                'satker_vshort'        => $satker->satker_vshort,
                'wilayah_kerja'        => $satker->satker_city ?? 'Provinsi Aceh',
                'no_ptsp'              => $satker->whatsapp ?? ($satker->ptspDaerah->no_wa_layanan ?? null),
                'penanggung_jawab'     => $satker->ptspDaerah->nama_pj ?? '-',
                'hp_pj'                => $satker->ptspDaerah->no_hp_pj ?? '-',
                'status_ptsp'          => ($satker->ptspDaerah->has_whatsapp_service ?? false) 
                                        ? 'Ada dan Siap Digunakan' 
                                        : 'Belum Siap / Ada Kendala',
                'has_whatsapp_service' => $satker->ptspDaerah->has_whatsapp_service ?? false, // <-- Tambahkan ini
                'is_call_able'         => $satker->ptspDaerah->is_call_able ?? false,
            ];
        });

        return view('Pages.Layanan.bukutelepon', compact('daftarSatker'));
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