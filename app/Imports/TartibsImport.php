<?php

namespace App\Imports;

use App\Tartib;
use Carbon\Traits\Date;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TartibsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        //dd($row);

        return new Tartib([
            'kode_pelanggaran' => $row['kode_pelanggaran'],
            'nama_pelanggaran' => $row['nama_pelanggaran'],
            'jenis'  => $row['jenis'],
            'kategori' => $row['kategori'],
            'skor'  => $row['skor'],
            'is_active'     => 1,
            'created_by'    => Auth::id(),
            'updated_by'    => Auth::id(),
        ]);
    }
}
