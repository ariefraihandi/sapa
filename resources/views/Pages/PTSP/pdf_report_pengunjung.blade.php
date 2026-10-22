<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengunjung PTSP - {{ $periodeLabel }}</title>
    <style>
        @page {
            margin: 10mm 10mm 15mm 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .header-subtitle {
            font-size: 11pt;
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 6px;
            padding: 4px 8px;
            background-color: #e9ecef;
            border-left: 4px solid #0d6efd;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #444;
            padding: 5px 6px;
            font-size: 8.5pt;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        
        /* Layout Tanda Tangan */
        .signature-wrapper {
            page-break-inside: avoid;
            margin-top: 20px;
            width: 100%;
        }
        table.sign-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }
        table.sign-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        /* Footer Otomatis Aplikasi SAPA */
        .app-footer {
            position: fixed;
            bottom: -10px;
            left: 0px;
            right: 0px;
            font-size: 7.5pt;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 4px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <div class="text-center">
        <div class="header-title">LAPORAN REKAPITULASI PENGUNJUNG PTSP</div>
        <div class="header-title">{{ $namaSatker }}</div>
        <div class="header-subtitle">PERIODE: {{ strtoupper($periodeLabel) }}</div>
    </div>

    <!-- ================= 1. TABEL PENGUNJUNG PTSP FISIK ================= -->
    <div class="section-title">
        A. DAFTAR PENGUNJUNG PTSP FISIK (TATAP MUKA)
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="12%">TANGGAL & WAKTU</th>
                <th width="15%">NAMA / NIK</th>
                <th width="11%">NO. HP / EMAIL</th>
                <th width="12%">PEKERJAAN / USIA</th>
                <th>KEPERLUAN / KONSULTASI</th>
                <th width="11%">STATUS TINDAK LANJUT</th>
                <th width="8%">DURASI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengunjungFisik as $index => $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d/m/Y H:i') }} WIB
                    </td>
                    <td>
                        <strong>{{ $item->nama_responden }}</strong><br>
                        <small>NIK: {{ $item->nik ?? '-' }}</small>
                    </td>
                    <td>
                        {{ $item->no_hp ?? '-' }}<br>
                        <small>{{ $item->email ?? '-' }}</small>
                    </td>
                    <td>
                        {{ $item->pekerjaan ?? '-' }}<br>
                        <small>Usia: {{ $item->usia ? $item->usia . ' Thn' : '-' }}</small>
                    </td>
                    <td>{{ $item->nama_keperluan }}</td>
                    <td class="text-center">
                        @if($item->is_tindak_lanjut)
                            <span style="color: green; font-weight: bold;">Selesai</span><br>
                            <small>{{ \Carbon\Carbon::parse($item->tindak_lanjut_at)->translatedFormat('d/m/Y H:i') }}</small>
                        @else
                            <span style="color: red; font-weight: bold;">Belum</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->format_durasi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 10px;">
                        <em>Tidak ada data kunjungan fisik untuk periode ini.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="font-size: 8.5pt; margin-bottom: 10px;">
        <strong>Subtotal Pengunjung Fisik:</strong> {{ $pengunjungFisik->count() }} Orang
    </div>


    <!-- ================= 2. TABEL PENGGUNA LAYANAN SAPA ================= -->
    <div class="section-title">
        B. DAFTAR PENGGUNA LAYANAN SAPA (ONLINE / PESAN / TELEPON)
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="12%">TANGGAL & WAKTU</th>
                <th width="15%">NAMA / NIK</th>
                <th width="11%">NO. HP / EMAIL</th>
                <th width="8%">LAYANAN</th>
                <th width="12%">PEKERJAAN / USIA</th>
                <th>KEPERLUAN / KONSULTASI</th>
                <th width="11%">STATUS TINDAK LANJUT</th>
                <th width="8%">DURASI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penggunaSapa as $index => $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d/m/Y H:i') }} WIB
                    </td>
                    <td>
                        <strong>{{ $item->nama_responden }}</strong><br>
                        <small>NIK: {{ $item->nik ?? '-' }}</small>
                    </td>
                    <td>
                        {{ $item->no_hp ?? '-' }}<br>
                        <small>{{ $item->email ?? '-' }}</small>
                    </td>
                    <td class="text-center" style="text-transform: capitalize;">
                        {{ str_replace('_', ' ', $item->jenis_layanan) }}
                    </td>
                    <td>
                        {{ $item->pekerjaan ?? '-' }}<br>
                        <small>Usia: {{ $item->usia ? $item->usia . ' Thn' : '-' }}</small>
                    </td>
                    <td>{{ $item->nama_keperluan }}</td>
                    <td class="text-center">
                        @if($item->is_tindak_lanjut)
                            <span style="color: green; font-weight: bold;">Selesai</span><br>
                            <small>{{ \Carbon\Carbon::parse($item->tindak_lanjut_at)->translatedFormat('d/m/Y H:i') }}</small>
                        @else
                            <span style="color: red; font-weight: bold;">Belum</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->format_durasi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 10px;">
                        <em>Tidak ada data pengguna layanan SAPA untuk periode ini.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="font-size: 8.5pt; margin-bottom: 10px;">
        <strong>Subtotal Pengguna SAPA:</strong> {{ $penggunaSapa->count() }} Orang
    </div>


    <!-- ================= CONTAINER TANDA TANGAN ================= -->
    <div class="signature-wrapper">
        <table class="sign-table">
            <tr>
                <!-- Total Keseluruhan Kiri -->
                <td style="width: 60%; font-size: 9pt;">
                    <strong>TOTAL KESELURUHAN KUNJUNGAN:</strong> {{ $totalKeseluruhan }} Orang
                </td>

                <!-- Tanda Tangan Kanan -->
                <td style="width: 40%; text-align: center; font-size: 9pt;">
                    <p style="margin: 0;">{{ $namaKota }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p style="margin: 5px 0 60px 0;">Penanggung Jawab PTSP,</p>
                    <br>
                    <br>
                    <br>
                    <p style="margin: 0;"><strong>{{ $namaPj }}</strong></p>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= FOOTER OTOMATIS ================= -->
    <div class="app-footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    Dokumen ini dicetak otomatis oleh Aplikasi SAPA MS Aceh pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB.
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    SAPA MS ACEH - Layanan PTSP Online
                </td>
            </tr>
        </table>
    </div>

</body>
</html>