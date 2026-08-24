<?php

namespace App\Http\Controllers;

use App\Models\SyaratPerkara;
use App\Models\JenisPerkara;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SyaratPerkaraController extends Controller
{
    /**
     * Helper Method: Mengecek & menyinkronkan status is_approved otomatis
     */
    private function syncGroupApprovalStatus($satkerId, $jenisPerkaraId)
    {
        $docs = SyaratPerkara::where('satker_id', $satkerId)
            ->where('jenis_perkara_id', $jenisPerkaraId)
            ->get();

        $totalDocs = $docs->count();
        $activeDocs = $docs->where('is_active', 1)->count();

        $isFullyActive = ($totalDocs > 0) && ($totalDocs === $activeDocs);

        SyaratPerkara::where('satker_id', $satkerId)
            ->where('jenis_perkara_id', $jenisPerkaraId)
            ->update([
                'is_approved' => $isFullyActive ? 1 : 0
            ]);

        return $isFullyActive;
    }

    /**
     * Helper Method: Cek apakah user saat ini adalah Administrator / Superadmin
     */
    private function isAdministrator()
    {
        $user = Auth::user();
        $roleName = strtolower($user->role->role_name ?? $user->role ?? '');
        return in_array($roleName, ['administrator', 'superadmin', 'admin']) || !$user->satker_id;
    }

    /**
     * Index Halaman Utama
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userSatker = $user->satker;
        $isMsAceh = !$user->satker_id || ($userSatker && $userSatker->satker_vshort === 'ms-aceh');

        $satkers = Satker::orderBy('satker_name', 'asc')->get();

        $query = SyaratPerkara::with(['jenisPerkara', 'satker']);

        if (!$isMsAceh) {
            $query->where('satker_id', $user->satker_id);
        } else {
            if ($request->filled('satker_id')) {
                $query->where('satker_id', $request->satker_id);
            }
        }

        $kategoriOrder = [
            'Perkawinan' => 1,
            'Kewarisan & Harta' => 2,
            'Kewarisan' => 2,
            'Ekonomi Syariah' => 3
        ];

        $syaratPerkaraGrouped = $query->get()
            ->groupBy(function ($item) {
                return $item->satker_id . '_' . $item->jenis_perkara_id;
            })
            ->map(function ($items) use ($kategoriOrder) {
                $first = $items->first();
                $totalDocs = $items->count();
                $totalAktif = $items->where('is_active', 1)->count();
                $belumValid = $totalDocs - $totalAktif;

                $isFullyApproved = ($totalDocs > 0) && ($totalDocs === $totalAktif);

                $kategoriName = $first->jenisPerkara->kategori ?? 'Lainnya';
                $order = $kategoriOrder[$kategoriName] ?? 99;

                return (object) [
                    'id'               => $first->id,
                    'satker_id'        => $first->satker_id,
                    'jenis_perkara_id' => $first->jenis_perkara_id,
                    'jenisPerkara'     => $first->jenisPerkara,
                    'satker'           => $first->satker,
                    'total_dokumen'    => $totalDocs,
                    'total_aktif'      => $totalAktif,
                    'belum_valid'      => $belumValid,
                    'is_approved'      => $isFullyApproved,
                    'kategori_order'   => $order,
                ];
            })
            ->sortBy([
                ['kategori_order', 'asc'],
                ['jenisPerkara.nama_layanan', 'asc']
            ])
            ->values();

        $jenisPerkara = JenisPerkara::orderBy('kategori')->get();

        return view('Pages.PTSP.syaratperkara', [
            'syaratPerkara' => $syaratPerkaraGrouped,
            'jenisPerkara'  => $jenisPerkara,
            'satkers'       => $satkers,
            'isMsAceh'      => $isMsAceh
        ]);
    }

    /**
     * Halaman Edit
     */
    public function edit(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('ptsp.syarat-perkara.index')->with('error', 'ID Syarat Perkara tidak ditemukan.');
        }

        $sample = SyaratPerkara::with(['jenisPerkara', 'satker'])->findOrFail($id);
        
        $user = Auth::user();
        $userSatker = $user->satker;
        $isMsAceh = !$user->satker_id || ($userSatker && $userSatker->satker_vshort === 'ms-aceh');

        if (!$isMsAceh && $sample->satker_id !== $user->satker_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola data ini.');
        }

        $syaratList = SyaratPerkara::where('satker_id', $sample->satker_id)
            ->where('jenis_perkara_id', $sample->jenis_perkara_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('Pages.PTSP.syaratperkara_edit', compact('sample', 'syaratList', 'isMsAceh'));
    }

    /**
     * Tambah Jenis Perkara Baru (Otomatis Buat 1 Syarat Pertama)
     */
    public function storeJenisPerkara(Request $request)
    {
        $request->validate([
            'kategori'       => 'required|in:Perkawinan,Kewarisan & Harta,Ekonomi Syariah,Lainnya',
            'nama_layanan'   => 'required|string|max:255|unique:jenis_perkaras,nama_layanan',
            'deskripsi'      => 'nullable|string|max:500',
            'syarat_pertama' => 'required|string|max:255',
            'url_dokumen'    => 'nullable|url',
        ], [
            'nama_layanan.unique' => 'Nama layanan / jenis perkara ini sudah terdaftar.',
            'syarat_pertama.required' => 'Wajib mengisi minimal 1 syarat dokumen pertama.',
        ]);

        $user = Auth::user();
        $satkerId = $user->satker_id ?? $request->satker_id;

        if (!$satkerId) {
            return redirect()->back()->with('error', 'Gagal mengidentifikasi Satker penginput.');
        }

        $jenisPerkara = JenisPerkara::create([
            'id'           => (string) Str::uuid(),
            'kategori'     => $request->kategori,
            'nama_layanan' => trim($request->nama_layanan),
            'deskripsi'    => trim($request->deskripsi),
        ]);

        $syaratUtama = SyaratPerkara::create([
            'id'               => (string) Str::uuid(),
            'satker_id'        => $satkerId,
            'jenis_perkara_id' => $jenisPerkara->id,
            'syarat_dokumen'   => trim($request->syarat_pertama),
            'url_dokumen'      => $request->url_dokumen,
            'is_active'        => 1,
            'is_approved'      => 0,
        ]);

        $this->syncGroupApprovalStatus($satkerId, $jenisPerkara->id);

        return redirect()->route('ptsp.syarat-perkara.edit', ['id' => $syaratUtama->id])
            ->with('success', 'Jenis perkara baru berhasil ditambahkan! Silakan lengkapi syarat dokumen lainnya.');
    }

    /**
     * Tambah 1 Syarat Dokumen Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'satker_id'        => 'required|exists:satkers,id',
            'jenis_perkara_id' => 'required|exists:jenis_perkaras,id',
            'syarat_dokumen'   => 'required|string|max:255',
            'url_dokumen'      => 'nullable|url',
        ]);

        SyaratPerkara::create([
            'id'                 => (string) Str::uuid(),
            'satker_id'          => $request->satker_id,
            'jenis_perkara_id'   => $request->jenis_perkara_id,
            'syarat_dokumen'     => trim($request->syarat_dokumen),
            'url_dokumen'        => $request->url_dokumen,
            'is_active'          => 1,
            'is_approved'        => 0,
        ]);

        $this->syncGroupApprovalStatus($request->satker_id, $request->jenis_perkara_id);

        return redirect()->back()->with('success', 'Dokumen persyaratan baru berhasil ditambahkan.');
    }

    /**
     * Update 1 Baris Syarat Dokumen
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'syarat_dokumen' => 'required|string|max:255',
            'url_dokumen'    => 'nullable|url',
        ]);

        $syarat = SyaratPerkara::findOrFail($id);

        $syarat->update([
            'syarat_dokumen' => trim($request->syarat_dokumen),
            'url_dokumen'    => $request->url_dokumen,
        ]);

        $this->syncGroupApprovalStatus($syarat->satker_id, $syarat->jenis_perkara_id);

        return redirect()->back()->with('success', 'Rincian dokumen berhasil diperbarui.');
    }

    /**
     * Hapus 1 Baris Syarat Dokumen (Hanya Administrator / Superadmin)
     */
    public function destroy($id)
    {
        if (!$this->isAdministrator()) {
            return redirect()->back()->with('error', 'Akses ditolak. Fitur hapus hanya diizinkan untuk Administrator / Superadmin.');
        }

        $syarat = SyaratPerkara::findOrFail($id);
        $satkerId = $syarat->satker_id;
        $jenisPerkaraId = $syarat->jenis_perkara_id;

        $syarat->delete();

        $this->syncGroupApprovalStatus($satkerId, $jenisPerkaraId);

        return redirect()->back()->with('success', 'Satu dokumen persyaratan berhasil dihapus.');
    }

    /**
     * Hapus Seluruh Jenis Perkara Beserta Semua Syarat Dokumen di Bawahnya (Khusus Administrator)
     */
    public function destroyJenisPerkara($jenisPerkaraId)
    {
        if (!$this->isAdministrator()) {
            return redirect()->back()->with('error', 'Akses ditolak. Fitur hapus jenis perkara hanya untuk Administrator / Superadmin.');
        }

        $jenisPerkara = JenisPerkara::findOrFail($jenisPerkaraId);

        // 1. Hapus semua syarat dokumen yang terkait dengan jenis perkara ini
        SyaratPerkara::where('jenis_perkara_id', $jenisPerkaraId)->delete();

        // 2. Hapus jenis perkaranya
        $jenisPerkara->delete();

        return redirect()->route('ptsp.syarat-perkara.index')
            ->with('success', 'Jenis perkara beserta seluruh syarat dokumen terkait berhasil dihapus.');
    }

    /**
     * Update Status Keaktifan via AJAX Toggle
     */
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id'        => 'required|exists:syarat_perkaras,id',
            'is_active' => 'required|in:0,1,true,false',
        ]);

        $syarat = SyaratPerkara::findOrFail($request->id);
        $status = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        $syarat->update([
            'is_active' => $status
        ]);

        $isGroupApproved = $this->syncGroupApprovalStatus($syarat->satker_id, $syarat->jenis_perkara_id);

        return response()->json([
            'success'           => true,
            'is_active'         => $syarat->is_active,
            'is_group_approved' => $isGroupApproved,
            'message'           => $isGroupApproved 
                                   ? 'Status dokumen diubah. Seluruh syarat telah AKTIF, layanan otomatis TAYANG!' 
                                   : 'Status dokumen diubah. Masih ada dokumen non-aktif, status layanan PENDING REVIEW.'
        ]);
    }
}