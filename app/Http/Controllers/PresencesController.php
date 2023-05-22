<?php


/**
type Unit

1 =     text
2 =     file ([pdf,image])
3 =     web_url
4 =     vidcon_link




*/






namespace App\Http\Controllers;

use App\CourseModule;
use App\CourseUser;
use App\Presences;
use App\Course;
use App\PresencesLog;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use Illuminate\Support\Facades\Request;


class PresencesController extends Controller
{


    public static $typeUserForAdmin = array("1", "3", "4");

    public function show($courseModuleId)
    {




        $data['dataCourseModule']     = CourseModule::select(
            'course_modules.*',
            CourseModule::raw('DATE_FORMAT(schedule_start_at, "%d-%m-%Y %H:%i") as schedule_start_at_indonesia'),
            CourseModule::raw('DATE_FORMAT(schedule_end_at, "%d-%m-%Y %H:%i") as schedule_end_at_indonesia'),
            CourseModule::raw('DATE_FORMAT(actual_start_at, "%d-%m-%Y %H:%i") as actual_start_at_indonesia'),
            CourseModule::raw('DATE_FORMAT(actual_end_at, "%d-%m-%Y %H:%i") as actual_end_at_indonesia')
        )
        ->where('id', $courseModuleId)
        ->first();

        /// from helpers
        if(checkAccessCourse($data['dataCourseModule']->course_id, Auth::id()) ===  false){
            return Redirect::route('courses.index');
        };

        $data['filters']        = Request::all('search', 'trashed');

        $data['dataPresences'] = CourseUser::filter(Request::only('search'))
            ->select(
                        'users.id',
                        'users.usertype_id',
                        'users.fullname',
                        'presences.status',
                        'presences.description',
                        CourseModule::raw('DATE_FORMAT(date_complete, "%d-%m-%Y %H:%i") as date_complete_indonesia')
                    )
            ->join('users',   'users.id',      '=',    'course_users.user_id')
            ->orderBy('users.usertype_id' ,'desc')
            ->orderBy('users.fullname' ,'asc')
            ->leftJoin('presences', function($join) use ($courseModuleId)
            {
                $join->on('presences.student_user_id', '=', 'users.id')
                ->where('presences.coursemodule_id', $courseModuleId);
            })
            ->where('course_users.course_id', $data['dataCourseModule']->course_id)
            ->where('course_users.is_active', 1)
            ->where('users.deleted_at',NULL)
            ->orderBy('users.usertype_id','desc')
            ->orderBy('users.fullname','desc')
            ->paginate()
            ->transform(function ($module) {
                return [
                    'student_user_id'                    => $module->id,
                    'usertype_id'                    => $module->usertype_id,
                    'fullname'              => $module->fullname,
                    'status'                => type_presences($module->status),
                    'description'                => $module->description,
                    'date_complete_indonesia'    => $module->date_complete_indonesia
                ];
            });

        if( in_array(Auth::user()->usertype_id, static::$typeUserForAdmin)) {

             if($data['dataCourseModule']->actual_start_at ){
                return Inertia::render('Presences/IndexTrainer', $data);
            }
            else{
                return Inertia::render('Presences/FormStart', $data);
            }

        }
        else{

            $data['presencesMe']     = Presences::select(
                '*',
                Presences::raw('DATE_FORMAT(date_complete, "%d-%m-%Y %H:%i") as date_complete_indonesia')
            )
            ->where('coursemodule_id', $courseModuleId)
            ->where('student_user_id', Auth::id())
            ->first();

            if( $data['presencesMe']){
                return Inertia::render('Presences/IndexLearner', $data);
            }
            else{
                if($data['dataCourseModule']->actual_end_at){
                    return Inertia::render('Presences/IndexLearner', $data);
                }
                else{
                    return Inertia::render('Presences/FormPresences', $data);
                }
            }

        }
    }


    public function start(\Illuminate\Http\Request $request)
    {
        CourseModule::where('id', $request->id)
        ->update([
            'actual_start_at'           => date("Y-m-d H:i:s")
        ]);


        /**
        $dataCourseModule       = CourseModule::where('id', $request->id)
        ->first();

        //dd( $dataCourseModule);

        $isTeacher              = Course::where('teacher_id', Auth::id())
        ->where('id', $dataCourseModule->course_id)
        ->first();

        dd();

        $isTeacherAssistent     = CourseUser::where('user_id', Auth::id())
        ->where('id', $dataCourseModule->course_id)
        ->first();

        if($isTeacher ||  $isTeacherAssistent){
            $present = new Presences;

            $present->student_user_id   = Auth::id();
            $present->created_by        = Auth::id();
            $present->updated_by        = Auth::id();
            $present->status            = "P";

            $present->coursemodule_id   = $request->id;
            $present->date_complete     = date('Y-m-d H:i:s');

            $present->save();
        }
        */

        $this->present($request);


        return Redirect::back();

    }

    public function stop($courseModuleId)
    {
        $data['dataCourseModule']     = CourseModule::where('id', $courseModuleId)
        ->first();

        $userNotPresents = CourseUser::select(
                'users.id',
                'users.fullname'
            )
            ->join('users',   'users.id',      '=',    'course_users.user_id')
            ->where('users.deleted_at',NULL)
            ->where('course_users.is_active',1)
            ->whereNotIn('users.id', function($q) use ($courseModuleId){
                $q->select('student_user_id')->from('presences')->where('coursemodule_id',$courseModuleId);
            })
            ->where('course_users.course_id', $data['dataCourseModule']->course_id)
            ->get();

            //dd($userNotPresents);

            foreach($userNotPresents as $userNotPresent){
                $present = new Presences;

                $present->student_user_id   = $userNotPresent->id;
                $present->coursemodule_id   = $courseModuleId;

                $present->created_by        = Auth::id();
                $present->updated_by        = Auth::id();
                $present->status            = "A";
                $present->description       = "By System";
                $present->date_complete     = date('Y-m-d H:i:s');

                if( $present->save()){
                    $this->present_log( $present->id, $userNotPresent->id, $courseModuleId,  date('Y-m-d H:i:s'), "A" , "By System");
                }
            }

        CourseModule::where('id', $courseModuleId)
        ->update([
            'actual_end_at'           => date("Y-m-d H:i:s")
        ]);

        return Redirect::back();



    }

    public function present(\Illuminate\Http\Request $request)
    {
        $present = new Presences;

        $present->student_user_id   = Auth::id();
        $present->created_by        = Auth::id();
        $present->updated_by        = Auth::id();
        $present->status            = "P";

        $present->coursemodule_id   = $request->id;
        $present->date_complete     = date('Y-m-d H:i:s');

        if ($present->save()) {

            $this->present_log( $present->id, Auth::id(), $request->id,  date('Y-m-d H:i:s'), "P", "" );

            return Redirect::back();
        }


    }



    public function update_status(\Illuminate\Http\Request $request)
    {
        $dataPresences = Presences::where('student_user_id', $request->student_user_id)
        ->where('coursemodule_id', $request->coursemodule_id)
        ->first();


        if($dataPresences){

            $this->present_log( $dataPresences->id, $request->student_user_id, $request->coursemodule_id, $dataPresences->date_complete, $request->status , $dataPresences->description  );

            $updateQuery = Presences::where('student_user_id', $request->student_user_id)
                ->where('coursemodule_id', $request->coursemodule_id)
                ->update([
                    'status'                => $request->status,
                    'description'           => $request->description,
                    'updated_by'            => Auth::id()
                ])
               ;
            //dd($request->student_user_id);



            return Redirect::back();


        }
        else{
            $present = new Presences;

            $present->student_user_id   =   $request->student_user_id;
            $present->created_by        =   Auth::id();
            $present->updated_by        =   Auth::id();
            $present->status            =   $request->status;
            $present->description       =   $request->description;

            $present->coursemodule_id   =   $request->coursemodule_id;
            $present->date_complete     =   date('Y-m-d H:i:s');

            if ($present->save()) {

                $this->present_log( $present->id, Auth::id(), $request->coursemodule_id,  date('Y-m-d H:i:s'),$request->status, $request->description );

                return Redirect::back();
            }
        }



    }


    public function present_log( $presences_id, $student_user_id, $coursemodule_id, $date_complete, $status, $description  )
    {
        $presentLog = new PresencesLog;

        $presentLog->created_by        = Auth::id();
        $presentLog->updated_by        = Auth::id();

        $presentLog->presences_id            = $presences_id;
        $presentLog->student_user_id         = $student_user_id;
        $presentLog->coursemodule_id         = $coursemodule_id;
        $presentLog->date_complete           = $date_complete;
        $presentLog->status                  = $status;
        $presentLog->description             = $description;

        $presentLog->save();
    }
}
