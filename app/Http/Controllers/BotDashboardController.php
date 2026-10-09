<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotDashboardController extends Controller
{
    // Updated port based on user request
    private $baseUrl = 'http://localhost:8080';

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (!$user) {
                return redirect()->route('login');
            }

            $roleName = strtolower($user->role->role_name ?? '');
            if ($roleName !== 'admin' && $roleName !== 'super admin' && $roleName !== 'superadmin') {
                return redirect()->route('dashboard')->with('error', 'Akses ditolak. Menu ini hanya boleh diakses oleh Admin.');
            }
            return $next($request);
        });
    }

    private function apiClient()
    {
        return Http::timeout(5)->withBasicAuth(
            env('RENCONG_GATE_USER'), 
            env('RENCONG_GATE_PASSWORD')
        );
    }

    public function sapaan()
    {
        $title = 'Pengaturan Sapaan Otomatis';
        try {
            $response = $this->apiClient()->get("{$this->baseUrl}/api/faqs");
            $data = $response->json()['data'] ?? [];
            $sapaan = collect($data)->firstWhere('keyword', '[SAPAAN_AWAL]');
        } catch (\Exception $e) {
            $sapaan = null;
            session()->flash('error', 'Gagal terhubung ke API Bot (Node.js di port 8080). Pastikan Bot sedang berjalan.');
        }

        return view('Pages.BotDashboard.sapaan', compact('title', 'sapaan'));
    }

    public function sapaanUpdate(Request $request, $id)
    {
        $request->validate([
            'answer' => 'required|string'
        ]);

        try {
            $response = $this->apiClient()->put("{$this->baseUrl}/api/faqs/{$id}", [
                'keyword' => '[SAPAAN_AWAL]',
                'answer' => $request->answer,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);

            if ($response->successful()) {
                return redirect()->route('bot.sapaan')->with('success', 'Sapaan awal berhasil diperbarui.');
            }
        } catch (\Exception $e) {
            // Error handling fallback
        }

        return redirect()->back()->with('error', 'Gagal memperbarui sapaan awal. Pastikan API Bot aktif.');
    }

    public function faqs()
    {
        $title = 'Manajemen FAQ Bot';
        try {
            $response = $this->apiClient()->get("{$this->baseUrl}/api/faqs");
            $data = $response->json()['data'] ?? [];
            $faqs = collect($data)->values();
        } catch (\Exception $e) {
            $faqs = collect([]);
            session()->flash('error', 'Gagal terhubung ke API Bot (Node.js di port 8080).');
        }

        return view('Pages.BotDashboard.faqs', compact('title', 'faqs'));
    }

    public function faqsStore(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string',
            'answer' => 'required|string',
        ]);

        try {
            $response = $this->apiClient()->post("{$this->baseUrl}/api/faqs", [
                'keyword' => $request->keyword,
                'answer' => $request->answer,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);

            if ($response->successful()) {
                return redirect()->route('bot.faqs')->with('success', 'FAQ berhasil ditambahkan.');
            }
        } catch (\Exception $e) {}

        return redirect()->back()->with('error', 'Gagal menambahkan FAQ. Pastikan API Bot aktif.');
    }

    public function faqsUpdate(Request $request, $id)
    {
        $request->validate([
            'keyword' => 'required|string',
            'answer' => 'required|string',
        ]);

        try {
            $response = $this->apiClient()->put("{$this->baseUrl}/api/faqs/{$id}", [
                'keyword' => $request->keyword,
                'answer' => $request->answer,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);

            if ($response->successful()) {
                return redirect()->route('bot.faqs')->with('success', 'FAQ berhasil diperbarui.');
            }
        } catch (\Exception $e) {}

        return redirect()->back()->with('error', 'Gagal memperbarui FAQ. Pastikan API Bot aktif.');
    }

    public function faqsDestroy($id)
    {
        try {
            $response = $this->apiClient()->delete("{$this->baseUrl}/api/faqs/{$id}");

            if ($response->successful()) {
                return redirect()->route('bot.faqs')->with('success', 'FAQ berhasil dihapus.');
            }
        } catch (\Exception $e) {}

        return redirect()->back()->with('error', 'Gagal menghapus FAQ. Pastikan API Bot aktif.');
    }

    public function users()
    {
        $title = 'Riwayat Pengguna Bot';
        try {
            $response = $this->apiClient()->get("{$this->baseUrl}/api/bot_users");
            $users = $response->json()['data'] ?? [];
        } catch (\Exception $e) {
            $users = [];
            session()->flash('error', 'Gagal terhubung ke API Bot (Node.js di port 8080).');
        }

        return view('Pages.BotDashboard.users', compact('title', 'users'));
    }
}
