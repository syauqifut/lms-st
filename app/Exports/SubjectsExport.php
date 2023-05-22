<?php

namespace App\Exports;

use App\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SubjectsExport implements FromCollection, WithHeadings, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Subject::where('is_active', 1)->get()
            ->transform(function($subject){
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                ];
            });
    }

    public function headings(): array
    {
        return ["id", "name"];
    }
    
    public function title(): string
    {
        return "subjects";
    }
}
