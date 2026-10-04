<?php

namespace App\Http\Controllers;

use App\Models\Satker;
use App\Models\JenisPerkara;
use App\Models\SyaratPerkara;
use App\Models\PengunjungPtsp;
use App\Models\Pekerjaan;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use PDF;
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

        // META SEO OPTIMIZED (Bahasa Ramah Masyarakat Awam)
        $seo = [
            'title'       => 'Pusat Bantuan & WhatsApp Resmi Mahkamah Syar\'iyah Se-Aceh - SAPA MS ACEH',
            'description' => 'Hubungi langsung petugas PTSP Mahkamah Syar\'iyah seluruh Aceh via WhatsApp atau Telepon. Konsultasi gratis syarat gugatan, informasi perkara, konsultasi hukum, dan jadwal sidang.',
            'keywords'    => 'nomor whatsapp mahkamah syariyah, wa ptsp pengadilan agama aceh, konsultasi perceraian aceh, call center ms aceh, kontak mahkamah syariyah, syarat gugat cerai aceh, bantuan hukum gratis aceh',
            'image'       => asset('assets/images/logo/sapa.png'),
            'author'      => 'Mahkamah Syar\'iyah Aceh',
        ];

        return view('Pages.Layanan.bukutelepon', compact('daftarSatker', 'pekerjaans', 'seo'));
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

        // OPTIMIZED META SEO (Targeting Search Intent Masyarakat Awam)
        $seo = [
            'title'       => 'Syarat Pengajuan Perkara & Syarat Cerai Mahkamah Syar\'iyah Se-Aceh - SAPA MS ACEH',
            'description' => 'Panduan lengkap dan syarat dokumen pengajuan perkara (Cerai Talak, Cerai Gugat, Isbat Nikah, Waris, Hak Asuh Anak, & Poligami) di Mahkamah Syar\'iyah seluruh Aceh.',
            'keywords'    => 'syarat gugat cerai aceh, syarat cerai talak, syarat isbat nikah, berkas perkara pengadilan agama, persyataan pengajuan perkara, ptsp mahkamah syariyah, konsultasi hukum aceh',
            'image'       => asset('assets/images/logo/sapa.png'),
            'author'      => 'Mahkamah Syar\'iyah Aceh',
        ];

        return view('Pages.Layanan.persyaratan', compact('daftarSatker', 'seo'));
    }

    public function downloadPersyaratanPdf($satker_vshort, $jenis_perkara_id)
    {
        // 1. Ambil data Satker
        $satker = Satker::where('satker_vshort', $satker_vshort)->firstOrFail();

        // 2. Ambil data Jenis Perkara
        $jenisPerkara = JenisPerkara::findOrFail($jenis_perkara_id);

        // 3. Ambil daftar syarat dokumen spesifik untuk satker & jenis perkara ini
        $dokumenList = SyaratPerkara::where('satker_id', $satker->id)
                        ->where('jenis_perkara_id', $jenis_perkara_id)
                        ->get();

        // 4. Render ke PDF
        $pdf = PDF::loadView('Pages.Layanan.pdf_persyaratan', [
            'satker'       => $satker,
            'jenisPerkara' => $jenisPerkara,
            'dokumenList'  => $dokumenList
        ], [], [
            'format'        => 'A4',
            'orientation'   => 'P',
            'margin_left'   => 0,  // Kop menempel full ke kiri kertas
            'margin_right'  => 0,  // Kop menempel full ke kanan kertas
            'margin_top'    => 0,  // Kop menempel paling atas kertas
            'margin_bottom' => 15,
        ]);

        $filename = 'Persyaratan_' . str_replace(' ', '_', $jenisPerkara->nama_layanan) . '_' . $satker->satker_vshort . '.pdf';

        return $pdf->stream($filename);
    }

    public function detailPersyaratanPerkara($satker_vshort)
    {
        // 1. Cari Satker berdasarkan kode vshort atau ID
        $satker = Satker::where('satker_vshort', $satker_vshort)
            ->orWhere('id', $satker_vshort)
            ->firstOrFail();

        // Penentuan Nama Satker yang Konsisten
        $namaSatker = $satker->satker_name ?? $satker->nama_satker;
        $kotaSatker = $satker->satker_city ?? 'Aceh';

        // 2. Optimization: Ambil Jenis Perkara beserta Syarat Approved milik Satker ini (Mencegah N+1 Query)
        $jenisPerkaraGrouped = JenisPerkara::whereHas('syaratPerkara', function ($query) use ($satker) {
                $query->where('satker_id', $satker->id)
                    ->where('is_approved', 1)
                    ->where('is_active', 1);
            })
            ->with(['syaratPerkara' => function ($query) use ($satker) {
                $query->where('satker_id', $satker->id)
                    ->where('is_approved', 1)
                    ->where('is_active', 1);
            }])
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_layanan', 'asc')
            ->get()
            ->map(function ($jenis) {
                return (object) [
                    'jenisPerkara' => $jenis,
                    'dokumenList'  => $jenis->syaratPerkara,
                    'is_tayang'    => true
                ];
            });

        // 3. META SEO DYNAMIC & LOCAL TARGETING (Spesifik per Kabupaten/Kota)
        $seo = [
            'title'       => "Syarat Gugatan & Berkas Perkara {$namaSatker} - SAPA MS ACEH",
            'description' => "Daftar syarat lengkap pengajuan perkara cerai, isbat nikah, waris, dan hak asuh anak di {$namaSatker} ({$kotaSatker}). Unduh dan cek berkas PTSP resmi.",
            'keywords'    => "syarat cerai " . strtolower($namaSatker) . ", syarat gugatan " . strtolower($kotaSatker) . ", berkas perkara pengadilan, ptsp " . strtolower($namaSatker) . ", mahkamah syar'iyah " . strtolower($kotaSatker) . ", syarat isbat nikah",
            'image'       => asset('assets/images/logo/sapa.png'),
            'author'      => $namaSatker,
        ];

        return view('Pages.Layanan.persyaratan_detail', compact('satker', 'jenisPerkaraGrouped', 'seo'));
    }   
    public function singlePersyaratanPerkara($satker_vshort, $jenis_perkara_id)
    {
        // 1. Ambil Data Satker
        $satker = Satker::where('satker_vshort', $satker_vshort)
            ->orWhere('id', $satker_vshort)
            ->firstOrFail();

        // 2. Ambil Data Jenis Perkara
        $jenisPerkara = JenisPerkara::findOrFail($jenis_perkara_id);

        // 3. Ambil Daftar Dokumen Syarat yang Approved & Active
        $dokumenList = SyaratPerkara::where('satker_id', $satker->id)
            ->where('jenis_perkara_id', $jenis_perkara_id)
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->get();

        // Variable Helper untuk SEO Text
        $namaLayanan = $jenisPerkara->nama_layanan;
        $namaSatker  = $satker->satker_name ?? $satker->nama_satker;
        $kotaSatker  = $satker->satker_city ?? 'Aceh';

        // 4. DYNAMIC META SEO (Targeted Long-Tail Keywords)
        $seo = [
            'title'       => "Syarat {$namaLayanan} di {$namaSatker} - SAPA MS ACEH",
            'description' => "Panduan resmi dokumen & syarat pengajuan {$namaLayanan} di {$namaSatker} ({$kotaSatker}). Cek kelengkapan berkas PTSP sebelum pendaftaran perkara.",
            'keywords'    => "syarat " . strtolower($namaLayanan) . ", berkas " . strtolower($namaLayanan) . ", " . strtolower($namaSatker) . ", ptsp " . strtolower($kotaSatker) . ", pengajuan " . strtolower($namaLayanan) . " aceh",
            'image'       => asset('assets/images/logo/sapa.png'),
            'author'      => $namaSatker,
        ];

        return view('Pages.Layanan.persyaratan_single', compact('satker', 'jenisPerkara', 'dokumenList', 'seo'));
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
            'nik'             => $request->nik ?? null, // <-- AMAN: Diisi null jika tidak dikirim dari JS
            'no_hp'           => $request->no_hp,
            'email'           => $request->email,
            'jenis_kelamin'   => $request->jenis_kelamin,
            'usia'            => $request->usia,
            'pekerjaan'       => $request->pekerjaan,
            'pendidikan'      => $request->pendidikan,
            'keperluan'       => $request->keperluan,
            'is_tindak_lanjut' => false,
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