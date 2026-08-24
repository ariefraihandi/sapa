<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use App\Models\Submenu;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 1. Gunakan path() tanpa query string (misal: 'system/users' meskipun URL ada ?satker_id=123)
        $currentUrl = trim($request->path(), '/');

        // 2. Bypass URL umum & Dashboard
        $whiteList = ['dashboard', '/', 'profile', 'profile/update'];
        if (in_array($currentUrl, $whiteList)) {
            return $next($request);
        }

        // 3. Normalisasi URL untuk Sub-action (misal: 'pengguna/admin/store' diambil segmen utamanya 'pengguna/admin')
        $segments = explode('/', $currentUrl);
        if (count($segments) > 2) {
            // Mengambil 2 segmen pertama saja (contoh: 'system/menu/store' -> 'system/menu')
            $currentUrl = $segments[0] . '/' . $segments[1];
        }

        // 4. Cek Akses di Submenu
        $submenu = Submenu::where('url', $currentUrl)->first();

        if ($submenu) {
            $hasAccess = $user->role->submenuAccesses()
                              ->where('submenu_id', $submenu->id)
                              ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses ke halaman ini!');
            }

            return $next($request);
        }

        // 5. Cek Akses di Menu Utama (Non-dropdown)
        $menu = Menu::where('url', $currentUrl)->first();

        if ($menu) {
            $hasAccess = $user->role->menuAccesses()
                              ->where('menu_id', $menu->id)
                              ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses ke halaman ini!');
            }

            return $next($request);
        }

        // Jika URL benar-benar tidak terdaftar di database Menu/Submenu
        return redirect()->back()->with('error', 'Halaman atau URL tidak ditemukan dalam sistem!');
    }
}