<?php

namespace App\Imports;

use App\User;
use Carbon\Traits\Date;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class UsersParentImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $rows)
    {
            $parent=User::where('username',$rows['username_parent'])->first();
            $rows['parent_id'] = $parent->id;

            $user= User::where('username', $rows['username'])->first();

            $user_parent=User::find($user->id);
            $user_parent->parent_id=$parent->id;
            $user_parent->save();
          // print_r($users);
          
            //    $parent=User::where('username',$rows['username']);
            //    $parent->parent_id=$users['id'];
            //    $parent->save();
           
    }

  
}
