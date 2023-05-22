<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserProfile extends Model
{
    //
    protected $fillable = ['user_id', 'no_induk','nisn', 'kps','nik','kip','kks','pkh','no_kk','prov','kec','nm_ayah','ktp_ayah','pend_ayah','krj_ayah','nm_ibu','ktp_ibu', 'krj_ibu', 'pend_ibu','hsl_wali','alamat_domisili','tgl_masuk','ket','tgl_byg','tgl_istirahat','tgl_pp','creted_by'];
    /** 
    protected $dates = [
        'tgl_masuk', 'tgl_byg', 'tgl_istirahat', 'tgl_pp'
    ];

    public function getTglMasukAttribute($tgl)
    {
        return Carbon::parse($tgl)->format('Y-m-d');
    }
    public function getTglBygAttribute($tgl)
    {
        return Carbon::parse($tgl)->format('Y-m-d');
    }
    public function getTglIstirahatAttribute($tgl)
    {
        return Carbon::parse($tgl)->format('Y-m-d');
    }
    public function getTglPpAttribute($tgl)
    {
        return Carbon::parse($tgl)->format('Y-m-d');
    }
    */
}
