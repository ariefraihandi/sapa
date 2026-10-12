<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WorkCalendarService
{
    /**
     * Ambil daftar tanggal merah / libur nasional (Cache 24 Jam)
     */
    public static function getHolidays(?int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;

        return Cache::remember("holidays_{$year}", 86400, function () use ($year) {
            try {
                // API Tanggal Merah Upset Dev
                $response = Http::timeout(5)->get("https://tanggalmerah.upset.dev/api/holidays?year={$year}");
                
                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['data']) && is_array($json['data'])) {
                        $holidays = [];
                        foreach ($json['data'] as $item) {
                            if (isset($item['date'])) {
                                $holidays[$item['date']] = true;
                            }
                        }
                        return $holidays;
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Gagal mengambil API Tanggal Merah: " . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Cek apakah hari libur (Sabtu/Minggu ATAU Libur Nasional)
     */
    public static function isNonWorkingDay(Carbon $date): bool
    {
        if ($date->isWeekend()) {
            return true;
        }

        $holidays = self::getHolidays($date->year);
        $dateStr = $date->format('Y-m-d');

        return isset($holidays[$dateStr]);
    }

    /**
     * Hitung durasi kerja murni (dalam detik)
     */
    public static function calculateWorkingSeconds(Carbon $start, Carbon $end): int
    {
        if ($start->greaterThanOrEqualTo($end)) {
            return 0;
        }

        $totalSeconds = 0;
        $current = $start->copy();

        while ($current->lessThan($end)) {
            $nextDay = $current->copy()->startOfDay()->addDay();

            if (!self::isNonWorkingDay($current)) {
                // Jam Masuk Selalu 08:00
                $workStart = $current->copy()->setTime(8, 0, 0);

                // Jam Pulang: Jumat jam 16:30, Senin-Kamis jam 17:00
                if ($current->isFriday()) {
                    $workEnd = $current->copy()->setTime(16, 30, 0);
                } else {
                    $workEnd = $current->copy()->setTime(17, 0, 0);
                }

                $effectiveStart = $current->greaterThan($workStart) ? $current : $workStart;
                $effectiveEnd = $end->lessThan($nextDay) ? $end : $nextDay;

                if ($effectiveEnd->greaterThan($workEnd)) {
                    $effectiveEnd = $workEnd;
                }

                if ($effectiveStart->lessThan($effectiveEnd) && $effectiveStart->lessThan($workEnd)) {
                    $totalSeconds += $effectiveStart->diffInSeconds($effectiveEnd);
                }
            }

            $current = $nextDay;
        }

        return $totalSeconds;
    }

    /**
     * Format detik ke format misal: "8j 2m 58d"
     */
    public static function formatSeconds(int $seconds): string
    {
        if ($seconds <= 0) return '-';

        $jam = floor($seconds / 3600);
        $menit = floor(($seconds % 3600) / 60);
        $sisaDetik = $seconds % 60;

        $parts = [];
        if ($jam > 0) $parts[] = "{$jam}j";
        if ($menit > 0) $parts[] = "{$menit}m";
        if ($sisaDetik > 0 || empty($parts)) $parts[] = "{$sisaDetik}d";

        return implode(' ', $parts);
    }
}