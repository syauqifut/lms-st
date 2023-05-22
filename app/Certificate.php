<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'user_id', 'file', 'nama', 'keterangan', 'tahun_sertifikat', 'desc_certificate', 'created_at', 'updated_at',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function setFile(&$file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        if (!file_exists(public_path('files/certificates'))) {
            mkdir(public_path('files/certificates'), 0777, true);
        }
        $destinationPath = public_path('/files/certificates');
        $file->move($destinationPath, $filename);

        return $filename;
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('nama', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
