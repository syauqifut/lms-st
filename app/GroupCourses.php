<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupCourses extends Model
{
    protected $fillable = [
         'id','created_by', 'updated_by', 'course_id', 'group_id'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('id', 'like', '%'.$search.'%')
            ->orWhereHas('course', function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%');
            })
            ->orWhereHas('group', function ($query) use ($search) {
                $query->where('classes', 'like', '%'.$search.'%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
