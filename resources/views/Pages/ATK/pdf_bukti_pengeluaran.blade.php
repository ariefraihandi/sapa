<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DAFTAR URAIAN PENGGUNAAN BARANG ATK PERKARA</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .nomor {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 25px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.data-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .col-no { width: 6%; text-align: center; }
        .col-nama { width: 38%; }
        .col-jumlah { width: 14%; text-align: center; }
        .col-ruang { width: 22%; text-align: center; }
        .col-ket { width: 20%; text-align: center; }
        
        .row-number td {
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            padding: 2px;
            background-color: #fafafa;
        }
        .empty-row td {
            height: 25px;
        }

        /* Layout 3 Kolom Tanda Tangan Presisi */
        .ttd-container {
            width: 100%;
            margin-top: 15px;
        }
        .ttd-table {
            width: 100%;
            border: none;
            table-layout: fixed;
        }
        .ttd-table td {
            border: none;
            vertical-align: top;
            padding: 0;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- JUDUL DOKUMEN -->
    <div class="title">
        DAFTAR URAIAN PENGGUNAAN BARANG ATK PERKARA {{ $namaSatker }}
    </div>
    <div class="nomor">
        NOMOR : {{ $trx->nomor_dokumen ?? '..... / ' . date('Y') }}
    </div>

    <!-- TABEL UTAMA -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-nama">NAMA BARANG</th>
                <th class="col-jumlah">JUMLAH</th>
                <th class="col-ruang">RUANG</th>
                <th class="col-ket">KET</th>
            </tr>
            <tr class="row-number">
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td class="text-bold">{{ $trx->item->nama_barang ?? '-' }}</td>
                <td class="text-center text-bold">{{ $trx->jumlah }} {{ $trx->item->satuan ?? '' }}</td>
                <td class="text-center">{{ $trx->ruangan ?? '-' }}</td>
                <td class="text-center">{{ $trx->keterangan ?? '-' }}</td>
            </tr>
            <!-- Baris Kosong Tambahan Sesuai Format Cetakan Fisik -->
            @for($i = 0; $i < 4; $i++)
            <tr class="empty-row">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <!-- TANDA TANGAN 3 KOLOM -->
    <div class="ttd-container">
        <table class="ttd-table">
            <!-- Baris 1: Tanggal di Kolom Paling Kanan -->
            <tr>
                <td style="width: 33.33%;"></td>
                <td style="width: 33.33%;"></td>
                <td style="width: 33.33%;">
                    {{ $kotaSatker }}, {{ $tanggalFormatted }}
                </td>
            </tr>

            <!-- Baris 2: Jabatan 3 Kolom -->
            <tr>
                <td style="text-align: left; padding-top: 5px;">
                    Mengetahui,<br>
                    <strong>Panmud Banding</strong>
                </td>
                <td style="padding-top: 5px;">
                    <br>
                    <strong>Pengelola Barang</strong>
                </td>
                <td style="padding-top: 5px;">
                    <br>
                    <strong>Yang Menerima / Pengambil</strong>
                </td>
            </tr>

            <!-- Baris 3: Space Tanda Tangan -->
            <tr>
                <td style="height: 60px;"></td>
                <td style="height: 60px;"></td>
                <td style="height: 60px;"></td>
            </tr>

            <!-- Baris 4: Nama TTD -->
            <tr>
                <td style="text-align: left;">
                    <u><strong>Abd. Latif, S.H., M.H</strong></u>
                </td>
                <td>
                    <u><strong>ZULDIATI, S.H</strong></u>
                </td>
                <td>
                    <u><strong>{{ strtoupper($trx->nama_pengambil ?? '..........................................') }}</strong></u>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>