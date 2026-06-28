@php
  $perusahaan = $pesanan->perusahaan;
  $items = $pesanan->items;
@endphp

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: 'Arial', sans-serif;
      font-size: 12px;
      color: #333;
      margin: 25px;
    }

    h2 {
      text-align: center;
      margin-bottom: 10px;
      text-transform: uppercase;
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
      margin-bottom: 20px;
    }

    th,
    td {
      border: 1px solid #ccc;
      padding: 8px;
    }

    .no-border td {
      border: none;
      padding: 4px 0;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .signature {
      margin-top: 60px;
      text-align: center;
    }

    .footer-note {
      font-size: 11px;
      margin-top: 30px;
      color: #666;
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


  <h2>Surat Pesanan Penjualan</h2>
  <hr>

  <table class="no-border" style="margin-top: 10px; margin-bottom: 15px;">
    <tr>
      <td style="width: 50%; vertical-align: top;">
        <strong>Kepada:</strong><br>
        {{ $perusahaan->nama }}<br>
        {{ $perusahaan->alamat }}
      </td>
      <td style="width: 50%; vertical-align: top; padding-left: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
          <tr>
            <td style="border: 1px solid #000; padding: 4px;"><strong>No Surat</strong></td>
            <td style="border: 1px solid #000; padding: 4px;">{{ $pesanan->nomor_surat }}</td>
          </tr>
          <tr>
            <td style="border: 1px solid #000; padding: 4px;"><strong>Tanggal</strong></td>
            <td style="border: 1px solid #000; padding: 4px;">{{ $pesanan->tanggal_pesanan }}</td>
          </tr>
          <tr>
            <td style="border: 1px solid #000; padding: 4px;"><strong>PO No</strong></td>
            <td style="border: 1px solid #000; padding: 4px;">{{ $pesanan->po_no }}</td>
          </tr>
          <tr>
            <td style="border: 1px solid #000; padding: 4px;"><strong>Pengiriman</strong></td>
            <td style="border: 1px solid #000; padding: 4px;">{{ $pesanan->tanggal_pengiriman }}</td>
          </tr>
          <tr>
            <td style="border: 1px solid #000; padding: 4px;"><strong>Syarat Pembayaran</strong></td>
            <td style="border: 1px solid #000; padding: 4px;">{{ $pesanan->syarat_pembayaran }}</td>
          </tr>
          <tr>
            <td style="border: 1px solid #000; padding: 4px;"><strong>FOB</strong></td>
            <td style="border: 1px solid #000; padding: 4px;">{{ $pesanan->fob ?? '-' }}</td>
          </tr>
          <tr>
            <td style="border: 1px solid #000; padding: 4px;"><strong>Ekspedisi</strong></td>
            <td style="border: 1px solid #000; padding: 4px;">{{ $pesanan->ekspedisi ?? '-' }}</td>
          </tr>
          <tr>
            <td style="border: 1px solid #000; padding: 4px;"><strong>Penjual</strong></td>
            <td style="border: 1px solid #000; padding: 4px;">{{ $pesanan->penjual ?? '-' }}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <table>
    <thead style="background-color:#f0f0f0;">
      <tr>
        <th style="width:10%">Kode</th>
        <th style="width:40%">Nama Barang</th>
        <th style="width:10%">Qty</th>
        <th style="width:15%">@Harga</th>
        <th style="width:10%">Diskon</th>
        <th style="width:15%">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $item)
      <tr>
      <td>{{ $item->barang->kode }}</td>
      <td>{{ $item->barang->nama }}</td>
      <td class="text-center">{{ $item->kuantitas }}</td>
      <td class="text-right">Rp{{ number_format($item->harga, 0, ',', '.') }}</td>
      <td class="text-right">Rp{{ number_format($item->diskon, 0, ',', '.') }}</td>
      <td class="text-right">Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <table class="no-border">
    <tr>
      <td style="width: 60%">
        <strong>Terbilang:</strong><br>
        <em>{{ $pesanan->terbilang }}</em>
        <br><br>
        <strong>Keterangan:</strong> {{ $pesanan->keterangan ?? '-' }}
      </td>
      <td style="width: 40%">
        <table>
          <tr>
            <td>Subtotal</td>
            <td class="text-right">Rp{{ number_format($pesanan->subtotal, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td>Diskon</td>
            <td class="text-right">Rp{{ number_format($pesanan->diskon_total, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td>PPN 11%</td>
            <td class="text-right">Rp{{ number_format($pesanan->ppn, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td>Biaya Lain</td>
            <td class="text-right">Rp{{ number_format($pesanan->biaya_lain, 0, ',', '.') }}</td>
          </tr>
          <tr style="background-color:#e0e0e0;">
            <th>Total</th>
            <th class="text-right">Rp{{ number_format($pesanan->total, 0, ',', '.') }}</th>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- Tanda Tangan -->
  <table class="no-border" style="width: 100%; margin-top: 60px;">
    <tr>
      <td style="width: 60%;"></td>
      <td style="text-align: center;">
        <p style="margin: 0 0 0 0;">Disetujui,</p>
        <img src="{{ public_path('ttd-erwin.png') }}" style="height: 100px;"><br>
        <strong>ERWIN ERIAN YOS, S.T</strong><br>
        <span style="display: inline-block; border-top: 1px solid #000; width: 200px; margin-top: 4px;"></span>
      </td>
    </tr>
  </table>


  <div class="footer-note">
    Dokumen ini dicetak secara otomatis dan tidak memerlukan tanda tangan basah.
  </div>

</body>

</html>