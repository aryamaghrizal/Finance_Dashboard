<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Mencari atau membuat supplier baru berdasarkan nama
        return Supplier::updateOrCreate(
            ['nama' => $row['nama_pemasok']],
            [
                'alamat'  => $row['alamat_pemasok'],
                'telepon' => $row['telepon'],
            ]
        );
    }
}