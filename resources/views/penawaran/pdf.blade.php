<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 30px;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            font-size: 18px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        td,
        th {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
        }

        .no-border td {
            border: none;
            padding: 4px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary td {
            border: none;
            padding: 4px 6px;
        }

        /* ----- PERUBAHAN UTAMA DI SINI ----- */
        .total-box {
            margin-top: 0px;
            /* Nilai negatif untuk menariknya lebih ke atas. Sesuaikan! */
            width: 40%;
            float: right;
            border: 1px solid #999;
            padding: 8px;
        }

        .logo {
            width: 100px;
        }

        .table-keterangan {
            float: left;
            margin-bottom: 16px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* ----- STYLES BARU DAN DIREVISI UNTUK TTD ----- */
        .signature-area {
            margin-top: 50px;
            /* Jarak yang lebih besar dari total box/keterangan */
            clear: both;
            /* Pastikan membersihkan float dari elemen di atasnya */
            width: 100%;
            box-sizing: border-box;
            /* Pastikan padding dihitung dalam lebar */
        }

        .signature-content {
            width: 40%;
            /* Menyesuaikan lebar blok TTD di kanan */
            float: right;
            text-align: center;
            line-height: 1.4;
            /* Jarak antar baris teks */
        }

        .signature-content strong {
            display: block;
            /* Agar nama dan jabatan berada di baris baru */
        }

        .signature-content .company-name {
            margin-bottom: 8px;
            /* Jarak antara nama perusahaan dan gambar ttd */
        }

        .signature-image {
            width: 120px;
            /* Ukuran gambar TTD, sesuaikan jika perlu */
            height: auto;
            display: block;
            /* Agar bisa di-center */
            margin: 0 auto;
            /* Tengah secara horizontal */
            margin-top: 5px;
            /* Jarak di atas gambar TTD */
            margin-bottom: 5px;
            /* Jarak di bawah gambar TTD */
        }

        .signature-name {
            margin-top: 10px;
            /* Jarak antara gambar TTD dan nama tertulis */
            border-bottom: 1px solid #000;
            /* Garis bawah nama */
            display: inline-block;
            /* Agar garis bawah hanya sepanjang teks */
            padding-bottom: 2px;
            font-weight: bold;
        }

        .signature-title {
            display: block;
            /* Agar jabatan di baris baru */
            font-size: 12px;
            margin-top: 2px;
        }

        hr {
            border: none;
            border-top: 1px solid #444;
            width: 100%;
            margin: 0 auto 20px auto;
        }
    </style>
</head>

<body>

    <table class="no-border">
        <tr>
            <td style="width: 100px;">
                <img src="{{ public_path('logo-cac.png') }}" class="logo">
            </td>
            <td>
                <strong style="font-size: 16px;">CV CEMERLANG AIR COND</strong><br>
                Jl. Masjid Raya No.92 C, Tompo Balang, Makassar<br>
                Sulawesi Selatan 90151 - Indonesia
            </td>
        </tr>
    </table>

    <h2>SURAT PENAWARAN HARGA</h2>
    <hr>


    <table class="no-border" style="margin-bottom: 10px;">
        <tr>
            <td style="width: 50%;">
                <strong>Kepada:</strong><br>
                {{ $surat->perusahaan->nama }}<br>
                {{ $surat->perusahaan->alamat }}
            </td>
            <td style="width: 50%;">
                <table class="no-border" style="width: 100%;">
                    <tr>
                        <td style="width: 35%;"><strong>No. Surat</strong></td>
                        <td style="width: 5%;">:</td>
                        <td>{{ $surat->nomor }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal</strong></td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($surat->tanggal)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pembayaran</strong></td>
                        <td>:</td>
                        <td>{{ $surat->pembayaran }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Kode Barang</th>
                <th style="width: 35%;">Nama Barang</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 15%;">@Harga</th>
                <th style="width: 15%;">Diskon</th>
                <th style="width: 20%;">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($surat->items as $i => $item)
                @php
                    $subtotalItem = $item->harga * $item->kuantitas;
                    $diskonAmount = $item->diskon ? $subtotalItem * ($item->diskon / 100) : 0;
                    $totalItem = $subtotalItem - $diskonAmount;
                    $total += $totalItem;
                @endphp
                <tr>
                    <td class="text-center">{{ $item->barang->kode }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td class="text-center">{{ $item->kuantitas }}</td>
                    <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $item->diskon }}%</td>
                    <td class="text-right">{{ number_format($totalItem, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- CONTAINER UNTUK KETERANGAN DAN TOTAL BOX --}}
    <div class="clearfix">
        <table style="width: 50%;" class="table-keterangan">
            <tr>
                <td style="border: 1px solid #999; padding: 8px;">
                    <strong>Keterangan :</strong><br>
                    {{ $surat->keterangan ?? '-' }}
                </td>
            </tr>
        </table>

        @php
            $diskonTotal = 0;
            $ppn = 0;
            if ($surat->ppn)
                $ppn = $total * 0.11;
            $grandTotal = $total + $ppn + $surat->biaya_lain;
        @endphp

        <div class="total-box">
            <table class="summary">
                <tr>
                    <td style="width: 60%;">Sub Total</td>
                    <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
                </tr>
                @if($surat->ppn)
                    <tr>
                        <td>PPN 11%</td>
                        <td class="text-right">{{ number_format($ppn, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Biaya Lain-lain</td>
                    <td class="text-right">{{ number_format($surat->biaya_lain, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>
    </div> {{-- End of clearfix --}}

    <table class="no-border" style="width: 100%; margin-top: 60px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="text-align: center;">
                <p style="margin: 0 0 0 0;">Hormat Kami</p>
                <p>CV. Cemerlang Air Cond</p>
                <img src="{{ public_path('ttd-erwin.png') }}" style="height: 100px;"><br>
                <strong>ERWIN ERIAN YOS, S.T</strong><br>
                <span style="display: inline-block; border-top: 1px solid #000; width: 200px; margin-top: 4px; "></span>
                <p style="margin: 0 0 0 0;">Direktur</p>

            </td>
        </tr>
    </table>

</body>

</html>