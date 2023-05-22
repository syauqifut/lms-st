<?php

namespace App;
use Auth;

use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    protected $fillable = [
        'title', 'date', 'schedule_start_at', 'schedule_end_at', 'actual_start_at', 'actual_end_at', 'is_active', 'course_id', 'created_by', 'updated_by',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'course_module_id');
    }

    public function unit($courseModuleId)
    {
        /**
         *
            1 administrator
            2 Murid
            3 Guru
            4 Admin
            5 Orang Tua
         */

        // if(Auth::user()->usertype_id == 1 || Auth::user()->usertype_id == 3){
        //     $datas = CourseUnit::
        //     select('course_units.*', 'course_unit_completes.date_complete','course_unit_completes.date_start',CourseUnitComplete::raw('DATE_FORMAT(course_unit_completes.date_complete, "%d-%m-%Y %H:%i") as date_complete_indonesia'))
        //     ->where('course_module_id', $courseModuleId)
        //     ->leftJoin('course_unit_completes', function($join) {
        //         $join->on('course_unit_completes.course_unit_id', '=', 'course_units.id')
        //         ->where('course_unit_completes.user_id',Auth::user()->id);
        //     })
        //     ->orderBy('order_course_units', 'asc')
        //     ->get();
        // }
        // else{

        $datas = CourseUnit::
            select('course_units.*', 'course_unit_completes.date_complete','course_unit_completes.date_start',CourseUnitComplete::raw('DATE_FORMAT(course_unit_completes.date_complete, "%d-%m-%Y %H:%i") as date_complete_indonesia'))
            ->where('course_module_id', $courseModuleId)
            ->leftJoin('course_unit_completes', function($join) {
                $join->on('course_unit_completes.course_unit_id', '=', 'course_units.id')
                ->where('course_unit_completes.user_id',Auth::user()->id);
            })
            ->orderBy('order_course_units', 'asc')
            ->get();


        $dataArray = array();

        $i=1;

        foreach($datas as $data){

            $column['id']                   =   $data->id;
            $column['link_to_unit_id']      =   $data->link_to_unit_id;
            $column['is_active']            =   $data->is_active;
            $column['created_by']           =   $data->created_by;
            $column['updated_by']           =   $data->updated_by;
            $column['created_at']           =   $data->created_at;
            $column['updated_at']           =   $data->updated_at;
            $column['name']                 =   $data->name;
            $column['type_course_units']    =   $data->type_course_units;
            $column['order_course_units']    =   $data->order_course_units;
            $column['content']              =   $data->content;
            $column['date_complete']        =   $data->date_complete;
            $column['date_complete_indonesia']        =   $data->date_complete_indonesia;
            $column['date_start']           =   $data->date_start;

            if($i==1){
                $column['status']               =   "open";
            }else{
                $checkPrevComplete = CourseUnitComplete::where('course_unit_id','<', $data->id)
                ->where('user_id',Auth::user()->id)
                ->whereNotNull('date_complete')
                ->first();

                if($checkPrevComplete){
                    $column['status']               =   "open";
                }
                else{
                    $column['status']               =   "close";
                }
            }


            array_push($dataArray, $column);

            $i++;
        }



        return $dataArray;
    }

    public function unitactive($courseModuleId)
    {
        /**
         *
            1 administrator
            2 Murid
            3 Guru
            4 Admin
            5 Orang Tua
         */

        // if(Auth::user()->usertype_id == 1 || Auth::user()->usertype_id == 3){
        //     $datas = CourseUnit::
        //     select('course_units.*', 'course_unit_completes.date_complete','course_unit_completes.date_start',CourseUnitComplete::raw('DATE_FORMAT(course_unit_completes.date_complete, "%d-%m-%Y %H:%i") as date_complete_indonesia'))
        //     ->where('course_module_id', $courseModuleId)
        //     ->leftJoin('course_unit_completes', function($join) {
        //         $join->on('course_unit_completes.course_unit_id', '=', 'course_units.id')
        //         ->where('course_unit_completes.user_id',Auth::user()->id);
        //     })
        //     ->orderBy('order_course_units', 'asc')
        //     ->get();
        // }
        // else{

        $datas = CourseUnit::
            select('course_units.*', 'course_unit_completes.date_complete','course_unit_completes.date_start',CourseUnitComplete::raw('DATE_FORMAT(course_unit_completes.date_complete, "%d-%m-%Y %H:%i") as date_complete_indonesia'))
            ->where('course_module_id', $courseModuleId)
            ->where('is_active', '1')
            ->leftJoin('course_unit_completes', function($join) {
                $join->on('course_unit_completes.course_unit_id', '=', 'course_units.id')
                ->where('course_unit_completes.user_id',Auth::user()->id);
            })
            ->orderBy('order_course_units', 'asc')
            ->get();


        $dataArray = array();

        $i=1;

        foreach($datas as $data){

            $column['id']                   =   $data->id;
            $column['link_to_unit_id']      =   $data->link_to_unit_id;
            $column['is_active']            =   $data->is_active;
            $column['created_by']           =   $data->created_by;
            $column['updated_by']           =   $data->updated_by;
            $column['created_at']           =   $data->created_at;
            $column['updated_at']           =   $data->updated_at;
            $column['name']                 =   $data->name;
            $column['type_course_units']    =   $data->type_course_units;
            $column['order_course_units']    =   $data->order_course_units;
            $column['content']              =   $data->content;
            $column['date_complete']        =   $data->date_complete;
            $column['date_complete_indonesia']        =   $data->date_complete_indonesia;
            $column['date_start']           =   $data->date_start;

            if($i==1){
                $column['status']               =   "open";
            }else{
                $checkPrevComplete = CourseUnitComplete::where('course_unit_id','<', $data->id)
                ->where('user_id',Auth::user()->id)
                ->whereNotNull('date_complete')
                ->first();

                if($checkPrevComplete){
                    $column['status']               =   "open";
                }
                else{
                    $column['status']               =   "close";
                }
            }


            array_push($dataArray, $column);

            $i++;
        }



        return $dataArray;
    }


    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
