<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseUser extends Model
{
    protected $fillable = [
         'id','created_by', 'updated_by', 'user_id', 'course_id', 'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->whereHas('user', function($teacher) use($search){
                $teacher->where('fullname','like','%'.$search.'%');
            });
            $query->orWhereHas('course', function($course) use($search){
                $course->where('title','like','%'.$search.'%');
            });
        });
    }
}
