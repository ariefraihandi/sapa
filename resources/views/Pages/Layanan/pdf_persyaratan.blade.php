<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Persyaratan Perkara {{ $jenisPerkara->nama_layanan ?? '' }}</title>
    <style>
        /* MARGIN KERTAS UTAMA */
        @page {
            margin-top: 0px;
            margin-left: 0px;
            margin-right: 0px;
            margin-bottom: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #111;
            margin: 0;
            padding: 0;
        }
        
        /* KOP SURAT FULL EDGE TO EDGE */
        .kop-header {
            width: 100%;
            margin: 0;
            padding: 0;
            line-height: 0;
        }
        
        .kop-header img {
            width: 100%;
            height: auto;
            margin: 0;
            padding: 0;
            display: block;
        }

        /* CONTAINER ISI DOKUMEN */
        .content-body {
            padding: 15px 40px 20px 100px;
        }

        .text-center { text-align: center; }
        .title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 11pt;
            font-weight: bold;
            color: #047857;
            margin-bottom: 25px;
        }

        /* WRAPPER GESER KANAN */
        .table-wrapper {
            margin-left: 35px; /* Menggeser posisi tabel ke kanan */
            margin-right: 10px;
        }

        /* TABEL PERSYARATAN STYLING */
        table.persyaratan-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.persyaratan-table td {
            border: none !important;
            padding: 10px 0;
            vertical-align: top;
        }

        /* KOTAK CEKLIS (SIMBOL UNICODE) */
        .checkbox-symbol {
            font-size: 14pt;
            font-weight: normal;
            line-height: 1;
        }
        
        /* DTO PETUGAS PTSP STYLING */
        .signature-section {
            width: 100%;
            margin-top: 40px;
        }
        .signature-table {
            width: 100%;
            border: none !important;
        }
        .signature-table td {
            border: none !important;
            padding: 0;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <!-- GAMBAR KOP SURAT (MENTOK PALING ATAS KERTAS) -->
    <div class="kop-header">
        @if(!empty($satker->kop_surat) && file_exists(public_path('assets/images/satker/' . $satker->kop_surat)))
            <img src="{{ public_path('assets/images/satker/' . $satker->kop_surat) }}">
        @else
            <div style="padding: 20px 40px; text-align: center;">
                <div style="font-size: 14pt; font-weight: bold;">{{ strtoupper($satker->satker_name) }}</div>
                <div style="font-size: 9pt;">{{ $satker->alamat ?? 'Wilayah Hukum Provinsi Aceh' }}</div>
            </div>
        @endif
    </div>

    <!-- WRAPPER ISI DOKUMEN UTAMA -->
    <div class="content-body">
        <!-- JUDUL DOKUMEN -->
        <div class="text-center">
            <div class="title">DAFTAR PERSYARATAN PERKARA</div>
            <div class="subtitle">{{ strtoupper($jenisPerkara->nama_layanan ?? '-') }}</div>
        </div>

        <!-- TABEL PERSYARATAN (DIBUNGKUS WRAPPER AGAR GESER KANAN) -->
        <div class="table-wrapper">
            <table class="persyaratan-table">
                <tbody>
                    @forelse($dokumenList as $index => $doc)
                        <tr>
                            <td style="width: 5%; font-weight: bold;">
                                {{ $index + 1 }}.
                            </td>
                            <td style="width: 85%; line-height: 1.5;">
                                {{ $doc->syarat_dokumen ?? $doc->nama_syarat }}
                            </td>
                            <td style="width: 10%; text-align: right;" class="checkbox-symbol">
                                &#9633;
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center" style="color: #777;">Belum ada dokumen persyaratan yang dikonfigurasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- TANDA TANGAN PETUGAS PTSP -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%; text-align: center;">
                        <p style="margin-bottom: 5px;">
                           Dicetak Pada Tanggal {{ date('d F Y') }}
                        </p>
                        <p style="font-weight: bold; margin-top: 0; margin-bottom: 60px;">
                            Petugas PTSP {{ $satker->satker_name }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>