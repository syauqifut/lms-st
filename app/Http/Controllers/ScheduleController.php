<?php

namespace App\Http\Controllers;

use App\CourseModule;
use App\Subject;
use App\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        $userID = Auth::user()->id;
        $data = [];

        // Orang Tua
        if ($userRole == 5) {
            $data['courseModules'] = CourseModule::whereIn('course_modules.is_active', [0,1])
            ->join('courses', 'courses.id', '=', 'course_modules.course_id')
            ->join('course_users', 'course_users.course_id', '=', 'courses.id')
            ->join('users as c', 'c.id', 'course_users.user_id')
            ->join('users as p', 'p.id', 'c.parent_id')
            ->where('p.id',$userID)
            ->where('course_users.is_active',1)
            ->get([
                'course_modules.id', 'course_users.course_id', 'course_modules.title', 'course_modules.date', 'course_modules.schedule_start_at',
                'course_modules.schedule_end_at', 'c.fullname'
                ])
                ->transform(function ($module) {
                    $course = Course::where('id', $module->course_id)->get();
                        $id = 0;
                        $nama = '';
                        foreach($course as $c){
                            $id = $c->subject_id;
                        }
                        $subject = Subject::where('id', $id)->get();
                        foreach($subject as $s){
                            $nama = $s->name;
                        }
                    return [
                        'course_module_id' => $module->id,
                        // 'route' => url('courses-modules/'.$module->course_id),
                        // 'route' => url('courses-modules/detail', $module->id),
                        'route' => url('#'),

                        'title' => $module->fullname . ' - ' . $module->title . ' - ' . $nama,
                        'start' => $module->date . ' ' . $module->schedule_start_at,
                        'end' => $module->date . ' ' . $module->schedule_end_at,
                    ];
                });
                array_push($data, $data['courseModules']);
            }elseif($userRole == 2){
                $data['courseModules'] = CourseModule::whereIn('course_modules.is_active', [0,1])
                ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                ->join('course_users', 'course_users.course_id', '=', 'courses.id')
                ->join('users as c', 'c.id', 'course_users.user_id')
                // ->join('users as p', 'p.id', 'c.parent_id')
                ->where('c.id',$userID)
                ->where('course_users.is_active',1)
                ->get([
                    'course_modules.id', 'course_users.course_id', 'course_modules.title', 'course_modules.date', 'course_modules.schedule_start_at',
                    'course_modules.schedule_end_at', 'c.fullname'
                    ])
                    ->transform(function ($module) {
                        $course = Course::where('id', $module->course_id)->get();
                        $id = 0;
                        $nama = '';
                        foreach($course as $c){
                            $id = $c->subject_id;
                        }
                        $subject = Subject::where('id', $id)->get();
                        foreach($subject as $s){
                            $nama = $s->name;
                        }
                        return [
                            'course_module_id' => $module->id,
                            // 'route' => url('courses-modules/'.$module->course_id),
                            'route' => url('courses-modules/detail', $module->id),
                            'title' => $module->fullname . ' - ' . $module->title . ' - ' . $nama,
                            'start' => $module->date . ' ' . $module->schedule_start_at,
                            'end' => $module->date . ' ' . $module->schedule_end_at,
                        ];
                    });
                    array_push($data, $data['courseModules']);
            }elseif($userRole == 3){
                $data['courseModules'] = CourseModule::whereIn('course_modules.is_active', [0,1])
                ->join('courses', 'courses.id', '=', 'course_modules.course_id')
               // ->join('subjects','subjects.id','=','courses.subjects_id')
                ->join('course_users', 'course_users.course_id', '=', 'courses.id')
                ->join('users as c', 'c.id', 'course_users.user_id')
                // ->join('users as p', 'p.id', 'c.parent_id')
                //->where('course_modules.is_active','=','1')
                ->where('c.id', Auth::id())
                ->where('course_users.is_active',1)
                // ->where('c.usertype_id',3)
                ->get([
                    'course_modules.id', 'course_users.course_id', 'course_modules.title', 'course_modules.date', 'course_modules.schedule_start_at',
                    'course_modules.schedule_end_at', 'c.fullname'
                    ])//,'subjects.id as sub','subjects.name as subname'
                    ->transform(function ($module) {
                        $course = Course::where('id', $module->course_id)->get();
                        $id = 0;
                        $nama = '';
                        foreach($course as $c){
                            $id = $c->subject_id;
                        }
                        $subject = Subject::where('id', $id)->get();
                        foreach($subject as $s){
                            $nama = $s->name;
                        }
                        return [
                            'course_module_id' => $module->id,
                            // 'route' => url('courses-modules/'.$module->course_id),
                            'route' => url('courses-modules/detail', $module->id),
                            'title' => $module->fullname . ' - ' . $module->title . ' - ' . $nama,
                            'start'=>$module->date . ' '. $module->schedule_start_at,
                            'end' => $module->date . ' ' . $module->schedule_end_at,
                        ];
                    });
                    array_push($data, $data['courseModules']);
            }else{
                $data['courseModules'] = CourseModule::whereIn('course_modules.is_active', [0,1])
                ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                ->join('course_users', 'course_users.course_id', '=', 'courses.id')
                ->join('users as c', 'c.id', 'course_users.user_id')
                // ->join('users as p', 'p.id', 'c.parent_id')
                // ->where('c.id', Auth::id())
                ->where('course_modules.is_active','=','1')//query untuk menampilkan course modules yang aktif
                ->where('c.usertype_id',3)
                ->where('course_users.is_active',1)
                ->get([
                    'course_modules.id', 'course_users.course_id', 'course_modules.title', 'course_modules.date', 'course_modules.schedule_start_at',
                    'course_modules.schedule_end_at', 'c.fullname'
                    ])
                    ->transform(function ($module) {
                        $course = Course::where('id', $module->course_id)->get();
                        $id = 0;
                        $nama = '';
                        foreach($course as $c){
                            $id = $c->subject_id;
                        }
                        $subject = Subject::where('id', $id)->get();
                        foreach($subject as $s){
                            $nama = $s->name;
                        }
                        return [
                            'course_module_id' => $module->id,
                           // 'route' => url('courses-modules/'.$module->course_id),

                         

                           'route' => url('courses-modules/detail', $module->id),
                            'title' => $module->fullname . ' - ' . $module->title . ' - ' . $nama,
                            'start'=>$module->date . ' ' . $module->schedule_start_at,
                            'end' => $module->date . ' ' . $module->schedule_end_at,
                        ];
                    });
                    array_push($data, $data['courseModules']);
            }

        // dd($data);
        return Inertia::render('Schedules/Index', $data);
    }

    public function day_schedules($date)
    {
        $course = Auth::user()->courses->pluck('id');
        $course_module = CourseModule::whereIn('course_modules.is_active', [0,1])
                ->whereIn('course_id', $course)
                ->whereDate('date', $date)
                ->get()->transform(function($query){
                    return [
                        'title' => 'Kelas '. $query->title,
                        'date' => $query->schedule_start_at . ' - ' . $query->schedule_end_at,
                        'id' => $query->id,
                        'route' => url('courses-modules/'.$query->course_id)
                    ];
                });
        Carbon::setLocale('id');
        return Inertia::render('Schedules/Show', [
            'schedules' => $course_module,
            'date' => Carbon::parse($date)->settings([
                            'locale' => 'id',
                        ])->translatedFormat('l, jS \\ F Y'), 
        ]);
        
    }
}
