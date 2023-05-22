<?php

namespace App\Http\Controllers;

use App\CourseModule;
use App\Task;
use App\Userblock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DB;
// use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $username = Auth::user()->username;
        if (Userblock::where('username', $username)->where('status', 1)->exists()) {
            return Inertia::render('Auth/LoginError');
        }else
		{    
            $roleAdmin = [1, 4];
            $userRole = Auth::user()->role;
            $userID = Auth::user()->id;
            $data = [];
            // dd($userRole);
			 $datenow=date("Y-m-d");
            //$date5=date("Y-m-d",mktime(0, 0, 0, date("m"), date("d"), date("Y")));

            if (in_array($userRole, $roleAdmin)) {
                $data['courseModules'] = CourseModule::whereIn('course_modules.is_active', [0,1])
                    ->join('courses', 'course_modules.course_id', '=', 'courses.id')
                    ->join('course_users', 'course_users.course_id', '=', 'courses.id')
                    ->join('users','users.id','=','courses.teacher_id')
                    //->join('users as d', 'd.id','=', 'users.usertype_id')
                    // ->where('course_users.user_id',$userID)
                    ->where('users.usertype_id', '=', '3')
                    ->where('course_modules.is_active','=','1')
                    ->where('course_modules.date',$datenow)
                    ->where('course_users.is_active',1)
                    ->groupBy('course_modules.id')
                    ->orderBy('date')
                    ->select('course_modules.id','course_modules.course_id','courses.title as course','course_modules.title','course_modules.schedule_start_at','course_modules.schedule_end_at','users.fullname as teacher')//'c.fullname')// //,'users.fullname as teacher'
                    ->paginate()
                    ->transform(function ($module) {
                        return [
                            'course_module_id' => $module->id,
                            'course_id' => $module->course_id,
                            'route' => url('courses-modules/'.$module->course_id),
                            'guru' => $module->teacher, 
                            'title' => $module->course .'-'.$module->title,
                            'start' => $module->date . ' ' . $module->schedule_start_at,
                            'end' => $module->date . ' ' . $module->schedule_end_at,
                            //'teacher'=>$module->teacher,
                        ];
                    });
                array_push($data, $data['courseModules']);
            }elseif($userRole==5){
                $data['courseModules'] = CourseModule::whereIn('course_modules.is_active', [0,1])
                ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                ->join('course_users', 'course_users.course_id', '=', 'courses.id')
                ->join('users','users.id','=','course_users.user_id')
                ->join('users as p','p.id','=','users.parent_id')
                ->where('p.id',$userID)
                ->where('course_users.is_active',1)
                ->where('course_modules.date',$datenow)
                ->orderBy('date')
                ->select('course_modules.id','course_modules.course_id','courses.title as course','course_modules.title','course_modules.schedule_start_at','course_modules.schedule_end_at','users.fullname as student')
                ->paginate()
                    ->transform(function ($module) {
                        return [
                            'course_module_id' => $module->id,
                            'course_id' => $module->course_id,
                            'route' => url('courses-modules/'.$module->course_id),
                            'title' => $module->course .'-'.$module->title,
                            'start' => $module->date . ' ' . $module->schedule_start_at,
                            'end' => $module->date . ' ' . $module->schedule_end_at,
                            'student' => $module->student,
                        ];
                    });
                    // dd($data);
                    array_push($data, $data['courseModules']);
                
            } else {
                $data['courseModules'] = CourseModule::whereIn('course_modules.is_active', [0,1])
                ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                ->join('course_users', 'course_users.course_id', '=', 'courses.id')
                ->where('course_users.user_id',$userID)
                ->where('course_users.is_active',1)
                ->where('course_modules.date',$datenow)
                ->orderBy('date')
                ->select('course_modules.id','course_modules.course_id','courses.title as course','course_modules.title','course_modules.schedule_start_at','course_modules.schedule_end_at')
                ->paginate()
                    ->transform(function ($module) {
                        return [
                            'course_module_id' => $module->id,
                            'course_id' => $module->course_id,
                            'route' => url('courses-modules/'.$module->course_id),
                            'title' => $module->course .'-'.$module->title,
                            'start' => $module->date . ' ' . $module->schedule_start_at, 
                            'end' => $module->date . ' ' . $module->schedule_end_at,
                        ];
                    });
                // dd($data);
                    array_push($data, $data['courseModules']);
            }



                if($userRole==2){
                    $data['courseTasks'] = CourseModule::whereIn('course_modules.is_active', [0,1])
                    ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                    ->join('course_users', 'course_users.course_id', '=', 'courses.id')
                    ->join('tasks','tasks.course_module_id','=','course_modules.id')
                    ->leftJoin('task_file_users', function($join) use ($userID)
                    {
                        $join->on('task_file_users.task_id', '=', 'tasks.id')
                        ->where('task_file_users.user_id', $userID);
                    })
                    ->where('course_users.user_id',$userID)
                    ->where('course_users.is_active','=','1')
                    ->where('course_users.is_active',1)
                    ->where('tasks.due_date','>=',$datenow)
                    ->orderBy('tasks.due_date')
                    ->select('course_modules.id','course_modules.course_id','courses.title as course','course_modules.title','tasks.name as task','tasks.due_date','task_file_users.mark',DB::raw('(CASE WHEN (task_file_users.status IS NULL) THEN "BELUM" ELSE "SUDAH" END) AS status'))
                    // ->get();
                    ->paginate()
                        ->transform(function ($module) {
                            return [
                                'course_module_id' => $module->id,
                                'task_id' => $module->task_id,
                                'route' => url('courses-modules/'.$module->course_id),
                                'title' => $module->course .'-'.$module->title,
                                'task' => $module->task,
                                'end' => $module->date . ' ' . $module->due_date,
                                'status' => $module->status,
                                'mark' => $module->mark
                            ];
                        });
                        array_push($data, $data['courseTasks']);

                    // dd($data['courseTasks']);

                }elseif($userRole==3){
                    $data['courseTasks'] = CourseModule::whereIn('course_modules.is_active', [0,1])
                    ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                    ->join('course_users', 'course_users.course_id', '=', 'courses.id')
                    ->join('tasks','tasks.course_module_id','=','course_modules.id')
                    ->leftJoin('task_file_users','task_file_users.task_id', '=', 'tasks.id')
                    ->where('course_users.user_id',$userID)
                    ->where('course_users.is_active',1)
                     ->where('course_users.is_active','=','1')
                   // ->where('tasks.due_date','>=',$datenow)
                    ->where(function($query){
                        return $query
                        ->whereNull('task_file_users.mark')
                        ->orWhere('task_file_users.status', '=', '0');
                    })
                    ->selectRaw('COUNT(task_file_users.id) as jml,course_modules.id,tasks.name,tasks.task_type,courses.title,tasks.due_date,tasks.id as task_id')
                    ->groupBy('course_modules.id','tasks.name','tasks.task_type','courses.title','tasks.due_date','tasks.id')
                    ->paginate(1000)
                    ->transform(function ($module) {
                        return [
                            'course_module_id' => $module->id,
                            'title' =>$module->task_type .'-'.$module->title .'-'.$module->name ,
                            'end' => $module->due_date,
                            'total' => $module->jml,
                            'task_id' => $module->task_id
                        ];
                    });
                    array_push($data, $data['courseTasks']);

                    // $data['unmarktask']=count($data['courseTasks']);
                    // dd($data['courseTasks']);
                }elseif($userRole==5){
                    $data['courseTasks'] = CourseModule::whereIn('course_modules.is_active', [0,1])
                    ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                    ->join('course_users', 'course_users.course_id', '=', 'courses.id')
                    ->join('users','users.id','=','course_users.user_id')
                    ->join('tasks','tasks.course_module_id','=','course_modules.id')
                    ->join('users as p','p.id','=','users.parent_id')
                    ->leftJoin('task_file_users', function($join)
                    {
                        $join->on('task_file_users.task_id', '=', 'tasks.id')
                        ->on('task_file_users.user_id','=','users.id');
                    })
                    ->where('p.id',$userID)
                    ->where('tasks.due_date','>=',$datenow)
                     ->where('course_users.is_active','=','1')
                    ->where('course_users.is_active',1)
                    ->orderBy('tasks.due_date')
                    ->orderBy('users.fullname')
                    ->select('course_modules.id','course_modules.course_id','courses.title as course','course_modules.title','tasks.name as task','tasks.due_date','task_file_users.mark','task_file_users.id as tid','users.fullname as student',DB::raw('(CASE WHEN (task_file_users.status IS NULL) THEN "BELUM" ELSE "SUDAH" END) AS status'))
                    // ->get();
                    ->paginate()
                        ->transform(function ($module) {
                            return [
                                'course_module_id' => $module->id,
                                'task_id' => $module->task_id,
                                'route' => url('courses-modules/'.$module->course_id),
                                'title' => $module->course .'-'.$module->title,
                                'task' => $module->task,
                                'end' => $module->date . ' ' . $module->due_date,
                                'student' => $module->student,
                                'status' => $module->status,
                                'mark' => $module->mark
                            ];
                        });
                        array_push($data, $data['courseTasks']);

                    // dd($data['courseTasks']);
                } elseif(in_array($userRole, $roleAdmin)) {
                    $data['courseTasks'] = Task::with(['course_module.course'])
                        ->withCount(['user_upload_tasks'])
                        ->join('task_file_users','task_file_users.task_id', '=', 'tasks.id')
                        //->join('users','users.id','=','tasks.teacher_id')
                       
                       // ->where('course_modules.is_active','=','1')
                       // ->where('tasks.due_date','>=',$datenow)
                        ->where(function($query){
                            return $query
                            ->whereNull('task_file_users.mark')
                            ->orWhere('task_file_users.status', '=', '0');
                        })
                        ->orderBy('due_date')
                        ->groupBy('tasks.id')
                        ->paginate(1000)
                        ->transform(function ($task) {
                            return [
                                'course_module_id' => $task->course_module->id,
                                'title' => $task->task_type .'-'.$task->course_module->course->title .'-'.$task->name,
                                'end' => $task->due_date,
                                'total' => $task->user_upload_tasks_count,
                                'task_id' => $task->id
                            ];
                        });

                    array_push($data, $data['courseTasks']);
                }
				return Inertia::render('Dashboard/Index',$data);
        }
    }
}