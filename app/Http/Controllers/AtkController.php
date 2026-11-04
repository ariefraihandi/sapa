<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AtkItem;
use App\Models\AtkTransaction;
use App\Models\AtkShoppingPlan;
use App\Models\AtkShoppingPlanDetail;
use App\Models\Satker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AtkController extends Controller
{
    /**
     * Helper untuk mendapatkan Satker ID user login
     */
    private function getSatkerId()
    {
        $user = Auth::user();
        return $user ? $user->satker_id : null;
    }

    // =========================================================================
    // 1. VIEW METHODS (Halaman Web)
    // =========================================================================

    /**
     * Submenu 1: Dashboard & Stok ATK
     */
    public function dashboard()
    {
        $satkerId =$this->getSatkerId();

        $totalItems = AtkItem::where('satker_id',$satkerId)->count();
        $stokMinimalCount = AtkItem::where('satker_id',$satkerId)
            ->whereColumn('stok_saat_ini', '<=', 'stok_minimal')
            ->count();

        $items = AtkItem::where('satker_id',$satkerId)
            ->orderBy('nama_barang', 'asc')
            ->get();

        $recentTransactions = AtkTransaction::with(['item', 'user'])
            ->where('satker_id', $satkerId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('Pages.ATK.dashboard', compact(
            'totalItems',
            'stokMinimalCount',
            'items',
            'recentTransactions'
        ));
    }

    /**
     * Submenu 2: Master Data Barang
     */
    public function masterBarang()
    {
        $satkerId =$this->getSatkerId();

        $items = AtkItem::where('satker_id',$satkerId)
            ->orderBy('kode_barang', 'asc')
            ->get();

        return view('Pages.ATK.master_barang', compact('items'));
    }

    public function transaksi()
    {
        $satkerId = $this->getSatkerId();

        $items = AtkItem::where('satker_id', $satkerId)
            ->where('stok_saat_ini', '>', 0)
            ->orderBy('nama_barang', 'asc')
            ->get();

        $transactions = AtkTransaction::with(['item', 'user'])
            ->where('satker_id', $satkerId)
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // 💡 Generate Nomor Dokumen Otomatis (Format: 001/2026)
        $tahun = date('Y');
        $countTrx = AtkTransaction::where('satker_id', $satkerId)
            ->whereYear('created_at', $tahun)
            ->where('jenis_transaksi', 'keluar')
            ->count();

        $autoNomorDokumen = sprintf('%03d/%s', $countTrx + 1, $tahun);

        return view('Pages.ATK.transaksi', compact('items', 'transactions', 'autoNomorDokumen'));
    }

    public function laporan(Request $request)
    {
        $satkerId = $this->getSatkerId();
        
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));

        $items = AtkItem::where('satker_id', $satkerId)
            ->with(['transactions' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal_transaksi', $bulan)
                ->whereYear('tanggal_transaksi', $tahun);
            }])
            ->orderBy('nama_barang', 'asc')
            ->get();

        $shoppingPlans = AtkShoppingPlan::with(['details.item', 'creator'])
            ->where('satker_id', $satkerId)
            ->orderBy('created_at', 'desc')
            ->get();

        $bulanStr = sprintf('%02d', $bulan);

        return view('Pages.ATK.laporan', compact('items', 'shoppingPlans', 'tahun'), [
            'bulan' => $bulanStr
        ]);
    }

    // =========================================================================
    // 2. ACTION METHODS (Store, Update, Delete & Process)
    // =========================================================================

    /**
     * Master Barang: Store / Update Barang Baru
     */
    public function storeItem(Request $request)
    {
        $request->validate([
            'nama_barang'  => 'required|string|max:255',
            'satuan'       => 'required|string|max:50',
            'stok_minimal' => 'nullable|numeric|min:0',
        ]);

        $satkerId =$this->getSatkerId();

        if ($request->id) {
            $item = AtkItem::where('satker_id',$satkerId)->findOrFail($request->id);$item->update([
                'nama_barang'  => $request->nama_barang,
                'satuan'       => $request->satuan,
                'stok_minimal' => $request->stok_minimal ?? 5,
            ]);
            $msg = 'Master data barang berhasil diperbarui.';
        } else {
            $count = AtkItem::where('satker_id',$satkerId)->count();
            $kode = 'ATK-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            AtkItem::create([
                'id'            => (string) Str::uuid(),
                'satker_id'     => $satkerId,
                'kode_barang'   => $kode,
                'nama_barang'   => $request->nama_barang,
                'satuan'        => $request->satuan,
                'stok_awal'     => 0,
                'stok_saat_ini' => 0,
                'stok_minimal'  => $request->stok_minimal ?? 5,
            ]);
            $msg = 'Barang ATK baru berhasil ditambahkan.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Master Barang: Destroy
     */
    public function destroyItem($id)
    {
        $satkerId =$this->getSatkerId();
        $item = AtkItem::where('satker_id',$satkerId)->findOrFail($id);$item->delete();

        return redirect()->back()->with('success', 'Barang ATK berhasil dihapus.');
    }

    public function storeKeluar(Request $request)
    {
        $request->validate([
            'atk_item_id'       => 'required|exists:atk_items,id',
            'jumlah'            => 'required|numeric|min:1',
            'nama_pengambil'    => 'required|string|max:255',
            'ruangan'           => 'required|string|max:255',
            'nomor_dokumen'     => 'nullable|string|max:100',
            'keterangan'        => 'nullable|string',
            'tanggal_transaksi' => 'required|date',
        ]);

        $satkerId = $this->getSatkerId();
        $item = AtkItem::where('satker_id', $satkerId)->findOrFail($request->atk_item_id);

        if ($item->stok_saat_ini < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok barang tidak mencukupi! Sisa stok: ' . $item->stok_saat_ini . ' ' . $item->satuan);
        }

        // Jika nomor dokumen kosong, generate otomatis
        $nomorDokumen = $request->nomor_dokumen;
        if (empty($nomorDokumen)) {
            $tahun = date('Y');
            $countTrx = AtkTransaction::where('satker_id', $satkerId)
                ->whereYear('created_at', $tahun)
                ->where('jenis_transaksi', 'keluar')
                ->count();
            $nomorDokumen = sprintf('%03d/%s', $countTrx + 1, $tahun);
        }

        DB::transaction(function () use ($satkerId, $item, $request, $nomorDokumen) {
            // 1. Kurangi Stok
            $item->decrement('stok_saat_ini', $request->jumlah);

            // 2. Catat Transaksi Keluar
            AtkTransaction::create([
                'id'                => (string) Str::uuid(),
                'satker_id'         => $satkerId,
                'atk_item_id'       => $item->id,
                'user_id'           => Auth::id(),
                'nomor_dokumen'     => $nomorDokumen,
                'jenis_transaksi'   => 'keluar',
                'jumlah'            => $request->jumlah,
                'nama_pengambil'    => $request->nama_pengambil,
                'ruangan'           => $request->ruangan,
                'keterangan'        => $request->keterangan,
                'tanggal_transaksi' => $request->tanggal_transaksi,
            ]);
        });

        return redirect()->back()->with('success', 'Transaksi pengeluaran barang berhasil dicatat.');
    }

    public function updateTransaction(Request $request, $id)
    {
        $request->validate([
            'atk_item_id'       => 'required|exists:atk_items,id',
            'jumlah'            => 'required|numeric|min:1',
            'nama_pengambil'    => 'required|string|max:255',
            'ruangan'           => 'required|string|max:255',
            'nomor_dokumen'     => 'nullable|string|max:100',
            'keterangan'        => 'nullable|string',
            'tanggal_transaksi' => 'required|date',
        ]);

        $satkerId = $this->getSatkerId();
        $transaction = AtkTransaction::where('satker_id', $satkerId)->findOrFail($id);

        DB::transaction(function () use ($satkerId, $transaction, $request) {
            // 1. Reset (Kembalikan) stok barang dari transaksi LAMA
            $oldItem = AtkItem::find($transaction->atk_item_id);
            if ($oldItem) {
                if ($transaction->jenis_transaksi === 'keluar') {
                    $oldItem->increment('stok_saat_ini', $transaction->jumlah);
                } else {
                    $oldItem->decrement('stok_saat_ini', $transaction->jumlah);
                }
            }

            // 2. Ambil barang BARU (atau barang yang sama setelah stok lama dikembalikan)
            $newItem = AtkItem::where('satker_id', $satkerId)->findOrFail($request->atk_item_id);

            // Validasi kecukupan stok jika transaksi tipe keluar
            if ($transaction->jenis_transaksi === 'keluar' && $newItem->stok_saat_ini < $request->jumlah) {
                throw new \Exception('Stok barang tidak mencukupi! Sisa stok terkini: ' . $newItem->stok_saat_ini . ' ' . $newItem->satuan);
            }

            // 3. Potong/Tambah stok berdasarkan data transaksi BARU
            if ($transaction->jenis_transaksi === 'keluar') {
                $newItem->decrement('stok_saat_ini', $request->jumlah);
            } else {
                $newItem->increment('stok_saat_ini', $request->jumlah);
            }

            // 4. Update data transaksi
            $transaction->update([
                'atk_item_id'       => $newItem->id,
                'nomor_dokumen'     => $request->nomor_dokumen,
                'jumlah'            => $request->jumlah,
                'nama_pengambil'    => $request->nama_pengambil,
                'ruangan'           => $request->ruangan,
                'keterangan'        => $request->keterangan,
                'tanggal_transaksi' => $request->tanggal_transaksi,
            ]);
        });

        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui dan stok telah disesuaikan.');
    }

    public function destroyTransaction($id)
    {
        $satkerId = $this->getSatkerId();
        $transaction = AtkTransaction::where('satker_id', $satkerId)->findOrFail($id);

        DB::transaction(function () use ($transaction) {
            // Reset (Kembalikan) stok barang sebelum transaksi dihapus
            $item = AtkItem::find($transaction->atk_item_id);
            if ($item) {
                if ($transaction->jenis_transaksi === 'keluar') {
                    $item->increment('stok_saat_ini', $transaction->jumlah);
                } else {
                    $item->decrement('stok_saat_ini', $transaction->jumlah);
                }
            }

            $transaction->delete();
        });

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus dan stok barang telah dikembalikan.');
    }

    /**
     * Rencana Belanja: Store Header & Details
     */
    public function storeRencanaBelanja(Request $request)
    {
        $request->validate([
            'nama_kegiatan'   => 'required|string|max:255',
            'tanggal_rencana' => 'required|date',
            'catatan'         => 'nullable|string',
            'items'           => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:atk_items,id',
            'items.*.jumlah'  => 'required|integer|min:1',
        ]);

        $satkerId = $this->getSatkerId();

        DB::beginTransaction();
        try {
            // Generate Kode Rencana Otomatis
            $kodeRencana = 'PLN-' . date('Ymd') . '-' . Str::upper(Str::random(4));

            $plan = AtkShoppingPlan::create([
                'satker_id'       => $satkerId,
                'kode_rencana'    => $kodeRencana,
                'nama_kegiatan'   => $request->nama_kegiatan,
                'tanggal_rencana' => $request->tanggal_rencana,
                'catatan'         => $request->catatan,
                'status'          => 'draft',
                'created_by'      => Auth::id(),
            ]);

            // Masukkan seluruh detail item (menggunakan kolom atk_item_id)
            foreach ($request->items as $item) {
                $plan->details()->create([
                    'atk_item_id'    => $item['item_id'], // Fix: disesuaikan dengan kolom database 'atk_item_id'
                    'jumlah_rencana' => $item['jumlah'],
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Rencana belanja berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan rencana belanja: ' . $e->getMessage());
        }
    }

    /**
     * Rencana Belanja: Eksekusi Pindah Stok Saat Belanja Selesai (Status Completed)
     */
    public function completeRencanaBelanja(Request $request,$id)
    {
        $satkerId = $this->getSatkerId();$plan = AtkShoppingPlan::where('satker_id', $satkerId)->with('details')->findOrFail($id);

        if ($plan->status === 'completed') {
            return redirect()->back()->with('error', 'Rencana belanja ini sudah pernah direalisasikan.');
        }

        DB::transaction(function () use ($satkerId, $plan,$request) {
            // Update Status Rencana Belanja
            $plan->update(['status' => 'completed']);

            // Realisasi item & Tambah Stok
            foreach ($plan->details as$detail) {
                $realisasi = isset($request->realisasi[$detail->id]) ?$request->realisasi[$detail->id] :$detail->jumlah_rencana;
                
                $detail->update(['jumlah_realisasi' =>$realisasi]);

                // 1. Tambah Stok Utama
                $item = AtkItem::where('satker_id', $satkerId)->findOrFail($detail->atk_item_id);
                $item->increment('stok_saat_ini',$realisasi);

                // 2. Catat Transaksi Masuk
                AtkTransaction::create([
                    'id'                => (string) Str::uuid(),
                    'satker_id'         => $satkerId,
                    'atk_item_id'       => $detail->atk_item_id,
                    'user_id'           => Auth::id(),
                    'nomor_dokumen'     => $plan->kode_rencana,
                    'jenis_transaksi'   => 'masuk',
                    'jumlah'            => $realisasi,
                    'keterangan'        => 'Pengisian Stok dari Rencana Belanja: ' . $plan->nama_kegiatan,
                    'tanggal_transaksi' => now(),
                ]);
            }
        });

        return redirect()->back()->with('success', 'Belanja ATK berhasil diselesaikan dan stok telah ditambahkan!');
    }
}