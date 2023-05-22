<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'icon', 'route', 'description', 'order_menu', 'parentId', 'isactive', 'createdBy'];
    
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parentId');
    }

    public function privilleges()
    {
        return $this->hasMany(Menuprivilleges::class);
        # code...
    }
}
