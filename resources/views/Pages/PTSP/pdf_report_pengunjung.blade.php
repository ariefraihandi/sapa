<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengunjung PTSP - {{ $periodeLabel }}</title>
    <style>
        @page { margin: 10mm 10mm 15mm 10mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 8.5pt; color: #333; margin: 0; padding: 0; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .header-title { font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .header-subtitle { font-size: 10pt; margin-bottom: 12px; }
        
        /* Summary Box Grid */
        .summary-box { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .summary-box td { border: 1px solid #198754; padding: 8px; text-align: center; background-color: #f8f9fa; }
        .summary-num { font-size: 14pt; font-weight: bold; color: #0d6efd; }
        .summary-label { font-size: 7.5pt; text-transform: uppercase; color: #555; }

        /* Demografi Table */
        .stat-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 12px; }
        .stat-table td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
        .stat-header { background-color: #e9ecef; font-weight: bold; text-align: left; padding: 4px 8px; font-size: 8.5pt; margin-bottom: 4px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.data-table th, table.data-table td { border: 1px solid #444; padding: 4px 5px; font-size: 8pt; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        table.data-table tfoot td { background-color: #e9ecef; font-weight: bold; }

        .section-title { font-size: 9.5pt; font-weight: bold; margin-top: 12px; margin-bottom: 6px; padding: 4px 8px; background-color: #e9ecef; border-left: 4px solid #0d6efd; }
        .satker-group-header { font-size: 10pt; font-weight: bold; margin-top: 15px; margin-bottom: 5px; padding: 5px; background-color: #d1e7dd; color: #0f5132; }

        .badge-tatap-muka { color: #0d6efd; font-weight: bold; }
        .badge-sapa { color: #6f42c1; font-weight: bold; }

        .signature-wrapper { page-break-inside: avoid; margin-top: 15px; width: 100%; }
        .app-footer { position: fixed; bottom: -10px; left: 0px; right: 0px; font-size: 7pt; color: #777; border-top: 1px solid #ddd; padding-top: 3px; font-style: italic; }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <div class="text-center">
        <div class="header-title">LAPORAN REKAPITULASI & ANALISIS PENGUNJUNG PTSP</div>
        <div class="header-title">{{ $namaSatker }}</div>
        <div class="header-subtitle">
            PERIODE: {{ strtoupper($periodeLabel) }} 
            @if($samarkan) <span style="color:red;">(DETAIL DISAMARKAN)</span> @endif
            @if($excludeMsAceh) <span style="color:blue;">(EXCLUDE MS ACEH)</span> @endif
        </div>
    </div>

    <!-- ================= 1. KOTAK EKSEKUTIF SUMMARY GLOBAL ================= -->
    <table class="summary-box">
        <tr>
            <td style="width: 25%;">
                <div class="summary-num">{{ $totalKeseluruhan }}</div>
                <div class="summary-label">Total Kunjungan</div>
            </td>
            <td style="width: 25%;">
                <div class="summary-num" style="color: #198754;">{{ $totalFisik }}</div>
                <div class="summary-label">Pengunjung Fisik</div>
            </td>
            <td style="width: 25%;">
                <div class="summary-num" style="color: #0dcaf0;">{{ $totalSapa }}</div>
                <div class="summary-label">Layanan Online (SAPA)</div>
            </td>
            <td style="width: 25%;">
                <div class="summary-num" style="color: #fd7e14;">{{ $avgDurasiFormatted }}</div>
                <div class="summary-label">Rata-Rata Durasi Layanan</div>
            </td>
        </tr>
    </table>

    <!-- ================= 2. TABEL REKAPITULASI & PERFORMANSI SATKER ================= -->
    <div class="section-title">RANGKUMAN LAYANAN & PERFORMANSI KECEPATAN MELAYANI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">RANK</th>
                <th>NAMA MAHKAMAH SYAR'IYAH</th>
                <th width="15%">TATAP MUKA</th>
                <th width="15%">SAPA (ONLINE)</th>
                <th width="15%">TOTAL</th>
                <th width="22%">RATA-RATA WAKTU MELAYANI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rankingSatker as $rank => $stkStat)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $stkStat['satker_name'] }}</strong></td>
                    <td class="text-center">{{ $stkStat['tatap_muka'] }} Orang</td>
                    <td class="text-center">{{ $stkStat['sapa'] }} Orang</td>
                    <td class="text-center"><strong>{{ $stkStat['total'] }} Orang</strong></td>
                    <td class="text-center"><strong>{{ $stkStat['avg_durasi'] }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center"><em>Tidak ada data rekapitulasi.</em></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ================= 3. DEMOGRAFI GLOBAL (HANYA 1 KALI DITAMPILKAN) ================= -->
    <table class="stat-table">
        <tr>
            <!-- STATISTIK PEKERJAAN -->
            <td style="width: 33%;">
                <div class="stat-header">DEMOGRAFI PEKERJAAN</div>
                <table style="width: 100%; border: none;">
                    @foreach($statPekerjaan as $pekerjaan => $count)
                        @php $persen = $totalKeseluruhan > 0 ? round(($count / $totalKeseluruhan) * 100, 1) : 0; @endphp
                        <tr>
                            <td style="border:none;">{{ $pekerjaan }}</td>
                            <td style="border:none; text-align: right; font-weight: bold;">{{ $count }} ({{ $persen }}%)</td>
                        </tr>
                    @endforeach
                </table>
            </td>

            <!-- STATISTIK USIA -->
            <td style="width: 33%;">
                <div class="stat-header">RENTANG USIA RESPONDEN</div>
                <table style="width: 100%; border: none;">
                    @foreach($statUsia as $range => $count)
                        @php $persenUsia = $totalKeseluruhan > 0 ? round(($count / $totalKeseluruhan) * 100, 1) : 0; @endphp
                        <tr>
                            <td style="border:none;">{{ $range }}</td>
                            <td style="border:none; text-align: right; font-weight: bold;">{{ $count }} ({{ $persenUsia }}%)</td>
                        </tr>
                    @endforeach
                </table>
            </td>

            <!-- STATISTIK KABUPATEN / KOTA TERBANYAK -->
            <td style="width: 34%;">
                <div class="stat-header">WILAYAH ASAL (KAB/KOTA) TERBANYAK</div>
                <table style="width: 100%; border: none;">
                    @foreach($statKabkot->take(6) as $namaKabkot => $count)
                        @php $persenKab = $totalKeseluruhan > 0 ? round(($count / $totalKeseluruhan) * 100, 1) : 0; @endphp
                        <tr>
                            <td style="border:none;">{{ $namaKabkot }}</td>
                            <td style="border:none; text-align: right; font-weight: bold;">{{ $count }} ({{ $persenKab }}%)</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <div style="page-break-after: always;"></div>

    <!-- ================= 4. RINCIAN DATA PER-SATKER ================= -->
    @foreach($rankingSatker as $stkData)
        <div class="satker-group-header">
            SATKER: {{ strtoupper($stkData['satker_name']) }} (Total: {{ $stkData['total'] }} Kunjungan)
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th width="3%">NO</th>
                    <th width="12%">TANGGAL & WAKTU</th>
                    <th width="16%">NAMA / NIK</th>
                    <th width="13%">NO. HP / EMAIL</th>
                    <th width="10%">LAYANAN</th>
                    <th width="14%">PEKERJAAN / USIA</th>
                    <th>KEPERLUAN</th>
                    <th width="8%">STATUS</th>
                    <th width="8%">DURASI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stkData['items'] as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d/m/Y H:i') }}</td>
                        <td>
                            <strong>{{ $item->nama_responden }}</strong><br>
                            <small>NIK: {{ $item->nik ?? '-' }}</small>
                        </td>
                        <td>
                            {{ $item->no_hp ?? '-' }}<br>
                            <small>{{ $item->email ?? '-' }}</small>
                        </td>
                        <td class="text-center" style="text-transform: capitalize;">
                            @if($item->jenis_layanan === 'tatap_muka')
                                <span class="badge-tatap-muka">Tatap Muka</span>
                            @else
                                <span class="badge-sapa">{{ str_replace('_', ' ', $item->jenis_layanan) }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $item->pekerjaan ?? '-' }}<br>
                            <small>Usia: {{ $item->usia ? $item->usia . ' Thn' : '-' }}</small>
                        </td>
                        <td>{{ $item->nama_keperluan }}</td>
                        <td class="text-center">
                            @if($item->is_tindak_lanjut)
                                <span style="color: green; font-weight: bold;">Selesai</span>
                            @else
                                <span style="color: red; font-weight: bold;">Belum</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->formatted_durasi_item }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center"><em>Tidak ada rincian data pengunjung.</em></td>
                    </tr>
                @endforelse
            </tbody>
            <!-- FOOTER TABEL: KESIMPULAN USER & DURASI -->
            <tfoot>
                <tr>
                    <td colspan="4" class="text-bold">KESIMPULAN SATKER: {{ strtoupper($stkData['satker_name']) }}</td>
                    <td colspan="3">
                        Tatap Muka: <strong>{{ $stkData['tatap_muka'] }}</strong> | SAPA: <strong>{{ $stkData['sapa'] }}</strong> | Total: <strong>{{ $stkData['total'] }} Pengguna</strong>
                    </td>
                    <td colspan="2" class="text-center">
                        Rata-Rata: <strong>{{ $stkData['avg_durasi'] }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    @endforeach

    <!-- SIGNATURE CONTAINER -->
    <div class="signature-wrapper">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 60%; border:none;"></td>
                <td style="width: 40%; text-align: center; border:none;">
                    <p style="margin: 0;">{{ $namaKota }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p style="margin: 5px 0 50px 0;">Penanggung Jawab PTSP,</p>
                    <br><br><br><br>
                    <p style="margin: 0;"><strong>{{ $namaPj }}</strong></p>
                </td>
            </tr>
        </table>
    </div>

    <!-- FOOTER HALAMAN -->
    <div class="app-footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none;">Dokumen ini dicetak otomatis oleh Aplikasi SAPA MS Aceh pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB.</td>
                <td style="border: none; text-align: right;">SAPA MS ACEH - Layanan PTSP Online</td>
            </tr>
        </table>
    </div>

</body>
</html>