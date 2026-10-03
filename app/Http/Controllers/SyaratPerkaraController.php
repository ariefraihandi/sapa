<?php

namespace App\Http\Controllers;

use App\Models\SyaratPerkara;
use App\Models\JenisPerkara;
use App\Models\Satker;
use App\Models\PtspDaerah;
use App\Models\NotifikasiPtsp;
use App\Models\PengunjungPtsp;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $title = 'Syarat Perkara';
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
            'title'         => $title,
            'isMsAceh'      => $isMsAceh
        ]);
    }

    /**
     * Halaman Edit
     */

    public function updateJenisPerkara(Request $request, $id)
    {
        $request->validate([
            'kategori'     => 'required|string',
            'nama_layanan' => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
        ]);

        $jenisPerkara = JenisPerkara::findOrFail($id);
        $jenisPerkara->update([
            'kategori'     => $request->kategori,
            'nama_layanan' => $request->nama_layanan,
            'deskripsi'    => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Master Jenis Perkara berhasil diperbarui.');
    }

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

    public function ptspDaerah()
    {
        $user = Auth::user();
        $userSatker = $user->satker;

        // Cek apakah user MS Aceh / Super Admin
        $isMsAceh = ($userSatker && ($userSatker->satker_vshort === 'ms-aceh' || strtolower($userSatker->satker_short_name) === 'ms aceh'));

        if ($isMsAceh) {
            // Mode MS Aceh: Monitoring Tabel Seluruh Satker
            $satkers = Satker::with('ptspDaerah')
                ->orderBy('satker_name', 'asc')
                ->get();

            return view('Pages.PTSP.index_admin', compact('satkers'));
        } else {
            // Mode Satker Daerah: Form Edit Profil Mandiri
            $satker = Satker::with('ptspDaerah')->findOrFail($user->satker_id);
            $ptsp = $satker->ptspDaerah;

            return view('Pages.PTSP.index_daerah', compact('satker', 'ptsp'));
        }
    }

    public function daftarPtsp()
    {
        // Mengambil seluruh data Satker beserta relasi ptspDaerah, diurutkan secara abjad
        $daftarSatker = Satker::with('ptspDaerah')->orderBy('satker_name', 'asc')->get();

        return view('Pages.PTSP.profil-ptsp', [
            'title'        => 'Daftar PTSP Se-Aceh',
            'daftarSatker' => $daftarSatker
        ]);
    }

    /**
     * Simpan / Update Data PTSP
     */
    public function updatePtspDaerah(Request $request, $satker_id)
    {
        $request->validate([
            'nama_pj'              => 'required|string|max:255',
            'no_hp_pj'             => 'required|string|max:50',
            'no_wa_layanan'        => 'nullable|string|max:50',
            'has_whatsapp_service' => 'required|boolean',
            'is_call_able'         => 'required|boolean',
        ]);

        $formatHp = function ($number) {
            if (empty($number)) return null;
            $clean = preg_replace('/[^0-9]/', '', $number);
            return str_starts_with($clean, '0') ? '62' . substr($clean, 1) : $clean;
        };

        $noWaLayananClean = $formatHp($request->no_wa_layanan);
        $noHpPjClean      = $formatHp($request->no_hp_pj);

        // Update / Create data di ptsp_daerahs
        PtspDaerah::updateOrCreate(
            ['satker_id' => $satker_id],
            [
                'nama_pj'              => $request->nama_pj,
                'no_hp_pj'             => $noHpPjClean,
                'has_whatsapp_service' => $request->has_whatsapp_service,
                'no_wa_layanan'        => $noWaLayananClean,
                'is_call_able'         => $request->is_call_able,
            ]
        );

        // Update nomor WhatsApp di tabel satkers
        $satker = Satker::findOrFail($satker_id);
        $satker->update([
            'whatsapp' => $noWaLayananClean
        ]);

        return redirect()->back()->with('success', 'Data PTSP berhasil diperbarui!');
    }

    public function indexPengunjung(Request $request)
    {
        $user = Auth::user();
        $title = 'Daftar Pengunjung';

        $query = PengunjungPtsp::with('satker')->latest();

        if ($user->role !== 'admin') {
            $satkerName = $user->satker->satker_name ?? '';
            $isMsAceh = str_contains(strtolower($satkerName), 'mahkamah syar\'iyah aceh') || str_contains(strtolower($satkerName), 'ms aceh');

            if (!$isMsAceh && $user->satker_id) {
                $query->where('satker_id', $user->satker_id);
            }
        }

        $pengunjung = $query->paginate(15);

        return view('Pages.PTSP.pengunjung_index', compact('pengunjung', 'title'));
    }

    public function indexPengunjungv2(Request $request)
    {
        $user = Auth::user();
        $userSatker = $user ? $user->satker : null;

        // 💡 LOGIKA CEK & CREATE NOTIFIKASI SATKER
        $notifPtsp = null;
        if ($userSatker) {
            $notifPtsp = NotifikasiPtsp::firstOrCreate(
                ['satker_id' => $userSatker->id],
                [
                    'is_pengunjung' => false,
                    'is_pengaduan'  => false,
                ]
            );
        }

        $isMsAceh = ($userSatker && (
            strtolower($userSatker->satker_vshort ?? '') === 'ms-aceh' || 
            strtolower($userSatker->satker_short_name ?? '') === 'ms aceh'
        ));

        if ($request->ajax()) {
            $type = $request->input('type'); // 'ms_aceh' atau 'daerah'
            
            // Eager load relasi satker, satkerTujuan, dan ptspDaerah milik satkerTujuan
            $query = PengunjungPtsp::with(['satker', 'satkerTujuan.ptspDaerah'])
                ->select('pengunjung_ptsp.*')
                ->latest('created_at');

            if ($isMsAceh) {
                if ($type === 'ms_aceh') {
                    // Tabel MS Aceh: Muncul jika Satker Awal ATAU Satker Tujuan adalah MS Aceh
                    $query->where(function ($q) use ($userSatker) {
                        $q->where('satker_id', $userSatker->id)
                        ->orWhere('satker_tujuan_id', $userSatker->id);
                    });
                } else {
                    // Tabel MS Daerah (View MS Aceh): Muncul jika Satker Awal BUKAN MS Aceh ATAU Satker Tujuan BUKAN MS Aceh
                    $query->where(function ($q) use ($userSatker) {
                        $q->where('satker_id', '!=', $userSatker->id)
                        ->orWhere('satker_tujuan_id', '!=', $userSatker->id);
                    });
                }
            } else {
                // User Satker Daerah:
                // Tetap MUNCUL jika Satker Awal = ID (Satker Lama) ATAU Satker Tujuan = ID (Satker Baru)
                if ($userSatker) {
                    $query->where(function ($q) use ($userSatker) {
                        $q->where('satker_id', $userSatker->id)
                        ->orWhere('satker_tujuan_id', $userSatker->id);
                    });
                }
            }

            $tanggalFilter = $request->input('tanggal');
            if (!empty($tanggalFilter)) {
                $query->whereDate('created_at', $tanggalFilter);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('pemohon_html', function ($row) {
                    $jkBadge = $row->jenis_kelamin == 'L' 
                        ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1" style="font-size:10px;">Laki-laki</span>' 
                        : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1" style="font-size:10px;">Perempuan</span>';

                    $pekerjaanBadge = $row->pekerjaan 
                        ? '<span class="badge bg-light text-dark border me-1" style="font-size:10px;"><i class="fa-solid fa-briefcase me-1"></i>' . e($row->pekerjaan) . '</span>' 
                        : '';

                    $uuidShort = $row->id ? substr((string)$row->id, 0, 8) : '-';
                    $idBadge = '<span class="badge bg-secondary-subtle text-secondary border me-1" style="font-size:10px;"><i class="fa-solid fa-hashtag me-1"></i>' . $uuidShort . '</span>';

                    $waktuBadge = $row->created_at 
                        ? '<span class="badge bg-light text-muted border" style="font-size:10px;"><i class="fa-regular fa-clock me-1"></i>' . $row->created_at->format('d/m/Y H:i') . ' WIB</span>' 
                        : '';

                    return '
                        <div class="fw-bold text-dark lh-sm mb-1">' . e($row->nama_responden) . '</div>
                        <div class="d-flex flex-wrap align-items-center gap-1">
                            ' . $jkBadge . '
                            ' . $pekerjaanBadge . '
                            ' . $idBadge . '
                            ' . $waktuBadge . '
                        </div>
                    ';
                })
                ->filterColumn('pemohon_html', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('pengunjung_ptsp.nama_responden', 'LIKE', "%{$keyword}%")
                        ->orWhere('pengunjung_ptsp.id', 'LIKE', "%{$keyword}%")
                        ->orWhere('pengunjung_ptsp.pekerjaan', 'LIKE', "%{$keyword}%");
                    });
                })
                ->addColumn('tujuan_layanan_html', function ($row) {
                    $satkerAwal = $row->satker->satker_short_name ?? '-';
                    $satkerTujuan = $row->satkerTujuan->satker_short_name ?? null;
                    
                    $layananRaw = strtolower($row->jenis_layanan ?? 'pesan');
                    $layanan = ucfirst($row->jenis_layanan ?? 'Pesan');

                    // Penentuan Style Badge berdasarkan Jenis Layanan
                    if (in_array($layananRaw, ['telepon', 'telpon', 'panggilan', 'call'])) {
                        $badgeClass = 'bg-primary-subtle text-primary border-primary-subtle';
                        $iconClass = 'fa-solid fa-phone me-1';
                    } else {
                        $badgeClass = 'bg-success-subtle text-success border-success-subtle';
                        $iconClass = 'fa-solid fa-message me-1';
                    }

                    // Tampilan Alur Satker jika didisposisikan
                    if ($row->satker_tujuan_id && $row->satker_tujuan_id !== $row->satker_id) {
                        $satkerHtml = '
                            <div class="mb-1 d-flex flex-column align-items-center gap-1">
                                <span class="badge bg-light text-secondary border" title="Satker Asal"><i class="fa-solid fa-building me-1 text-muted"></i>' . e($satkerAwal) . '</span>
                                <i class="fa-solid fa-arrow-down text-info" style="font-size: 10px;"></i>
                                <span class="badge bg-info-subtle text-info border border-info-subtle" title="Satker Disposisi Tujuan"><i class="fa-solid fa-share-nodes me-1"></i>' . e($satkerTujuan) . '</span>
                            </div>
                        ';
                    } else {
                        $satkerHtml = '<div class="mb-1"><span class="badge bg-light text-dark border"><i class="fa-solid fa-building me-1 text-success"></i>' . e($satkerAwal) . '</span></div>';
                    }

                    return $satkerHtml . '<div><span class="badge ' . $badgeClass . ' border"><i class="' . $iconClass . '"></i>' . e($layanan) . '</span></div>';
                })
                ->addColumn('kontak_wa_html', function ($row) {
                    if (!$row->no_hp) return '<span class="text-muted small">-</span>';
                    
                    // Formatting Nomor HP
                    $phone = preg_replace('/[^0-9]/', '', $row->no_hp);
                    if (str_starts_with($phone, '0')) {
                        $phone = '62' . substr($phone, 1);
                    }

                    // Panggilan berdasarkan Jenis Kelamin
                    $panggilan = ($row->jenis_kelamin === 'L') ? 'Bapak' : 'Ibu';

                    // Nama Satker Asal & Tujuan
                    $satkerAwalName = $row->satker->satker_name ?? 'Mahkamah Syar\'iyah';
                    $satkerTujuanName = $row->satkerTujuan->satker_name ?? null;
                    $keperluanText = $row->keperluan ?: 'Layanan Informasi PTSP';

                    // Cek apakah data ini hasil Disposisi (Ada Satker Tujuan)
                    $isDisposisi = ($row->satker_tujuan_id && $row->satker_tujuan_id !== $row->satker_id);

                    if ($isDisposisi) {
                        // Draf Pesan Khusus untuk Pemohon yang Didisposisikan
                        $pesanPemohon = "Assalamu'alaikum wr. wb.,\n\n";
                        $pesanPemohon .= "Mohon maaf mengganggu waktunya " . $panggilan . " " . $row->nama_responden . " 🙏\n\n";
                        $pesanPemohon .= "Kami menerima informasi dari *" . $satkerAwalName . "* bahwa " . $panggilan . " memiliki keperluan terkait:\n";
                        $pesanPemohon .= "👉 *" . $keperluanText . "*\n\n";
                        $pesanPemohon .= "Karena keperluan tersebut berada dalam kewenangan *" . $satkerTujuanName . "*, permohonan " . $panggilan . " telah dialihkan kepada kami di PTSP *" . $satkerTujuanName . "*.\n\n";
                        $pesanPemohon .= "Apakah ada informasi yang ingin ditanyakan lebih lanjut? Silakan balas pesan ini ya, kami siap membantu 😊";
                    } else {
                        // Draf Pesan Standar (Tanpa Disposisi)
                        $pesanPemohon = "Assalamu'alaikum wr. wb.,\n\n";
                        $pesanPemohon .= "Terima kasih " . $panggilan . " " . $row->nama_responden . " sudah menghubungi PTSP " . $satkerAwalName . " 🙏\n\n";
                        $pesanPemohon .= "Mengenai keperluan " . $panggilan . " terkait:\n";
                        $pesanPemohon .= "👉 *" . $keperluanText . "*\n\n";
                        $pesanPemohon .= "Ada yang bisa kami bantu atau informasikan lebih lanjut? Silakan balas pesan ini ya, terima kasih 😊";
                    }

                    $waUrl = "https://wa.me/" . $phone . "?text=" . urlencode($pesanPemohon);

                    // Logika Warna & Status (Merah untuk Belum, Hijau untuk Sudah)
                    if ($row->is_tindak_lanjut) {
                        $btnClass = 'btn-outline-success'; // Hijau jika sudah
                        $statusText = '<i class="fa-solid fa-check-circle me-1"></i>Ditindaklanjuti';
                    } else {
                        $btnClass = 'btn-danger'; // Merah jika belum
                        $statusText = '<i class="fa-brands fa-whatsapp me-1"></i>Hubungi Pemohon';
                    }

                    return '
                        <div class="d-flex flex-column align-items-center gap-1">
                            <a href="' . $waUrl . '" target="_blank" 
                            id="btn-wa-' . $row->id . '" 
                            onclick="markAsFollowedUp(\'' . $row->id . '\')" 
                            class="btn btn-sm ' . $btnClass . ' fw-bold px-2 py-1" style="font-size: 11px;">
                                ' . $statusText . '
                            </a>
                            <span class="text-muted fw-semibold" style="font-size: 11px;">' . e($row->no_hp) . '</span>
                        </div>
                    ';
                })
                ->addColumn('action', function ($row) {
                    $btnHubungiPtspTujuan = '';
                    
                    // Opsi Hubungi PTSP Satker Tujuan di dropdown Aksi (Aksesibel oleh Satker Awal)
                    if ($row->satkerTujuan && $row->satker_tujuan_id !== $row->satker_id) {
                        $ptspDaerah = $row->satkerTujuan->ptspDaerah;
                        $noWaPtsp = $ptspDaerah->no_wa_layanan ?? null;

                        if ($noWaPtsp) {
                            $phonePtsp = preg_replace('/[^0-9]/', '', $noWaPtsp);
                            if (str_starts_with($phonePtsp, '0')) {
                                $phonePtsp = '62' . substr($phonePtsp, 1);
                            }

                            $satkerAwalName = $row->satker->satker_name ?? 'Satker Asal';
                            $satkerTujuanName = $row->satkerTujuan->satker_name ?? 'Satker Tujuan';
                            $keperluanText = $row->keperluan ?: 'Tidak dicantumkan';

                            $pesan = "Assalamu'alaikum wr. wb., Yth. Petugas PTSP " . $satkerTujuanName . ".\n\n";
                            $pesan .= "Pemberitahuan Disposisi Pengunjung PTSP:\n";
                            $pesan .= "• Nama Pemohon: " . $row->nama_responden . "\n";
                            $pesan .= "• Kontak Pemohon: " . $row->no_hp . "\n";
                            $pesan .= "• Satker Asal: " . $satkerAwalName . "\n";
                            $pesan .= "• Keperluan: " . $keperluanText . "\n\n";
                            $pesan .= "Pemohon telah didisposisikan ke " . $satkerTujuanName . ". Mohon bantuan untuk dapat dilayani lebih lanjut. Terima kasih.";

                            $waUrl = "https://wa.me/" . $phonePtsp . "?text=" . urlencode($pesan);

                            $btnHubungiPtspTujuan = '
                                <li>
                                    <a class="dropdown-item py-1 text-success fw-semibold" href="' . $waUrl . '" target="_blank">
                                        <i class="fa-brands fa-whatsapp text-success me-2"></i>Hubungi PTSP ' . e($row->satkerTujuan->satker_short_name) . '
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                            ';
                        }
                    }

                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border dropdown-toggle fw-bold py-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:12px;">
                                Aksi
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <a class="dropdown-item py-1" href="#" data-bs-toggle="modal" data-bs-target="#modalDetailPengunjung' . $row->id . '">
                                        <i class="fa-solid fa-eye text-info me-2"></i>Detail
                                    </a>
                                </li>
                                ' . $btnHubungiPtspTujuan . '
                                <li>
                                    <a class="dropdown-item py-1 text-primary" href="#" data-bs-toggle="modal" data-bs-target="#modalDisposisiPengunjung' . $row->id . '">
                                        <i class="fa-solid fa-share-nodes me-2"></i>Disposisi Satker
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1" href="#" data-bs-toggle="modal" data-bs-target="#modalEditPengunjung' . $row->id . '">
                                        <i class="fa-solid fa-pen-to-square text-warning me-2"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-1 text-danger" href="#" data-bs-toggle="modal" data-bs-target="#modalDeletePengunjung' . $row->id . '">
                                        <i class="fa-solid fa-trash me-2"></i>Hapus
                                    </a>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['pemohon_html', 'tujuan_layanan_html', 'kontak_wa_html', 'action'])
                ->make(true);
        }
        
        $pengunjung     = PengunjungPtsp::with(['satker', 'satkerTujuan'])->latest('created_at')->get();
        $satkers        = Satker::orderBy('satker_name', 'asc')->get();
        $isSoundActive  = $notifPtsp ? (bool)$notifPtsp->is_pengunjung : false;

        return view('Pages.PTSP.pengunjung_v2', compact('pengunjung', 'satkers', 'isMsAceh', 'isSoundActive'));
    }

    // 💡 FUNGSI UNTUK TOGGLE STATUS SOUND DI DATABASE
    public function toggleSoundPengunjung(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->satker) {
            return response()->json(['success' => false, 'message' => 'User tidak terautentikasi / satker tidak ditemukan'], 401);
        }

        $notif = NotifikasiPtsp::firstOrCreate(
            ['satker_id' => $user->satker->id],
            ['is_pengunjung' => false, 'is_pengaduan' => false]
        );

        // Toggle nilai is_pengunjung (1 jadi 0, 0 jadi 1)
        $notif->is_pengunjung = !$notif->is_pengunjung;
        $notif->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool)$notif->is_pengunjung,
            'message' => $notif->is_pengunjung ? 'Suara notifikasi diaktifkan!' : 'Suara notifikasi dinonaktifkan!'
        ]);
    }

    /**
     * Disposisi / Dialihkan ke Satker Baru
     */
    public function disposisiPengunjung(Request $request, $id)
    {
        // 1. Validasi input
        $request->validate([
            'satker_tujuan_id' => 'required|exists:satkers,id',
        ], [
            'satker_tujuan_id.required' => 'Pilih Satker tujuan terlebih dahulu.',
            'satker_tujuan_id.exists'   => 'Satker tujuan tidak valid.',
        ]);

        // 2. Cari data pengunjung berdasarkan ID
        $pengunjung = PengunjungPtsp::findOrFail($id);

        // 3. Simpan ke kolom satker_tujuan_id (TIDAK mengubah satker_id asal)
        $pengunjung->satker_tujuan_id = $request->satker_tujuan_id;

        // 4. Reset status tindak lanjut ke false agar Satker tujuan harus menindaklanjuti ulang
        $pengunjung->is_tindak_lanjut = false;

        // 5. Simpan perubahan ke database
        $pengunjung->save();

        return redirect()->back()->with('success', 'Pengunjung berhasil didisposisikan ke Satker tujuan baru.');
    }

    public function checkNewPengunjung(Request $request)
    {
        $user = Auth::user();
        $userSatker = $user ? $user->satker : null;

        $isMsAceh = ($userSatker && (
            strtolower($userSatker->satker_vshort ?? '') === 'ms-aceh' || 
            strtolower($userSatker->satker_short_name ?? '') === 'ms aceh'
        ));

        if ($isMsAceh) {
            // 1. Data Paling Baru khusus MS Aceh (Utamakan satker_tujuan_id jika ada)
            $latestMsAcehRecord = PengunjungPtsp::whereRaw('COALESCE(satker_tujuan_id, satker_id) = ?', [$userSatker->id])
                ->latest('updated_at')
                ->first();

            // 2. Data Paling Baru dari Seluruh Satker Daerah (Utamakan satker_tujuan_id jika ada)
            $latestDaerahRecord = PengunjungPtsp::whereRaw('COALESCE(satker_tujuan_id, satker_id) != ?', [$userSatker->id])
                ->latest('updated_at')
                ->first();

            // Kombinasi ID + Timestamp (updated_at / created_at) untuk deteksi akurat
            $latestMsAcehKey = $latestMsAcehRecord 
                ? $latestMsAcehRecord->id . '_' . ($latestMsAcehRecord->updated_at ?? $latestMsAcehRecord->created_at)->timestamp 
                : null;

            $latestDaerahKey = $latestDaerahRecord 
                ? $latestDaerahRecord->id . '_' . ($latestDaerahRecord->updated_at ?? $latestDaerahRecord->created_at)->timestamp 
                : null;

            return response()->json([
                'status' => 'success',
                'is_ms_aceh' => true,
                'latest_ms_aceh_id' => $latestMsAcehKey,
                'latest_daerah_id' => $latestDaerahKey,
            ]);
        } else {
            // 3. Satker Daerah Biasa
            $latestKey = null;

            if ($userSatker) {
                $latestRecord = PengunjungPtsp::whereRaw('COALESCE(satker_tujuan_id, satker_id) = ?', [$userSatker->id])
                    ->latest('updated_at')
                    ->first();

                $latestKey = $latestRecord 
                    ? $latestRecord->id . '_' . ($latestRecord->updated_at ?? $latestRecord->created_at)->timestamp 
                    : null;
            }

            return response()->json([
                'status' => 'success',
                'is_ms_aceh' => false,
                'latest_id' => $latestKey,
            ]);
        }
    }

    public function updatePengunjung(Request $request, $id)
    {
        $request->validate([
            'nama_responden' => 'required|string|max:255',
            'no_hp'          => 'required|string|max:20',
            'jenis_kelamin'  => 'required|in:L,P',
            'pekerjaan'      => 'nullable|string|max:100',
            'nik'            => 'nullable|string|max:16',
            'keperluan'      => 'nullable|string',
        ]);

        $pengunjung = PengunjungPtsp::findOrFail($id);
        $pengunjung->update([
            'nama_responden' => $request->nama_responden,
            'no_hp'          => $request->no_hp,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'pekerjaan'      => $request->pekerjaan,
            'nik'            => $request->nik,
            'keperluan'      => $request->keperluan,
        ]);

        return redirect()->back()->with('success', 'Data pengunjung berhasil diperbarui.');
    }

    // 2. Hapus Data Pengunjung PTSP
    public function destroyPengunjung($id)
    {
        $pengunjung = PengunjungPtsp::findOrFail($id);
        $pengunjung->delete();

        return redirect()->back()->with('success', 'Data pengunjung berhasil dihapus.');
    }

    // 2. Action Update Status Tindak Lanjut via WA Click
    public function toggleTindakLanjut($id)
    {
        $item = PengunjungPtsp::findOrFail($id);
        
        // Ubah status menjadi sudah ditindaklanjuti
        $item->update(['is_tindak_lanjut' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui!'
        ]);
    }

    public function indexPengaduan()
    {
        $user = Auth::user();
        $query = Pengaduan::with('satker')->latest();
        $title = 'Daftar Pengaduan';
        // Jika bukan Admin dan bukan satker Mahkamah Syar'iyah Aceh, batasi data hanya miliknya sendiri
        if ($user->role !== 'admin') {
            $satkerName = $user->satker->satker_name ?? '';
            $isMsAceh = str_contains(strtolower($satkerName), 'mahkamah syar\'iyah aceh') || str_contains(strtolower($satkerName), 'ms aceh');
            
            if (!$isMsAceh && $user->satker_id) {
                $query->where('satker_id', $user->satker_id);
            }
        }

        $pengaduan = $query->paginate(15);

        return view('Pages.PTSP.pengaduan_index', compact('pengaduan', 'title'));
    }

    public function toggleTindakLanjutPengaduan(Request $request, $id)
    {
        $request->validate([
            'catatan_tindak_lanjut' => 'required|string',
            'file_tindak_lanjut'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Maks 5MB
        ]);

        $pengaduan = Pengaduan::findOrFail($id);

        $filePath = $pengaduan->file_tindak_lanjut;
        if ($request->hasFile('file_tindak_lanjut')) {
            // Hapus file lama jika ada
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file_tindak_lanjut')->store('tindak_lanjut_pengaduan', 'public');
        }

        $pengaduan->update([
            'is_tindak_lanjut'      => true,
            'catatan_tindak_lanjut' => $request->catatan_tindak_lanjut,
            'file_tindak_lanjut'    => $filePath,
            'tgl_tindak_lanjut'     => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tindak lanjut pengaduan berhasil disimpan.',
            'data'    => [
                'catatan'  => $pengaduan->catatan_tindak_lanjut,
                'file_url' => $filePath ? asset('storage/' . $filePath) : null,
                'tgl'      => $pengaduan->tgl_tindak_lanjut->format('d M Y - H:i')
            ]
        ]);
    }
}