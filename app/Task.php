<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    protected $fillable = [
        'course_module_id', 'teacher_id', 'group_id', 'date', 'name', 'task_type', 'due_date', 'is_file', 'file','soal', 'auto_mark', 'random_order', 'link', 'created_by', 'updated_by',
    ];

    public function user_upload_tasks()
    {
        return $this->hasMany(TaskFileUser::class, 'task_id');
    }

    public function user_upload($task_id)
    {
        return $this->user_upload_tasks()
            ->where('user_id', Auth::id())
            ->where('task_id', $task_id)
            ->where('status', '!=', null)
            ->first();
    }

    public function course_module()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public static function setFile(&$file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        if (!file_exists(public_path('files/tasks'))) {
            mkdir(public_path('files/tasks'), 0777, true);
        }
        $destinationPath = public_path('/files/tasks');
        $file->move($destinationPath, $filename);

        return $filename;
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
