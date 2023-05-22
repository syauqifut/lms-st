<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TartibNegSiswa extends Model
{
    // use SoftDeletes;
    protected $table = 'Tartibnegsiswa';

    protected $fillable = [
        'date', 'group_id', 'user_id', 'tartib_id', 'poin', 'created_by', 'updated_by', 'userlapor_id','catatan',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function activeGroup()
    {
        return $this->group()->where('is_active', 1);
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'userlapor_id');
    }

    public function tartib()
    {
        return $this->belongsTo(Tartib::class, 'tartib_id');
    }

    public function scopeFilter($query, array $filters)
    {
        // $query->when($filters['search'] ?? null, function ($query, $search) {
        //     $query->where('name', 'like', '%'.$search.'%');
        // })->when($filters['trashed'] ?? null, function ($query, $trashed) {
        //     if ($trashed === 'with') {
        //         $query->withTrashed();
        //     } elseif ($trashed === 'only') {
        //         $query->onlyTrashed();
        //     }
        // });
    }
}
