<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengunjungPtsp;
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

        // Jika kolom website belum diisi di database, tolak akses
        if (empty($satker->website)) {
            return [
                'valid'   => false,
                'message' => 'Akses Ditolak: Website resmi untuk satker ini belum didaftarkan di sistem.'
            ];
        }

        // Ambil header Referer atau Origin dari request browser
        $originHeader = $request->headers->get('referer') ?? $request->headers->get('origin');

        if (!$originHeader) {
            return [
                'valid'   => false,
                'message' => 'Akses ditolak: Sumber domain (Referer/Origin) tidak terdeteksi.'
            ];
        }

        // Helper untuk ekstrak host/domain bersih
        $getHost = function ($url) {
            // Jika url tidak mengandung scheme, tambahkan temporary scheme agar parse_url bekerja
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "http://" . $url;
            }
            $host = parse_url($url, PHP_URL_HOST);
            // Hapus www. dan port (jika ada)
            $host = preg_replace('/^www\./i', '', strtolower($host ?? ''));
            return $host;
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
            // 1. Validasi Domain/URL Website Satker
            $domainCheck = $this->validateDomain($request, $request->satker_id);
            if (!$domainCheck['valid']) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $domainCheck['message'],
                ], 403);
            }

            // 2. Validasi Input Form
            $validated = $request->validate([
                'satker_id'      => 'required',
                'jenis_layanan'  => 'required|in:pesan,telepon',
                'nama_responden' => 'required|string|max:255',
                'no_hp'          => 'required|string|max:20',
                'nik'            => 'required|numeric',
                'email'          => 'nullable|email|max:255',
                'jenis_kelamin'  => 'required|in:L,P',
                'usia'           => 'nullable|string|max:30',
                'pekerjaan'      => 'nullable|string|max:255',
                'pendidikan'     => 'nullable|string|max:255',
                'keperluan'      => 'required|string',
            ], [
                'nik.required'           => 'NIK wajib diisi.',
                'nik.numeric'            => 'NIK hanya boleh berupa angka.',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
                'jenis_kelamin.in'        => 'Pilihan jenis kelamin tidak valid.',
            ]);

            // 3. Simpan Data Pengunjung ke Database
            $pengunjung = PengunjungPtsp::create($validated);

            // 4. Ambil Data Satker & Layanan PTSP
            $satker = $domainCheck['satker'];
            $ptspDaerah = DB::table('ptsp_daerahs')->where('satker_id', $request->satker_id)->first();

            $namaSatker = $satker->satker_name ?? $satker->satker_short_name ?? 'MS Aceh';
            
            if ($ptspDaerah && !empty($ptspDaerah->no_wa_layanan)) {
                $noWaPetugas = $ptspDaerah->no_wa_layanan;
            } else {
                $noWaPetugas = $satker->whatsapp ?? $satker->telepon ?? '6281111111111';
            }

            // Sanitasi nomor HP agar format internasional
            $noWaPetugas = preg_replace('/[^0-9]/', '', $noWaPetugas);
            if (str_starts_with($noWaPetugas, '0')) {
                $noWaPetugas = '62' . substr($noWaPetugas, 1);
            }

            $genderText = $pengunjung->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan';

            // 5. Format Pesan Otomatis WhatsApp
            $pesanWa  = "Halo PTSP *" . $namaSatker . "*,\n\n";
            $pesanWa .= "Saya membutuhkan informasi/layanan:\n";
            $pesanWa .= "• *Nama:* " . $pengunjung->nama_responden . "\n";
            $pesanWa .= "• *Jenis Kelamin:* " . $genderText . "\n";
            $pesanWa .= "• *NIK:* " . $pengunjung->nik . "\n";
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
            Log::error('PTSP Widget Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem server.',
            ], 500);
        }
    }

    public function getInitData(Request $request)
    {
        $satkerId = $request->query('satker_id');

        // Validasi Domain saat inisialisasi
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