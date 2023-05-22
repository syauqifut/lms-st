<?php

namespace App\Imports;

use App\Course;
use App\User;
use App\Group;
use App\Subject;
use App\Level;
use App\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CoursesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // $validator = Validator::make($row, [
        //     'classes' => ['required'],
        //     'year' => ['required'],
        //     'academicterms' => ['required'],
        //     'mainteacher' => ['required'],
            
        // ]);
        
        $row['created_by'] = Auth::id();
        $row['is_active'] = 1;
        $code = getKode(15);
        while (Course::where('join_code', $code)->count()>0) {
            $code = getKode(15);
        }
        $row['join_code'] = $code;
        $subject = Subject::where('subject_code', $row['kode_subjek'])->first();
        $category = Category::where('title', $row['kategori'])->first();
        $level = Level::where('title', $row['level'])->first();
        $teacher = User::where('username', $row['pendidik'])->first();
        // dd($subject, $category, $level, $teacher);
        // dd($code);
        if(!($subject && $category && $level && $teacher))
            return null;

        $row['subject_id'] = $subject->id;
        $row['category_id'] = $category->id;
        $row['level_id'] = $level->id;
        $row['teacher_id'] = $teacher->id;
        $row['group_id'] = $row['group_id'];
        $row['title'] = $row['judul'];
        $row['description'] = $row['deskripsi'];
        return new Course($row);
    }
}
