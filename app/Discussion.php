<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = [
        "title", "discuss", "parent_discuss_id", "file_attachment", "user_id", "course_module_id", "is_active", "created_by", "updated_by",
    ];

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function discussionComment()
    // {

    // }

    public static function setFile(&$file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        if (!file_exists(public_path('files/discussions'))) {
            mkdir(public_path('files/discussions'), 0777, true);
        }
        $destinationPath = public_path('/files/discussions');
        $file->move($destinationPath, $filename);

        return $filename;
    }

    public static function removeFile($file_name)
    {
        $path = public_path('files/discussions/' . $file_name);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
