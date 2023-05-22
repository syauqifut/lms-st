<?php

namespace App\Imports;

use App\GroupUser;
use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Auth;

class GroupUsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row['created_by'] = Auth::id();
        $row['is_active'] = 1;
        $validator = Validator::make($row, [
            'group_id' => ['required'],
            'username' => ['required'],
        ]);
        // dd($row);
        $user = User::where('username', $row['username'])->first();
        if(!$user)
            return null;
        $row['user_id'] = $user->id;

        $groupUser= GroupUser::where('group_id', $row['group_id'])->where('user_id', $user->id)->first();
        if($groupUser)
            return null;
        return new GroupUser($row);
    }
}
