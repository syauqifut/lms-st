<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CourseUnitComplete  extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "id", "course_unit_id","user_id", "date_start", "date_complete",
    ];

    
}
