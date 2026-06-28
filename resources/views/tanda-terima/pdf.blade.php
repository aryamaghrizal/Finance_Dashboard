@php
    // Variabel ini akan dikirim dari Controller
    $perusahaan = $tandaTerima->perusahaan;
    $fakturDibayar = $tandaTerima->fakturPenjualans;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima Pembayaran - {{ $tandaTerima->nomor_pembayaran }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 25px;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 16px;
        }
        hr {
            border: 0;
            height: 1px;
            background-color: #999;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        .no-border td {
            border: none;
            padding: 3px 0;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-box {
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Header Perusahaan -->
    <table class="no-border" style="margin-bottom: 0;">
        <tr>
            <td style="width: 100px; vertical-align: top;">
                <img src="{{ public_path('logo-cac.png') }}" style="width: 100px; height: auto;">
            </td>
            <td style="vertical-align: top;">
                <div style="line-height: 1.5; margin-left: 12px; margin-top: 10px;">
                    <div style="font-size: 16px; font-weight: bold;">CV CEMERLANG AIR COND</div>
                    <div style="font-size: 11px;">
                        Jl. Masjid Raya No.92 C, Tompo Balang, Kec. Botoala.<br>
                        Kota Makassar, Sulawesi Selatan 90151<br>
                        Indonesia
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <h2>Tanda Terima Pembayaran</h2>
    <hr>

    <table class="no-border">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Diterima dari:</strong><br>
                {{ $perusahaan->nama }}<br>
                {{ $perusahaan->alamat }}
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                <table class="no-border">
                    <tr>
                        <td style="width: 40%;"><strong>No. Pembayaran</strong></td>
                        <td>: {{ $tandaTerima->nomor_pembayaran }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tgl. Pembayaran</strong></td>
                        <td>: {{ $tandaTerima->tanggal_pembayaran->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Bank</strong></td>
                        <td>: {{ $tandaTerima->bank ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>No. Cek/Giro</strong></td>
                        <td>: {{ $tandaTerima->nomor_cek ?? '-' }}</td>
                    </tr>
                     <tr>
                        <td><strong>Tanggal Cek</strong></td>
                        <td>: {{ $tandaTerima->tanggal_cek ? $tandaTerima->tanggal_cek->format('d M Y') : '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p>Telah kami terima pembayaran atas faktur-faktur berikut:</p>

    <table class="table-bordered">
        <thead style="background-color:#f0f0f0;">
            <tr>
                <th style="width:10%">No.</th>
                <th style="width:35%">No. Faktur</th>
                <th style="width:30%">Tanggal Faktur</th>
                <th style="width:25%">Nilai Faktur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fakturDibayar as $index => $faktur)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $faktur->nomor_surat }}</td>
                    {{-- PERBAIKAN DI SINI: Menggunakan Carbon::parse() untuk mengubah string menjadi objek tanggal --}}
                    <td class="text-center">{{ $faktur->tanggal_pesanan ? \Carbon\Carbon::parse($faktur->tanggal_pesanan)->format('d M Y') : '-' }}</td>
                    <td class="text-right">Rp{{ number_format($faktur->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="no-border">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <strong>Keterangan:</strong>
                <p>{{ $tandaTerima->keterangan ?? '-' }}</p>
            </td>
            <td style="width: 40%; vertical-align: top;">
                <table class="no-border summary-box">
                    @php
                        $totalFaktur = $fakturDibayar->sum('total');
                        $lebihBayar = $tandaTerima->pembayaran - $totalFaktur;
                    @endphp
                    <tr>
                        <td><strong>Total Faktur Dibayarkan</strong></td>
                        <td class="text-right"><strong>Rp{{ number_format($totalFaktur, 0, ',', '.') }}</strong></td>
                    </tr>
                     <tr>
                        <td><strong>Jumlah Pembayaran</strong></td>
                        <td class="text-right">Rp{{ number_format($tandaTerima->pembayaran, 0, ',', '.') }}</td>
                    </tr>
                     @if($lebihBayar > 0)
                    <tr>
                        <td><strong>Lebih Bayar</strong></td>
                        <td class="text-right">Rp{{ number_format($lebihBayar, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </table>
                 <p style="margin-top: 10px;">
                    <strong>Terbilang:</strong><br>
                    <em>{{ $tandaTerima->terbilang }} Rupiah</em>
                </p>
            </td>
        </tr>
    </table>

     <!-- Tanda Tangan -->
    <table class="no-border" style="width: 100%; margin-top: 60px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="text-align: center; vertical-align: top;">
                <p style="margin: 0;">Diterima Oleh,</p>
                <p style="margin-top: 8px;">CV. Cemerlang Air Cond</p>
                <div style="height: 60px;"></div>
                <strong style="border-bottom: 1px solid #000; padding: 0 40px;">(________________)</strong><br>
            </td>
        </tr>
    </table>
</body>
</html>
