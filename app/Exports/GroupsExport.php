<?php

namespace App\Exports;

use App\Group;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GroupsExport implements FromCollection, WithHeadings, WithTitle
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
                    'huruf' => $group->huruf,
                    'year' => $group->year,
                    'academicterms' => $group->academicterms,
                    'mainteacherr' => $group->mainteacherr ? $group->mainteacherr->username : null,
                    'kel_kelas' =>$group->kel_kelas,
                    'level' => $group->level->title,
                    // 'code' => $group->code,
                ];
            });
    }

    public function headings(): array
    {
        return ["id", "kelas", 'huruf', "periode", 'semester', 'pengajar', 'gender', 'level'];
    }
    
    public function title(): string
    {
        return "Groups";
    }
}
