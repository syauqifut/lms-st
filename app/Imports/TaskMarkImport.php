<?php

namespace App\Imports;

use App\TaskFileUser;
use Carbon\Traits\Date;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class TaskMarkImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $rows)
    {
            $taskmark=TaskFileUser::find($rows['id']);
            $taskmark->status=1;
            $taskmark->mark=$rows['nilai'];
            $taskmark->save();
    }

  
}
