<?php

namespace App\Imports;

use App\CourseUser;
use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Course;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Auth;

class CourseUserImport implements ToModel, WithHeadingRow
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
        $course = Course::where('join_code', $row['kode_course'])->first();
        $user = User::where('username', $row['username'])->first();
        if(!($course && $user))
            return null;

        $row['course_id'] = $course->id;
        $row['user_id'] = $user->id;
        if(Courseuser::where('course_id', $course->id)->where('user_id', $user->id)->first())
            return null;
        return new CourseUser($row);
    }
}
