<?php
namespace App\Imports;

use App\Models\Perusahaan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PerusahaanImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $namaPerusahaan = $row['nama_perusahaan'] ?? $row['nama'] ?? null;
        if (empty($namaPerusahaan)) {
            return null;
        }

        // Cari record yang ada terlebih dahulu
        $perusahaan = Perusahaan::where('nama', $namaPerusahaan)->first();

        // Siapkan data dari baris Excel
        $dataFromRow = [
            'alamat'  => $row['alamat_perusahaan'] ?? $row['alamat'] ?? null,
            'telepon' => $row['telepon'] ?? null,
            'email'   => $row['email'] ?? null,
            'npwp'    => $row['npwp'] ?? null,
        ];

        if ($perusahaan) {
            // JIKA SUDAH ADA: Update hanya kolom yang tidak kosong
            $updateData = array_filter($dataFromRow, fn ($value) => !is_null($value) && $value !== '');
            if (!empty($updateData)) {
                $perusahaan->update($updateData);
            }
            return $perusahaan;
        } else {
            // JIKA BARU: Buat record baru, pastikan field wajib memiliki nilai default
            $dataFromRow['nama'] = $namaPerusahaan;
            $dataFromRow['alamat'] = $dataFromRow['alamat'] ?? ''; // << FIX: Memberi nilai default
            return new Perusahaan($dataFromRow);
        }
    }
}