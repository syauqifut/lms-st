<?php

namespace App\Exports;

use App\User;
use App\UserAccess;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserallExport implements FromCollection, WithHeadings, WithTitle
{
    protected $usertypeid;
    public function __construct($usertypeid)
    {
        $this->usertypeid = $usertypeid;
    }
    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection()
    {
        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        return User::where('usertype_id', $this->usertypeid)->whereIn('id', $access)->get()
            ->transform(function($users){
                return [
                    'id' => $users->id,
                    'first_name' => $users->first_name,
                    'last_name' => $users->last_name,
                    'email' => $users->email,
                    'username' => $users->username,
                    'fullname' => $users->fullname,
                    'adress' => $users->adress,
                    'city' => $users->city,
                    'country' => $users->country,
                    'mobilephone' => $users->mobilephone,
                    'birthplace' => $users->birthplace,
                    'birthdate' => $users->birthdate,
                    'usertype_id' => $users->usertype_id,
                    'parent_id' => $users->parent_id,
                    'is_active' => $users->is_active,
                    'gender' => $users->gender,

                ];
            });
    }

    public function headings(): array
    {
        return [
            'id', 
            'first_name',
            'last_name',
            'email',
            'username',
            'fullname',
            'adress',
            'city',
            'country',
            'mobilephone',
            'birthplace',
            'birthdate',
            'usertype_id',
            'parent_id',
            'is_active',
            'gender'
        ];
    }
    
    public function title(): string
    {
        return "Users";
    }
}
