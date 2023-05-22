<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menuprivilleges extends Model
{
    protected $fillable = ['id','usertype_id','menu_id', 'created_by','updated_by'];

    public function usertype()
    {
        return $this->belongsTo(Usertype::class);

    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });

    }

    
}
