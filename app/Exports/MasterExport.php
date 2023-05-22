<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\GroupsExport;
use App\Exports\LevelsExport;
use App\Exports\CategoriesExport;
use App\Exports\SubjectsExport;
use App\Exports\CoursesExport;

class MasterExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
    $sheets = [];
        $sheets[] = new CoursesExport();
        $sheets[] = new GroupsExport();
        $sheets[] = new LevelsExport();
        $sheets[] = new CategoriesExport();
        $sheets[] = new SubjectsExport();

        return $sheets;
    }
}
