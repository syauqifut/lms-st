<?php

namespace App\Imports;

use App\User;
use App\Userblock;
use Carbon\Traits\Date;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class UsersBlockImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
      $user = User::where('username', $row['username'])->first();
      // if(!($user ))
      //     return null;
      // dd($user);
      return new Userblock([
        'user_id'       => $user->id,
        'username'      => $user->username,
        'status'        => 1,
        'created_by'    => Auth::id(),
    ]);
    }

  
}
