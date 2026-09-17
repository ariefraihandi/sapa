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
    public function storePengunjung(Request $request)
    {
        try {
            // 1. Validasi Input (NIK Dibuat Wajib / Required 16 Digit)
            $validated = $request->validate([
                'satker_id'      => 'required',
                'jenis_layanan'  => 'required|in:pesan,telepon',
                'nama_responden' => 'required|string|max:255',
                'no_hp'          => 'required|string|max:20',
                'nik'            => 'required|numeric',
                'email'          => 'nullable|email|max:255',
                'jenis_kelamin'  => 'nullable|in:L,P',
                'usia'           => 'nullable|string|max:30',
                'pekerjaan'      => 'nullable|string|max:255',
                'pendidikan'     => 'nullable|string|max:255',
                'keperluan'      => 'required|string',
            ], [
                'nik.required' => 'NIK wajib diisi.',
                'nik.digits'   => 'NIK harus berisi tepat 16 digit angka.',
                'nik.numeric'  => 'NIK hanya boleh berupa angka.',
            ]);

            // 2. Simpan Data Pengunjung ke Database
            $pengunjung = PengunjungPtsp::create($validated);

            // 3. Ambil Data Satker & Layanan PTSP Daerah
            $satker = Satker::find($request->satker_id);
            $ptspDaerah = DB::table('ptsp_daerahs')->where('satker_id', $request->satker_id)->first();

            $namaSatker = $satker->satker_name ?? $satker->satker_short_name ?? 'MS Aceh';
            
            // Mengarah Murni ke Nomor WhatsApp Layanan Publik PTSP Daerah
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

            // 4. Buat Format Pesan Otomatis untuk WhatsApp
            $pesanWa  = "Halo PTSP *" . $namaSatker . "*,\n\n";
            $pesanWa .= "Saya membutuhkan informasi/layanan:\n";
            $pesanWa .= "• *Nama:* " . $pengunjung->nama_responden . "\n";
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
        
        $satker = Satker::find($satkerId);
        $satkerName = $satker ? ($satker->satker_name ?? $satker->satker_short_name) : 'MS Aceh';

        $pekerjaanList = Pekerjaan::orderBy('nama_pekerjaan', 'asc')->pluck('nama_pekerjaan');

        return response()->json([
            'status'         => 'success',
            'satker_name'    => $satkerName,
            'pekerjaan_list' => $pekerjaanList
        ]);
    }
}