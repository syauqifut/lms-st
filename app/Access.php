<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    protected $fillable = ['name', 'logo', 'route', 'description', 'isactive', 'createdBy'];
    
    public function UserAccess()
    {
        return $this->hasMany(UserAccess::class);
    }
}
