<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interval extends Model
{
    protected $fillable = [
        'minmark', 'maxmark', 'minavg', 'maxavg', 'alphabet', 'status', 'created_by', 'updated_by', 'level_id'
    ];
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('minmark', 'like', '%' . $search . '%');
            $query->orwhere('maxmark', 'like', '%' . $search . '%');
            $query->orwhere('minavg', 'like', '%' . $search . '%');
            $query->orwhere('maxavg', 'like', '%' . $search . '%');
            $query->orwhere('alphabet', 'like', '%' . $search . '%');
            $query->orwhere('status', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
}
}
