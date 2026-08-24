<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu & fitur pencarian
     */
    public function index(Request $request)
    {
        $query = Menu::query();

        // Fitur Pencarian Nama Menu / URL
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('menu_name', 'LIKE', "%{$search}%")
                  ->orWhere('url', 'LIKE', "%{$search}%");
            });
        }

        $menus = $query->orderBy('order', 'asc')->get();

        return view('Pages.ManajemenSistem.menu', [
            'title' => 'Manajemen Menu - SAPA MS ACEH',
            'menus' => $menus,
        ]);
    }

    /**
     * Menyimpan data menu baru
     */
    public function store(Request $request)
    {
        // Validasi: Jika is_dropdown = 0 (false), maka URL Wajib diisi!
        $request->validate([
            'menu_name'   => 'required|string|max:255',
            'is_dropdown' => 'required|boolean',
            'url'         => 'required_if:is_dropdown,0,false|nullable|string|max:255',
            'icon'        => 'nullable|string|max:255',
            'order'       => 'nullable|integer',
        ], [
            'menu_name.required' => 'Nama menu wajib diisi.',
            'url.required_if'    => 'URL wajib diisi jika menu bukan merupakan dropdown.',
        ]);

        Menu::create([
            'menu_name'   => $request->menu_name,
            'is_dropdown' => $request->is_dropdown,
            'url'         => $request->is_dropdown ? null : $request->url,
            'icon'        => $request->icon,
            'order'       => $request->order ?? 0,
            'is_active'   => $request->has('is_active') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Memperbarui data menu
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'menu_name'   => 'required|string|max:255',
            'is_dropdown' => 'required|boolean',
            'url'         => 'required_if:is_dropdown,0,false|nullable|string|max:255',
            'icon'        => 'nullable|string|max:255',
            'order'       => 'nullable|integer',
        ], [
            'menu_name.required' => 'Nama menu wajib diisi.',
            'url.required_if'    => 'URL wajib diisi jika menu bukan merupakan dropdown.',
        ]);

        $menu->update([
            'menu_name'   => $request->menu_name,
            'is_dropdown' => $request->is_dropdown,
            'url'         => $request->is_dropdown ? null : $request->url,
            'icon'        => $request->icon,
            'order'       => $request->order ?? 0,
            'is_active'   => $request->has('is_active') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Data menu berhasil diperbarui.');
    }

    /**
     * Menghapus menu
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->back()->with('success', 'Menu berhasil dihapus.');
    }
}