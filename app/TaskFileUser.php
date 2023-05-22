<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskFileUser extends Model
{
    protected $table = 'task_file_users';

    protected $fillable = [
        'task_id', 'user_id', 'description', 'file', 'status', 'mark', 'tanggal_kumpul'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function presence()
    {
        return $this->hasMany(Presences::class, 'student_user_id', 'user_id');
    }

    public static function setFile(&$file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        if (!file_exists(public_path('files/student_tasks'))) {
            mkdir(public_path('files/student_tasks'), 0777, true);
        }
        $destinationPath = public_path('/files/student_tasks');
        $file->move($destinationPath, $filename);

        return $filename;
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('users.fullname', 'like', '%' . $search . '%');
            $query->orwhere('subjects.name', 'like', '%' . $search . '%');
            $query->orwhere('groups.classes', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
