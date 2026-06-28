<?php
namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Gunakan 'kode_barang' sebagai kunci utama, lewati baris jika kosong
        $kodeBarang = $row['kode_barang'] ?? null;
        if (empty($kodeBarang)) {
            return null;
        }

        // Cari record barang yang ada terlebih dahulu
        $barang = Barang::where('kode', $kodeBarang)->first();

        // Siapkan data dari baris Excel, periksa apakah ada isinya
        $dataFromRow = [
            'nama'     => $row['nama_barang'] ?? null,
            'kategori' => $row['kategori'] ?? null,
            'harga'    => isset($row['harga_jual']) ? (float) str_replace(['Rp', '.', ','], '', $row['harga_jual']) : null,
            'stok'     => isset($row['kuantitas']) ? (int) $row['kuantitas'] : null,
        ];

        if ($barang) {
            // JIKA BARANG SUDAH ADA: Update hanya kolom yang tidak null (ada isinya di Excel)
            $updateData = array_filter($dataFromRow, fn ($value) => !is_null($value));
            if (!empty($updateData)) {
                $barang->update($updateData);
            }
            return $barang;
        } else {
            // JIKA BARANG BARU: Buat record baru, pastikan field wajib memiliki nilai default
            $dataFromRow['kode'] = $kodeBarang;
            $dataFromRow['nama'] = $dataFromRow['nama'] ?? 'Nama Belum Diisi';
            $dataFromRow['harga'] = $dataFromRow['harga'] ?? 0;
            $dataFromRow['stok'] = $dataFromRow['stok'] ?? 0;
            return new Barang($dataFromRow);
        }
    }
}