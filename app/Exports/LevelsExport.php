<?php

namespace App\Exports;

use App\Level;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LevelsExport implements FromCollection, WithHeadings, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Level::get()
            ->transform(function($level){
                return [
                    'id' => $level->id,
                    'title' => $level->title,
                ];
            });
    }

    public function headings(): array
    {
        return ["id", "title"];
    }

    public function title(): string
    {
        return "Level";
    }
}
