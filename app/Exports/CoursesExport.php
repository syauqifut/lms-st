<?php

namespace App\Exports;

use App\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CoursesExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Course::isActive()
            ->get()
            ->transform(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                ];
            });
    }

    public function headings(): array
    {
        return ["id", "title"];
    }

    public function title(): string
    {
        return "Course";
    }
}
