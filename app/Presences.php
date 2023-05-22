<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presences extends Model
{
    protected $fillable = [
        'status', 'description', 'student_user_id', 'coursemodule_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class, 'coursemodule_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('fullname', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
