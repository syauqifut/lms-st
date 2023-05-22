<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseUnit extends Model
{
    protected $fillable = [
        "name", "content", "type_course_units", "is_active", "course_module_id", "created_by", "updated_by","order_course_units","content_extension"
    ];

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public static function setFile(&$file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        if (!file_exists(public_path('files/course_units'))) {
            mkdir(public_path('files/course_units'), 0777, true);
        }
        $destinationPath = public_path('/files/course_units');
        $file->move($destinationPath, $filename);

        return $filename;
    }
}
