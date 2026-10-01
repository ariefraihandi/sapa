<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengunjungPtsp;
use App\Models\Pengaduan;
use App\Models\Satker;
use App\Models\Pekerjaan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PtspWidgetController extends Controller
{
    /**
     * Helper privat untuk memvalidasi domain pengirim request
     */
    private function validateDomain(Request $request, $satkerId)
    {
        $satker = Satker::find($satkerId);

        if (!$satker) {
            return [
                'valid'   => false,
                'message' => 'Satker tidak ditemukan.'
            ];
        }

        if (empty($satker->website)) {
            return [
                'valid'   => false,
                'message' => 'Akses Ditolak: Website resmi untuk satker ini belum didaftarkan di sistem.'
            ];
        }

        $originHeader = $request->headers->get('referer') ?? $request->headers->get('origin');

        if (!$originHeader) {
            return [
                'valid'   => false,
                'message' => 'Akses ditolak: Sumber domain (Referer/Origin) tidak terdeteksi.'
            ];
        }

        $getHost = function ($url) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "http://" . $url;
            }
            $host = parse_url($url, PHP_URL_HOST);
            return preg_replace('/^www\./i', '', strtolower($host ?? ''));
        };

        $allowedHost = $getHost($satker->website);
        $requestHost = $getHost($originHeader);

        if ($allowedHost !== $requestHost) {
            return [
                'valid'   => false,
                'message' => 'Akses Widget Ditolak: Widget ini dipasang di domain (' . $requestHost . '), sedangkan terdaftar untuk (' . $allowedHost . ').'
            ];
        }

        return [
            'valid'  => true,
            'satker' => $satker
        ];
    }

    public function storePengunjung(Request $request)
    {
        try {
            $domainCheck = $this->validateDomain($request, $request->satker_id);
            if (!$domainCheck['valid']) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $domainCheck['message'],
                ], 403);
            }

            $validated = $request->validate([
                'satker_id'      => 'required',
                'jenis_layanan'  => 'required|in:pesan,telepon',
                'nama_responden' => 'required|string|max:255',
                'nik'            => 'nullable|string|max:16',
                'no_hp'          => 'required|string|max:20',
                'email'          => 'nullable|email|max:255',
                'jenis_kelamin'  => 'required|in:L,P',
                'usia'           => 'nullable|string|max:30',
                'pekerjaan'      => 'nullable|string|max:255',
                'pendidikan'     => 'nullable|string|max:255',
                'keperluan'      => 'required|string',
            ]);

            $validated['nik'] = $request->nik ?? null;
            $pengunjung = PengunjungPtsp::create($validated);

            $satker = $domainCheck['satker'];
            $ptspDaerah = DB::table('ptsp_daerahs')->where('satker_id', $request->satker_id)->first();
            $namaSatker = $satker->satker_name ?? $satker->satker_short_name ?? 'MS Aceh';
            
            $noWaPetugas = ($ptspDaerah && !empty($ptspDaerah->no_wa_layanan)) 
                ? $ptspDaerah->no_wa_layanan 
                : ($satker->whatsapp ?? $satker->telepon ?? '6281111111111');

            $noWaPetugas = preg_replace('/[^0-9]/', '', $noWaPetugas);
            if (str_starts_with($noWaPetugas, '0')) {
                $noWaPetugas = '62' . substr($noWaPetugas, 1);
            }

            $genderText = $pengunjung->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan';

            $pesanWa  = "Halo PTSP *" . $namaSatker . "*,\n\n";
            $pesanWa .= "Saya membutuhkan informasi/layanan:\n";
            $pesanWa .= "• *Nama:* " . $pengunjung->nama_responden . "\n";
            $pesanWa .= "• *Jenis Kelamin:* " . $genderText . "\n";            
            $pesanWa .= "• *No. HP:* " . $pengunjung->no_hp . "\n";
            $pesanWa .= "• *Keperluan:* " . $pengunjung->keperluan . "\n\n";
            $pesanWa .= "_Registrasi via Widget PTSP Online (ID: " . substr($pengunjung->id, 0, 8) . ")_";

            $targetUrl = "https://api.whatsapp.com/send?phone=" . $noWaPetugas . "&text=" . urlencode($pesanWa);

            return response()->json([
                'status'       => 'success',
                'message'      => 'Data pengunjung berhasil dicatat',
                'redirect_url' => $targetUrl,
                'phone_number' => $noWaPetugas,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('PTSP Widget Konsultasi Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem server.',
            ], 500);
        }
    }

    public function storePengaduan(Request $request)
    {
        try {
            $domainCheck = $this->validateDomain($request, $request->satker_id);
            if (!$domainCheck['valid']) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $domainCheck['message'],
                ], 403);
            }

            $validated = $request->validate([
                'satker_id'         => 'required',
                'nama_pelapor'      => 'required|string|max:255',
                'no_hp'             => 'required|string|max:20',
                'nik'               => 'nullable|string|max:16',
                'uaraian_pengaduan' => 'required|string',
            ], [
                'nama_pelapor.required'      => 'Nama pelapor wajib diisi.',
                'no_hp.required'             => 'Nomor HP/WhatsApp wajib diisi.',
                'uaraian_pengaduan.required' => 'Uraian pengaduan wajib diisi.',
            ]);

            // Simpan ke database Model Pengaduan
            $pengaduan = Pengaduan::create([
                'satker_id'         => $validated['satker_id'],
                'nama_pelapor'      => $validated['nama_pelapor'],
                'no_hp'             => $validated['no_hp'],
                'nik'               => $validated['nik'] ?? null,
                'uaraian_pengaduan' => $validated['uaraian_pengaduan'],
                'is_tindak_lanjut'  => false,
            ]);

            $satker = $domainCheck['satker'];
            $ptspDaerah = DB::table('ptsp_daerahs')->where('satker_id', $request->satker_id)->first();
            $namaSatker = $satker->satker_name ?? $satker->satker_short_name ?? 'MS Aceh';

            $noWaPetugas = ($ptspDaerah && !empty($ptspDaerah->no_wa_pengaduan)) 
                ? $ptspDaerah->no_wa_pengaduan 
                : (($ptspDaerah && !empty($ptspDaerah->no_wa_layanan)) 
                    ? $ptspDaerah->no_wa_layanan 
                    : ($satker->whatsapp ?? $satker->telepon ?? '6281111111111'));

            $noWaPetugas = preg_replace('/[^0-9]/', '', $noWaPetugas);
            if (str_starts_with($noWaPetugas, '0')) {
                $noWaPetugas = '62' . substr($noWaPetugas, 1);
            }

            // Format Pesan WhatsApp Pengaduan
            $pesanWa  = "Assalamualaikum Admin *" . $namaSatker . "*,\n\n";
            $pesanWa .= "Saya *" . $pengaduan->nama_pelapor . "* ingin menyampaikan *Pengaduan*:\n\n";
            $pesanWa .= "• *NIK:* " . ($pengaduan->nik ?? '-') . "\n";
            $pesanWa .= "• *No. HP:* " . $pengaduan->no_hp . "\n";
            $pesanWa .= "• *Uraian:* " . $pengaduan->uaraian_pengaduan . "\n\n";
            $pesanWa .= "_Laporan via Widget Pengaduan PTSP Online (ID: " . substr($pengaduan->id, 0, 8) . ")_";

            $targetUrl = "https://api.whatsapp.com/send?phone=" . $noWaPetugas . "&text=" . urlencode($pesanWa);

            return response()->json([
                'status'       => 'success',
                'message'      => 'Pengaduan berhasil dicatat',
                'redirect_url' => $targetUrl,
                'phone_number' => $noWaPetugas,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('PTSP Widget Pengaduan Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem server.',
            ], 500);
        }
    }

    public function getInitData(Request $request)
    {
        $satkerId = $request->query('satker_id');

        $domainCheck = $this->validateDomain($request, $satkerId);
        if (!$domainCheck['valid']) {
            return response()->json([
                'status'  => 'error',
                'message' => $domainCheck['message'],
            ], 403);
        }

        $satker = $domainCheck['satker'];
        $satkerName = $satker ? ($satker->satker_name ?? $satker->satker_short_name) : 'MS Aceh';

        $pekerjaanList = Pekerjaan::orderBy('nama_pekerjaan', 'asc')->pluck('nama_pekerjaan');

        return response()->json([
            'status'         => 'success',
            'satker_name'    => $satkerName,
            'pekerjaan_list' => $pekerjaanList
        ]);
    }
}