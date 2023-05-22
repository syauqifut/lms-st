<?php

namespace App\Imports;

use App\Subject;
use Carbon\Traits\Date;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        //dd($row);

        return new Subject([
            'name'          => $row['nama'],
            'description'   => $row['deskripsi'],
            'subject_code'  => $row['subject_code'],
            'sks'           => $row['sks'],
            'subject_type'  => $row['subject_type'],
            'is_active'     => 1,
            'created_by'    => Auth::id(),
            'updated_by'    => Auth::id(),
        ]);
    }
}
