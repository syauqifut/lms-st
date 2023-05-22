<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rapor extends Model
{
    protected $table = 'rapor';

    protected $fillable = [
        'nim', 'nama', 'tugas', 'uts', 'uas', 'perform', 'sakit', 'izin', 'alpha', 'nilai', 'huruf', 'kelas', 'walikelas', 'subject', 'gurupengajar', 'created_at', 'updated_at', 'created_by', 'updated_by', 'course_id'
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('nama', 'like', '%' . $search . '%');
            $query->orWhere('subject', 'like', '%' . $search . '%');
            $query->orWhere('kelas', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
