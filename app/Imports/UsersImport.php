<?php

namespace App\Imports;

use App\User;
use App\UserProfile;
use App\Access;
use App\UserAccess;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use PHPExcel_Shared_Date;

class UsersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $accessid = Access::where('name', '=', 'stai')->pluck('id')->first();

        $result = ['suc' => 0, 'err' => 0];
        foreach ($rows as $row) 
        {

            if(User::where('username', $row['username'])->first()){
                return Redirect::back()->with('error', 'sudah ada username yang digunakan');
            }

            $ttl=$row['tanggal_lahir'];
            $row['birthdate'] = $this->convertDate($ttl);

            $arData = array_merge($row->toArray(), [
                'password' => 'asdf12345',
                'fullname' => $row['nama_depan']. ' ' . $row['nama_belakang'],
                'is_active' => 1,
                'account_id' => 1,
                'createdBy' => Auth::id(),
                'owner' => 0,
                'first_name' =>$row['nama_depan'],
                'last_name' =>$row['nama_belakang'],
                'adress' =>$row['alamat'],
                'city' =>$row['kota'],
                'country' =>$row['negara'],
                'mobilephone' =>$row['no_hp'],
                'birthplace' =>$row['tempat_lahir'],
               // 'birthdate' => $ttl,
               
            ]);

            $user = User::create($arData);

            $row['user_id']         = $user->id;
            $row['created_by']      = Auth::id();
            $row['tgl_masuk']       = $this->convertDate($row['tgl_masuk'], false);
            $row['tgl_byg']         = $this->convertDate($row['tgl_byg'], false);
            $row['tgl_istirahat']   = $this->convertDate($row['tgl_istirahat'], false);
            $row['tgl_pp']          = $this->convertDate($row['tgl_pp'], false);
            // $row['tgl_masuk']       =  $row['tgl_masuk'];
            // $row['tgl_byg']         =  $row['tgl_byg'];
            // $row['tgl_istirahat']   =  $row['tgl_istirahat']; 
            // $row['tgl_pp']          =  $row['tgl_pp']; 
            
            UserProfile::create($row->toArray());

            // User Access
            $userid = $user->id; 

            $useraccess = new UserAccess;
            $useraccess->access_id = $accessid;
            $useraccess->user_id = $userid;
            $useraccess->created_by = Auth::id();
            $useraccess->save();
        }
        return $result;
    }

    public function convertDate($date, $time = true)
    {

       
        //exit();
        if (is_numeric($date)){
           // echo "num"; exit();
          // echo $date."_1";
            $UNIX_DATE = ($date - 25569) * 86400;
            if($time)
                $joss = (gmdate("Y-m-d H:i:s", $UNIX_DATE));
            else
                $joss = (gmdate("Y-m-d", $UNIX_DATE));
            //dd($joss);
            return $joss;
        }else{
          //  echo $date."_2";
           // echo "str"; exit();
            //dd($date);
            $date   =   explode("-",$date);
           // print_r($date);
            $joss   =    $date[2]."-".$date[1]."-".$date[0]. ($time?" 00:00:00": "");
            return $joss;
           // dd($joss);
        }
    }

}
