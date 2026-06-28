<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: 'Arial', sans-serif;
      font-size: 13px;
      margin: 40px;
      color: #333;
    }

    h2 {
      text-align: center;
      text-transform: uppercase;
      font-size: 18px;
      margin-bottom: 5px;
      letter-spacing: 1px;
    }

    hr {
      border: none;
      border-top: 2px solid #444;
      width: 180px;
      margin: 0 auto 20px auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }

    table.table-bordered th,
    table.table-bordered td {
      border: 1px solid #999;
    }

    th,
    td {
      border: 1px solid #999;
    }

    /* Tambahkan ke bagian <style> */
    table.table-bordered td {
      border: 1px solid #999;
    }

    table.table-bordered td.no-border {
      border: none;
    }



    .no-border td {
      border: none;
    }

    .text-center {
      text-align: center;
    }

    .info-table td {
      background-color: #f9f9f9;
      vertical-align: top;
      padding: 8px;
    }

    .keterangan-box {
      border: 1px solid #aaa;
      background-color: #fefefe;
      padding: 8px;
      min-height: 50px;
    }

    .signature-section {
      margin-top: 60px;
    }

    .signature-cell {
      width: 50%;
      text-align: center;
    }

    .line {
      width: 200px;
      border-top: 1px solid #000;
      margin: 40px auto 4px auto;
    }

    .tgl {
      text-align: left;
      margin-left: 50px;
    }

    .totals-box {
      padding: 6px 0;
      line-height: 1.6;
    }
  </style>
</head>

<body>

  <!-- Logo + Nama -->
  <table class="no-border">
    <tr>
      <td style="width: 100px;">
        <img src="{{ public_path('logo-cac.png') }}" style="width: 100px;">
      </td>
      <td>
        <div style="margin-left: 10px;">
          <strong style="font-size: 16px;">CV CEMERLANG AIR COND</strong><br>
          <small>
            Jl. Masjid Raya No.92 C, Tompo Balang, Kec. Botoala.<br>
            Kota Makassar Sulawesi Selatan 90151<br>
            Indonesia
          </small>
        </div>
      </td>
    </tr>
  </table>

  <h2>SURAT JALAN</h2>
  <hr>

  <!-- Informasi Utama (Kepada tanpa border, Detail Surat di kanan dengan border) -->
  <table class="no-border" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
    <tr>
      <!-- KEPADA TANPA BORDER -->
      <td style="width: 50%; padding-right: 20px; vertical-align: top;">
        <div style="padding: 0;">
          <strong>Kepada:</strong><br>
          {{ $surat->perusahaan->nama }}<br>
          <small>{{ $surat->perusahaan->alamat }}</small>
        </div>
      </td>

      <!-- DETAIL SURAT DENGAN BORDER -->
      <td style="width: 50%; vertical-align: top;">
        <table class="no-border"
          style="width: 100%; border-collapse: collapse;border-top: 1px solid #000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">
          <tr>
            <td style="width: 40%; padding: 6px; "><strong>No Surat</strong></td>
            <td style="width: 5%;">:</td>
            <td style="width: 55%;">{{ $surat->nomor }}</td>
          </tr>
          <tr>
            <td style="padding: 6px;"><strong>Tanggal</strong></td>
            <td>:</td>
            <td>{{ $surat->tanggal }}</td>
          </tr>
          <tr>
            <td style="padding: 6px;"><strong>PO No</strong></td>
            <td>:</td>
            <td>{{ $surat->po_no }}</td>
          </tr>
          <tr>
            <td style="padding: 6px;"><strong>Ekspedisi</strong></td>
            <td>:</td>
            <td>{{ $surat->ekspedisi ?? '-' }}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>


  <!-- Tabel Barang -->
  <table>
    <thead style="background-color: #f0f0f0;">
      <tr>
        <th style="width: 25%;">Kode Barang</th>
        <th style="width: 45%;">Nama Barang</th>
        <th style="width: 15%;">Kuantitas</th>
        <th style="width: 15%;">Satuan</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($surat->items as $item)
      <tr>
      <td>{{ $item->barang->kode }}</td>
      <td>{{ $item->barang->nama }}</td>
      <td class="text-center">{{ $item->kuantitas }}</td>
      <td class="text-center">{{ $item->satuan }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <!-- KETERANGAN + TOTAL KUANTITAS dalam FLEX CONTAINER -->
  <div style="display: flex; justify-content: space-between; margin-top: 10px; gap: 10px;">

    <!-- KETERANGAN -->
    <table style="width: 60%; border-collapse: collapse;">
      <tr>
        <td style="border: 1px solid #000; padding: 8px;">
          <strong>Keterangan :</strong><br>
          {{ $surat->keterangan ?? '-' }}
        </td>
      </tr>
    </table>

    <!-- TOTAL -->
    <table class="no-border" style="width: 38%; border-: collapse;">
      <tr>
        <td style="border-top: 1px solid #000; padding: 6px;">
          <strong>Total Kuantitas:</strong> {{ $surat->items->sum('kuantitas') }}
        </td>
      </tr>
      <tr>
        <td style="border-bottom: 1px solid #000; padding: 6px;">
          <strong>Jumlah Barang:</strong> {{ $surat->items->count() }}
        </td>
      </tr>
    </table>

  </div>

  <!-- Tanda Tangan -->
  <table class="no-border signature-section">
    <tr>
      <td class="signature-cell">
        <p>Pengirim,</p>
        <div class="line"></div>
        <p class="tgl">Tgl.</p>
      </td>
      <td class="signature-cell">
        <p>Penerima,</p>
        <div class="line"></div>
        <p class="tgl">Tgl.</p>
      </td>
    </tr>
  </table>

</body>

</html>