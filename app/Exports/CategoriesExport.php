<?php

namespace App\Exports;

use App\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CategoriesExport implements FromCollection, WithHeadings, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Category::where('is_active', 1)->get()
            ->transform(function($category){
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                    'parent' => $category->categoryParent? $category->categoryParent->title : '',
                ];
            });
    }

    public function headings(): array
    {
        return ["id", "title", "parent"];
    }
    
    public function title(): string
    {
        return "Category";
    }
}
