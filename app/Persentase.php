<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persentase extends Model
{
    protected $fillable = [
        'persen', 'task_type', 'category_id', 'created_by', 'updated_by'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('persen', 'like', '%' . $search . '%');
            $query->orwhere('task_type', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
