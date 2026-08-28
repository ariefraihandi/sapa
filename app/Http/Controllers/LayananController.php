<?php

namespace App\Http\Controllers;

use App\Models\Satker;
use App\Models\JenisPerkara;
use App\Models\SyaratPerkara;
use App\Models\PengunjungPtsp;
use App\Models\Pekerjaan;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LayananController extends Controller
{
    public function bukuTelepon()
    {
        $satkers = Satker::with('ptspDaerah')
            ->orderBy('satker_name', 'asc')
            ->get();

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
                'has_whatsapp_service' => $satker->ptspDaerah->has_whatsapp_service ?? false,
                'is_call_able'         => $satker->ptspDaerah->is_call_able ?? false,
            ];
        });

        // Ambil hanya nama pekerjaannya saja
        $pekerjaans = Pekerjaan::orderBy('nama_pekerjaan', 'asc')->pluck('nama_pekerjaan');

        return view('Pages.Layanan.bukutelepon', compact('daftarSatker', 'pekerjaans'));
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

    public function storePengunjung(Request $request)
    {
        $request->validate([
            'satker_id'      => 'required|exists:satkers,id',
            'jenis_layanan'  => 'required|in:pesan,telepon',
            'nama_responden' => 'required|string|max:255',
            'nik'            => 'nullable|string|max:16',
            'no_hp'          => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'jenis_kelamin'  => 'required|in:L,P',
            'usia'           => 'nullable|string|max:50',
            'pekerjaan'      => 'nullable|string|max:100',
            'pendidikan'     => 'nullable|string|max:50',
            'keperluan'      => 'nullable|string',
        ]);

        $pengunjung = PengunjungPtsp::create([
            'satker_id'       => $request->satker_id,
            'jenis_layanan'   => $request->jenis_layanan,
            'nama_responden'  => $request->nama_responden,
            'nik'             => $request->nik,
            'no_hp'           => $request->no_hp,
            'email'           => $request->email,
            'jenis_kelamin'   => $request->jenis_kelamin,
            'usia'            => $request->usia,
            'pekerjaan'       => $request->pekerjaan,
            'pendidikan'      => $request->pendidikan,
            'keperluan'       => $request->keperluan,
            'is_tindak_lanjut'=> false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pengunjung berhasil disimpan.',
            'data'    => $pengunjung
        ]);
    }

    public function storePengaduan(Request $request)
    {
        $request->validate([
            'satker_id'         => 'required|exists:satkers,id',
            'nama_pelapor'      => 'required|string|max:255',
            'no_hp'             => 'required|string|max:20',
            'nik'               => 'nullable|string|max:16',
            'uaraian_pengaduan' => 'required|string',
        ]);

        $pengaduan = Pengaduan::create([
            'satker_id'         => $request->satker_id,
            'nama_pelapor'      => $request->nama_pelapor,
            'no_hp'             => $request->no_hp,
            'nik'               => $request->nik,
            'uaraian_pengaduan' => $request->uaraian_pengaduan,
            'is_tindak_lanjut'  => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan pengaduan berhasil terkirim.',
            'data'    => $pengaduan
        ]);
    }
}