<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\Satker;
use App\Models\User;
use App\Models\MenuAccess;
use App\Models\SubmenuAccess;
use App\Models\Role;

class SystemController extends Controller
{
    /**
     * Halaman Manajemen Menu
     */
    public function menu(Request $request)
    {
        $query = Menu::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('icon', 'like', "%{$search}%")
                ->orWhere('url', 'like', "%{$search}%");
        }

        $menus = $query->orderBy('order', 'asc')->get();

        // Jika dipanggil via AJAX, render partial view menu_table
        if ($request->ajax()) {
            return response()->json([
                'html' => view('Layouts.Partials.menu_table', compact('menus'))->render()
            ]);
        }

        $title = 'Menu';
        return view('Pages.System.menu', compact('title', 'menus'));
    }

    public function submenu(Request $request)
    {
        // Query Submenu diurutkan berdasarkan Order Menu Utama (parent), lalu Order Submenu
        $query = Submenu::with('menu')
            ->join('menus', 'submenus.menu_id', '=', 'menus.id')
            ->select('submenus.*')
            ->orderBy('menus.order', 'asc')
            ->orderBy('submenus.order', 'asc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('submenus.submenu', 'like', "%{$search}%")
                ->orWhere('submenus.url', 'like', "%{$search}%")
                ->orWhere('menus.name', 'like', "%{$search}%");
            });
        }

        // Grouping hasil data berdasarkan menu_id
        $submenusGrouped = $query->get()->groupBy('menu_id');

        // Jika dipanggil via AJAX, render partial view
        if ($request->ajax()) {
            return response()->json([
                'html' => view('Layouts.Partials.submenu_table', ['submenusGrouped' => $submenusGrouped])->render()
            ]);
        }

        $title = 'Submenu';
        $menus = Menu::where('is_dropdown', true)->orderBy('order', 'asc')->get();

        return view('Pages.System.submenu', compact('title', 'submenusGrouped', 'menus'));
    }

    // Simpan Menu Baru + Akses Khusus Administrator MS Aceh
    public function storeMenu(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|string|max:100',
            'url'         => 'nullable|string|max:255',
            'is_dropdown' => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
        ]);

        $nextOrder = Menu::max('order') ? Menu::max('order') + 1 : 1;

        $menu = Menu::create([
            'name'        => $request->name,
            'icon'        => $request->icon,
            'order'       => $nextOrder,
            'url'         => $request->url,
            'is_dropdown' => $request->has('is_dropdown') ? 1 : 0,
            'is_active'   => $request->has('is_active') ? 1 : 0,
        ]);

        // Cari Role Administrator Khusus Satker MS Aceh
        $adminRole = Role::whereHas('satker', function($q) {
            $q->where('satker_vshort', 'ms-aceh');
        })->where('role_name', 'administrator')->first();

        if ($adminRole) {
            MenuAccess::firstOrCreate([
                'role_id' => $adminRole->id,
                'menu_id' => $menu->id,
            ]);
        }

        return redirect()->back()->with('success', 'Menu Utama berhasil ditambahkan!');
    }

    // Simpan Submenu Baru + Akses Khusus Administrator MS Aceh
    public function storeSubmenu(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'submenu' => 'required|string|max:255',
            'url'     => 'required|string|max:255',
        ]);

        $nextOrder = Submenu::where('menu_id', $request->menu_id)->max('order');
        $nextOrder = $nextOrder ? $nextOrder + 1 : 1;

        $submenu = Submenu::create([
            'menu_id' => $request->menu_id,
            'submenu' => $request->submenu,
            'url'     => $request->url,
            'order'   => $nextOrder,
        ]);

        // Cari Role Administrator Khusus Satker MS Aceh
        $adminRole = Role::whereHas('satker', function($q) {
            $q->where('satker_vshort', 'ms-aceh');
        })->where('role_name', 'administrator')->first();

        if ($adminRole) {
            SubmenuAccess::firstOrCreate([
                'role_id'    => $adminRole->id,
                'submenu_id' => $submenu->id,
            ]);

            MenuAccess::firstOrCreate([
                'role_id' => $adminRole->id,
                'menu_id' => $request->menu_id,
            ]);
        }

        return redirect()->back()->with('success', 'Submenu berhasil ditambahkan!');
    }

    public function destroyMenu($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Menu tidak ditemukan!'
            ], 404);
        }

        // 1. Ambil semua ID Submenu terkait
        $submenuIds = Submenu::where('menu_id', $id)->pluck('id');

        // 2. Hapus Hak Akses Submenu & Submenu-nya
        if ($submenuIds->isNotEmpty()) {
            SubmenuAccess::whereIn('submenu_id', $submenuIds)->delete();
            Submenu::whereIn('id', $submenuIds)->delete();
        }

        // 3. Hapus Hak Akses Menu Utama
        MenuAccess::where('menu_id', $id)->delete();

        // 4. Hapus Menu Utama
        $menu->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Menu beserta seluruh Submenu terkait berhasil dihapus!'
        ]);
    }
    
    public function reorder(Request $request)
    {
        $request->validate([
            'id'        => 'required',
            'type'      => 'required|in:menu,submenu',
            'direction' => 'required|in:up,down',
        ]);

        // 1. Tentukan Model & Query Acuan
        if ($request->type === 'menu') {
            $model = Menu::find($request->id);
            if (!$model) return response()->json(['status' => 'error', 'message' => 'Menu tidak ditemukan!'], 404);
            
            $baseQuery = Menu::query();
        } else {
            $model = Submenu::find($request->id);
            if (!$model) return response()->json(['status' => 'error', 'message' => 'Submenu tidak ditemukan!'], 404);

            $baseQuery = Submenu::where('menu_id', $model->menu_id);
        }

        // 2. NORMALISASI: Urutkan ulang nilai 'order' agar 1, 2, 3, ... tanpa gap/duplicate
        $items = (clone $baseQuery)->orderBy('order', 'asc')->get();
        foreach ($items as $index => $item) {
            $item->update(['order' => $index + 1]);
        }

        // 3. Ambil ulang data model yang sudah di-refresh nilainya
        $model->refresh();
        $currentOrder = $model->order;

        // 4. Cari item target tepat di atas (-1) atau di bawah (+1)
        if ($request->direction === 'up') {
            $target = (clone $baseQuery)->where('order', $currentOrder - 1)->first();
        } else {
            $target = (clone $baseQuery)->where('order', $currentOrder + 1)->first();
        }

        if (!$target) {
            return response()->json([
                'status'  => 'info',
                'message' => 'Posisi sudah berada di urutan paling ' . ($request->direction === 'up' ? 'atas!' : 'bawah!')
            ]);
        }

        // 5. Eksekusi Tukar Posisi (Swap Order)
        $model->update(['order' => $target->order]);
        $target->update(['order' => $currentOrder]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Urutan berhasil diperbarui!'
        ]);
    }


    public function satker()
    {
        // Mengambil data Satker beserta total user/admin di dalamnya
        $satkers = Satker::withCount('users')->get();

        return view('Pages.Pengguna.satker', [
            'title'   => 'Daftar Satuan Kerja - SAPA MS ACEH',
            'satkers' => $satkers,
        ]);
    }

    /**
     * Menampilkan daftar Pengguna/Admin dari setiap Satker
     * URL: http://sapa.test/pengguna/admin
     */
    public function admin(Request $request)
    {
        $query = User::with(['satker', 'role']);

        // Filter opsional berdasarkan Satker jika ada pencarian/pilihan
        if ($request->has('satker_id') && $request->satker_id != '') {
            $query->where('satker_id', $request->satker_id);
        }

        $users = $query->latest()->get();
        $satkers = Satker::all();

        return view('Pages.Pengguna.admin', [
            'title'   => 'Daftar Pengguna Satker - SAPA MS ACEH',
            'users'   => $users,
            'satkers' => $satkers,
            'selectedSatker' => $request->satker_id,
        ]);
    }

    public function access(Request $request)
    {
        // Mengambil semua role beserta relasi Satker
        $roles = Role::with('satker')->orderBy('role_name', 'asc')->get();
        
        // Role yang dipilih (default role pertama jika tidak ada filter)
        $selectedRoleId = $request->get('role_id', $roles->first()->id ?? null);
        
        // Ambil semua menu dan submenu
        $menus = Menu::orderBy('order', 'asc')->get();
        $submenus = Submenu::with('menu')->orderBy('order', 'asc')->get();

        // Ambil ID akses yang sudah dimiliki role terpilih
        $activeMenuIds = MenuAccess::where('role_id', $selectedRoleId)->pluck('menu_id')->toArray();
        $activeSubmenuIds = SubmenuAccess::where('role_id', $selectedRoleId)->pluck('submenu_id')->toArray();

        $title = 'Hak Akses Role';

        return view('Pages.System.access', compact(
            'title', 'roles', 'selectedRoleId', 'menus', 'submenus', 'activeMenuIds', 'activeSubmenuIds'
        ));
    }

    /**
     * Toggle Tambah/Hapus Hak Akses Menu Utama (AJAX)
     */
    public function toggleMenuAccess(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'menu_id' => 'required|exists:menus,id',
        ]);

        $access = MenuAccess::where('role_id', $request->role_id)
            ->where('menu_id', $request->menu_id)
            ->first();

        if ($access) {
            $access->delete();
            $status = 'removed';
            $message = 'Hak akses Menu Utama berhasil dicabut!';
        } else {
            MenuAccess::create([
                'role_id' => $request->role_id,
                'menu_id' => $request->menu_id,
            ]);
            $status = 'added';
            $message = 'Hak akses Menu Utama berhasil diberikan!';
        }

        return response()->json([
            'status'  => 'success',
            'action'  => $status,
            'message' => $message,
        ]);
    }

    /**
     * Toggle Tambah/Hapus Hak Akses Submenu (AJAX)
     */
    public function toggleSubmenuAccess(Request $request)
    {
        $request->validate([
            'role_id'    => 'required|exists:roles,id',
            'submenu_id' => 'required|exists:submenus,id',
        ]);

        $submenu = Submenu::findOrFail($request->submenu_id);

        $access = SubmenuAccess::where('role_id', $request->role_id)
            ->where('submenu_id', $request->submenu_id)
            ->first();

        if ($access) {
            $access->delete();
            $status = 'removed';
            $message = 'Hak akses Submenu berhasil dicabut!';
        } else {
            // Otomatis tambahkan juga akses ke Menu Parent-nya jika belum ada
            MenuAccess::firstOrCreate([
                'role_id' => $request->role_id,
                'menu_id' => $submenu->menu_id,
            ]);

            SubmenuAccess::create([
                'role_id'    => $request->role_id,
                'submenu_id' => $request->submenu_id,
            ]);
            $status = 'added';
            $message = 'Hak akses Submenu berhasil diberikan!';
        }

        return response()->json([
            'status'  => 'success',
            'action'  => $status,
            'message' => $message,
        ]);
    }
}