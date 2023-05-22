<?php

namespace App\Imports;

use App\Group;
use App\User;
use App\Level;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;

class GroupsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        //print_r($row);
        $row['createdBy'] = Auth::id();
        $row['is_active'] = 1;
        $code = getKode(15);
        while (Group::where('code', $code)->count()>0) {
            $code = getKode(15);
        }
        $row['code'] = $code;
        $user = User::where('username', $row['pendidik'])->first();
        $level = Level::where('title', $row['level'])->first();
        if(!($user && $level)){
           // dd("disini");
            return null;
        }

        $row['mainteacher'] = $user->id;
        $row['level_id'] = $level->id;

        
        $row['classes'] = $row['kelas'];
        $row['year'] = $row['periode'];
        $row['academicterms'] = $row['semester'];
        $groups = Group::where([['classes', $row['classes']], ['year', $row['year']], ['academicterms', $row['academicterms']]])->first();
       // print_r($groups);
        if(!$groups){
           // dd("SSSSS");
            return new Group($row);
        }else {    
           // dd("OEFKFOKE");
            return null;
        }
    }

    
}
