<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title', 'description', 'photo', 'join_code', 'is_active', 'subject_id', 'category_id', 'level_id', 'created_by', 'updated_by', 'teacher_id', 'group_id'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function course_modules()
    {
        return $this->hasMany(CourseModule::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function course_user()
    {
        return $this->hasMany(CourseUser::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function photoUrl(array $attributes)
    {
        $path = 'images/courses/' . $this->photo;
        if ($path) {
            return URL::to(App::make(Server::class)->fromPath($path, $attributes));
        }
    }

    public static function setPhoto(&$image)
    {
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        if (!file_exists(public_path('images/courses'))) {
            mkdir(public_path('images/courses'), 0777, true);
        }
        $destinationPath = public_path('/images/courses');
        $image->move($destinationPath, $filename);

        return $filename;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getTotalReviewsAttribute()
    {
        $total = $this->reviews->where('is_active', 1)->count();
        return $total;
    }

    public function getAverageReviewsAttribute()
    {
        $avg = $this->reviews->where('is_active', 1)->avg('star');
        return $avg;
    }

    public function scopeGuru($query)
    {
        // $query->whereHas('cou')
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
            $query->orWhereHas('teacher', function($teacher) use($search){
                $teacher->where('fullname','like','%'.$search.'%');
            });
            $query->orWhereHas('subject', function($subject) use($search){
                $subject->where('name','like','%'.$search.'%');
            });
            $query->orWhereHas('category', function($category) use($search){
                $category->where('title','like','%'.$search.'%');
            });
            $query->orWhereHas('level', function($level) use($search){
                $level->where('title','like','%'.$search.'%');
            });
            $query->orWhereHas('group', function($group) use($search){
                $group->where('classes','like','%'.$search.'%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function scopeIsActive($query)
    {
        $query->where('is_active', 1);
    }
}
