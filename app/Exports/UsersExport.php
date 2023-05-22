<?php

namespace App\Exports;

use App\User;
use App\Group;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        return Group::where('is_active', 1)->get()
            ->transform(function($group){
                return [
                    'id' => $group->id,
                    'classes' => $group->classes,
                    'year' => $group->year,
                    'academicterms' => $group->academicterms,
                    'mainteacherr' => $group->mainteacherr ? $group->mainteacherr->username : null,
                    // 'code' => $group->code,
                ];
            });
    }

    public function headings(): array
    {
        return ["id", "classes", "year", 'academicterms', 'mainteacherr'];
    }
}
