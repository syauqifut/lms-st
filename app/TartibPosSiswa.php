<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TartibPosSiswa extends Model
{
    protected $table = 'tartibpossiswa';

    protected $fillable = [
        'date', 'group_id', 'user_id', 'tartib_id', 'poin', 'userlapor_id', 'created_by', 'updated_by','catatan',
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
