<?php

namespace App\Imports;

use App\CourseModule;
use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Course;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Auth;

class CourseModuleImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row['created_by'] = Auth::id();
        $row['is_active'] = 1;
        $row['title'] = $row['judul'];
        $row['date'] = $row['tanggal'];
        $row['schedule_start_at'] = $row['schedule_start_at'];
        $row['schedule_end_at'] = $row['schedule_end_at'];
        $course = Course::where('id', $row['course_id'])->first();
        if(!($course))
            return null;

        $row['course_id'] = $course->id;
        // if(CourseModule::where('course_id', $course->id)->where('user_id', $user->id)->first())
        //     return null;
        return new CourseModule($row);
    }
}
