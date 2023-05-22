<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    protected $fillable = ['id','user_id','access_id', 'created_by','updated_by'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Access()
    {
        return $this->belongsTo(Access::class);
    }
}
