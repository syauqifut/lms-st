<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['classes', 'year', 'code', 'academicterms', 'mainteacher', 'level_id', 'huruf', 'kel_kelas', 'is_active', 'createdBy','category_id'];

    public function mainteacherr()
    {
        return $this->belongsTo(User::class, 'mainteacher');
    }

    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class, 'group_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('classes', 'like', '%'.$search.'%');
            $query->orWhere('year', 'like', '%'.$search.'%');
            $query->orWhere('academicterms', 'like','%'.$search.'%');
            $query->orWhereHas('mainteacherr', function($teacher) use($search){
                $teacher->where('fullname','like','%'.$search.'%');
            });
            $query->orWhere('classes', 'like', '%'.$search.'%');
        // })->when($filters['trashed'] ?? null, function ($query, $trashed) {
        //     if ($trashed === 'with') {
        //         $query->withTrashed();
        //     } elseif ($trashed === 'only') {
        //         $query->onlyTrashed();
        //     }
        })->when($filters['tahun'] ?? null, function ($query, $tahun) {
            $query->orWhere('year', 'like', '%'.$tahun.'%');
        })->when($filters['academicterms'] ?? null, function ($query, $academicterms) {
            $query->orWhere('academicterms', 'like','%'.$academicterms.'%');
        })->when($filters['mainteacher'] ?? null, function ($query, $mainteacher) {
            $query->orWhere('mainteacher', 'like', '%'.$mainteacher.'%');
        })->when($filters['classes'] ?? null, function ($query, $classes) {
            $query->orWhere('classes', 'like', '%'.$classes.'%');

        });
    }

    public function scopeIsactive($query)
    {
        $query->where('is_active', 1);
    }

    
}
