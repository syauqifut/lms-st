<?php

namespace App\Imports;

use App\Interval;
use App\User;
use App\Level;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;

class IntervalImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row['createdBy'] = Auth::id();
        $row['minmark'] = $row['minmark'];
        $row['maxmark'] = $row['maxmark'];
        $row['minavg'] = $row['minavg'];
        $row['maxavg'] = $row['maxavg'];
        $level = Level::where('title', $row['level'])->first();
        if(!($level)){
           // dd("disini");
            return null;
        }
        $row['level_id'] = $level->id;
        $row['alphabet'] = $row['alphabet'];
        $row['status'] = $row['status'];
        $intervals = Interval::where([['minmark', $row['minmark']], ['maxmark', $row['maxmark']], ['minavg', $row['minavg']], ['maxavg', $row['maxavg']], ['alphabet', $row['alphabet']], ['status', $row['status']]])->first();
       // print_r($groups);
        if(!$intervals){
           // dd("SSSSS");
            return new Interval($row);
        }else {    
           // dd("OEFKFOKE");
            return null;
        }
    }

    
}
