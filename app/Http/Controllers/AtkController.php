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

    /**
     * Submenu 3: Transaksi Stok (Pengeluaran / Transaksi Keluar-Masuk)
     */
    public function transaksi()
    {
        $satkerId =$this->getSatkerId();

        $items = AtkItem::where('satker_id',$satkerId)
            ->where('stok_saat_ini', '>', 0)
            ->orderBy('nama_barang', 'asc')
            ->get();

        $transactions = AtkTransaction::with(['item', 'user'])
            ->where('satker_id', $satkerId)
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Pages.ATK.transaksi', compact('items', 'transactions'));
    }

    /**
     * Submenu 4: Laporan & Rekapitulasi ATK
     */
    public function laporan(Request $request)
    {
        $satkerId =$this->getSatkerId();
        $bulan =$request->get('bulan', date('m'));
        $tahun =$request->get('tahun', date('Y'));

        $items = AtkItem::where('satker_id',$satkerId)
            ->with(['transactions' => function ($q) use ($bulan,$tahun) {
                $q->whereMonth('tanggal_transaksi',$bulan)
                  ->whereYear('tanggal_transaksi', $tahun);
            }])
            ->orderBy('nama_barang', 'asc')
            ->get();

        $shoppingPlans = AtkShoppingPlan::with(['details.item', 'creator'])
            ->where('satker_id', $satkerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Pages.ATK.laporan', compact('items', 'shoppingPlans', 'bulan', 'tahun'));
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

    /**
     * Transaksi Keluar: Catat Penggunaan Barang (Sesuai Form Fisik Gambar)
     */
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

        $satkerId = $this->getSatkerId();$item = AtkItem::where('satker_id', $satkerId)->findOrFail($request->atk_item_id);

        if ($item->stok_saat_ini <$request->jumlah) {
            return redirect()->back()->with('error', 'Stok barang tidak mencukupi! Sisa stok: ' . $item->stok_saat_ini . ' ' .$item->satuan);
        }

        DB::transaction(function () use ($satkerId, $item,$request) {
            // 1. Kurangi Stok
            $item->decrement('stok_saat_ini',$request->jumlah);

            // 2. Catat Transaksi Keluar
            AtkTransaction::create([
                'id'                => (string) Str::uuid(),
                'satker_id'         => $satkerId,
                'atk_item_id'       => $item->id,
                'user_id'           => Auth::id(),
                'nomor_dokumen'     => $request->nomor_dokumen,
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

    /**
     * Rencana Belanja: Store Header & Details
     */
    public function storeRencanaBelanja(Request $request)
    {
        $request->validate([
            'nama_kegiatan'   => 'required|string|max:255',
            'tanggal_rencana' => 'required|date',
            'items'           => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:atk_items,id',
            'items.*.jumlah'  => 'required|numeric|min:1',
        ]);

        $satkerId =$this->getSatkerId();
        $count = AtkShoppingPlan::where('satker_id',$satkerId)->count();
        $kodeRencana = 'RB-' . date('Ym') . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($satkerId,$kodeRencana, $request) {$plan = AtkShoppingPlan::create([
                'id'              => (string) Str::uuid(),
                'satker_id'       => $satkerId,
                'created_by'      => Auth::id(),
                'kode_rencana'    => $kodeRencana,
                'nama_kegiatan'   => $request->nama_kegiatan,
                'tanggal_rencana' => $request->tanggal_rencana,
                'status'          => 'draft',
                'catatan'         => $request->catatan,
            ]);

            foreach ($request->items as$detailItem) {
                AtkShoppingPlanDetail::create([
                    'id'                   => (string) Str::uuid(),
                    'atk_shopping_plan_id' => $plan->id,
                    'atk_item_id'          => $detailItem['item_id'],
                    'jumlah_rencana'       => $detailItem['jumlah'],
                    'jumlah_realisasi'     => 0,
                    'estimasi_harga'       => $detailItem['estimasi_harga'] ?? 0,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Rencana belanja ATK berhasil dibuat.');
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