<?php

namespace App\Http\Controllers;

use App\Course;
use App\User;
use App\Group;
use App\CourseModule;
use App\CourseUnit;


use App\CourseUser;
use App\Subject;
use App\Category;
use App\Groupuser;
use App\Level;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CourseModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data['filters'] = Request::all('search');
        $data['courseModules'] = CourseModule::filter(Request::only('search'))
            ->select('course_modules.*','DATE_FORMAT(date, "%d-%m-%Y") as date_indonesia')
            ->orderBy('title')
            ->paginate()
            ->transform(function ($module) {
                return [
                    'id' => $module->id,
                    'title' => $module->title,
                    'date' => $module->date,
                    'date_indonesia' => $module->date_indonesia,
                    'schedule_start_at' => $module->schedule_start_at,
                    'schedule_end_at' => $module->schedule_end_at,
                    'actual_start_at' => $module->actual_start_at,
                    'actual_end_at' => $module->actual_end_at,
                    'is_active' => $module->is_active,
                    'course' => $module->course,
                    'teacher' => $module->teacher,
                    'group' => $module->group,
                ];
            });
        return Inertia::render('CourseModules/Index', $data);
    }

    public function detail($courseModuleId)
    {

        $data['courseModules'] = CourseModule::select(
                'course_modules.*','courses.title as coursetitle','subjects.name','subjects.subject_code',
                CourseModule::raw('DATE_FORMAT(date, "%d-%m-%Y") as date_indonesia'),
                CourseModule::raw('DATE_FORMAT(schedule_start_at, "%H:%i") as schedule_start_at_indonesia'),
                CourseModule::raw('DATE_FORMAT(schedule_end_at, "%H:%i") as schedule_end_at_indonesia'),
                CourseModule::raw('DATE_FORMAT(actual_start_at, "%H:%i") as actual_start_at_indonesia'),
                CourseModule::raw('DATE_FORMAT(actual_end_at, "%H:%i") as actual_end_at_indonesia')
            )->where('course_modules.id',$courseModuleId)
            ->leftjoin('courses','courses.id','course_modules.course_id')
            ->leftjoin('subjects','courses.subject_id','subjects.id')
            ->orderBy('date','asc')
            ->get()
           // dd($data['courseModules']);
            ->transform(function ($module) {
                return [

                    'course_id' => $module->course_id,
                    'id'    => $module->id,
                    'title' => $module->title,
                    'date'  => $module->date,
                    'date_indonesia' => $module->date_indonesia,
                    'schedule_start_at_indonesia' => $module->schedule_start_at_indonesia,
                    'schedule_end_at_indonesia'   => $module->schedule_end_at_indonesia,
                    'actual_start_at_indonesia'   => $module->actual_start_at_indonesia,
                    'actual_end_at_indonesia'     => $module->actual_end_at_indonesia,
                    'is_active'         => $module->is_active,
                    'unit'              => $module->unitactive($module->id),
                    'coursename'    =>$module->coursetitle,
                    'subjectname'   =>$module->name,
                    'subjectcode'   =>$module->subject_code,

                ];
            });

        return Inertia::render('CourseModules/Detail', $data);
    }

    public function getModuleByCourse(Course $course)
    {

        /// from helpers
        if(checkAccessCourse($course->id, Auth::id()) ===  false){
            return Redirect::route('courses.index');
        };

        $data['filters']    = Request::all('search');

        $data['dataCourse'] =  Course::where('id', $course->id)->get()->transform(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'photo' => $course->photo,
                    'join_code' => $course->join_code,
                    'subject' => $course->subject,
                    'category' => $course->category,
                    'level' => $course->level,
                    'teacher' => $course->teacher,
                    'group' => $course->group,
                    'is_active' => $course->is_active,
                    'course' => $course->course_user->where('user_id', Auth::id())->first()
                ];
            });


        $data['coursedone'] = $course->course_modules->whereNotNull('actual_end_at')->count();
        $data['courseModules'] = CourseModule::filter(Request::only('search'))
            ->select(
                'course_modules.*',
                CourseModule::raw('DATE_FORMAT(date, "%d-%m-%Y") as date_indonesia'),
                CourseModule::raw('DATE_FORMAT(schedule_start_at, "%H:%i") as schedule_start_at_indonesia'),
                CourseModule::raw('DATE_FORMAT(schedule_end_at, "%H:%i") as schedule_end_at_indonesia'),
                CourseModule::raw('DATE_FORMAT(actual_start_at, "%H:%i") as actual_start_at_indonesia'),
                CourseModule::raw('DATE_FORMAT(actual_end_at, "%H:%i") as actual_end_at_indonesia')
            )
            ->where('course_id', $course->id)
            ->where('is_active',1)
            ->orderBy('date','asc')
            ->paginate()
            ->transform(function ($module) {
                return [

                    'id'    => $module->id,
                    'title' => $module->title,
                    'date'  => $module->date,
                    'date_indonesia' => $module->date_indonesia,
                    'schedule_start_at_indonesia' => $module->schedule_start_at_indonesia,
                    'schedule_end_at_indonesia'   => $module->schedule_end_at_indonesia,
                    'actual_start_at_indonesia'   => $module->actual_start_at_indonesia,
                    'actual_end_at_indonesia'     => $module->actual_end_at_indonesia,
                    'is_active'         => $module->is_active,
                    'unit'              => $module->unitactive($module->id),
                    'taskCount' => $module->tasks->count()
                ];
            });

            // $data['taskCount'] = Task::where('course_module_id', $data['courseModules']->id)->count();

        return Inertia::render('CourseModules/Index', $data);
    }

    public function getCourseUnitNotActive(Course $course)
    {

        /// from helpers
        if(checkAccessCourse($course->id, Auth::id()) ===  false){
            return Redirect::route('courses.index');
        };

        $data['filters']    = Request::all('search');

        $data['dataCourse'] =  Course::where('id', $course->id)->get()->transform(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'photo' => $course->photo,
                    'join_code' => $course->join_code,
                    'subject' => $course->subject,
                    'category' => $course->category,
                    'level' => $course->level,
                    'teacher' => $course->teacher,
                    'group' => $course->group,
                    'is_active' => $course->is_active,
                    'course' => $course->course_user->where('user_id', Auth::id())->first()
                ];
            });


        $data['coursedone'] = $course->course_modules->whereNotNull('actual_end_at')->count();
        $data['courseModules'] = CourseModule::filter(Request::only('search'))
            ->select(
                'course_modules.*',
                CourseModule::raw('DATE_FORMAT(date, "%d-%m-%Y") as date_indonesia'),
                CourseModule::raw('DATE_FORMAT(schedule_start_at, "%H:%i") as schedule_start_at_indonesia'),
                CourseModule::raw('DATE_FORMAT(schedule_end_at, "%H:%i") as schedule_end_at_indonesia'),
                CourseModule::raw('DATE_FORMAT(actual_start_at, "%H:%i") as actual_start_at_indonesia'),
                CourseModule::raw('DATE_FORMAT(actual_end_at, "%H:%i") as actual_end_at_indonesia')
            )
            ->where('course_id', $course->id)
            ->where('is_active',0)
            ->orderBy('date','asc')
            ->paginate()
            ->transform(function ($module) {
                return [

                    'id'    => $module->id,
                    'title' => $module->title,
                    'date'  => $module->date,
                    'date_indonesia' => $module->date_indonesia,
                    'schedule_start_at_indonesia' => $module->schedule_start_at_indonesia,
                    'schedule_end_at_indonesia'   => $module->schedule_end_at_indonesia,
                    'actual_start_at_indonesia'   => $module->actual_start_at_indonesia,
                    'actual_end_at_indonesia'     => $module->actual_end_at_indonesia,
                    'is_active'         => $module->is_active,
                    'unit'              => $module->unit($module->id),
                    'taskCount' => $module->tasks->count()
                ];
            });

            // $data['taskCount'] = Task::where('course_module_id', $data['courseModules']->id)->count();

        return Inertia::render('CourseModules/UnitNotActive', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($course_id = null)
    {
        $data['dataCourse'] = Course::find($course_id);
        return Inertia::render('CourseModules/Create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $rules = [
            'title' => ['required', 'max:255'],
            'date' => ['required'],
            'schedule_start_at' => ['required'],
            'schedule_end_at' => ['required'],
            'course_id' => ['required'],
            'is_active' => ['required'],
        ];
        $messages = [
            'title.required' => 'Judul course module tidak boleh kosong',
            'date.required' => 'Tanggal tidak boleh kosong',
            'schedule_start_at.required' => 'Waktu mulai tidak boleh kosong',
            'schedule_end_at.required'  => 'Waktu selesai tidak boleh kosong',
            'course_id.required' => 'Course tidak boleh kosong',
        ];
        $request->validate($rules, $messages);
        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);

        $existModule = CourseModule::where('course_id', $request->course_id)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('schedule_start_at', [$request->schedule_start_at, $request->schedule_end_at])
                    ->orWhereBetween('schedule_end_at', [$request->schedule_start_at, $request->schedule_end_at]);
                // $query->where(DB::raw("'$request->schedule_start_at' NOT between schedule_start_at and schedule_end_at"))
                //     ->orWhere(DB::raw("'$request->schedule_end_at' NOT between schedule_start_at and schedule_end_at"))
                //     ->orWhere(function ($query2) use ($request) {
                //         $query2->where($request->schedule_start_at, '<', 'schedule_start_at')
                //             ->where($request->schedule_end_at, '>', 'schedule_end_at');
                //     });
            })
            ->first();

        if ($existModule) {
            return Redirect::back()->with('error', 'Sudah ada modul di tanggal dan jam tersebut.');
        }

        CourseModule::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('create.course.modules', $request->course_id)->with('success', 'Course Modul berhasil dibuat.');
        }
        return Redirect::route('course-modules.get_by_course', $request->course_id)->with('success', 'Course Modul berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CourseModule  $courseModule
     * @return \Illuminate\Http\Response
     */
    public function show(CourseModule $courseModule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CourseModule  $courseModule
     * @return \Illuminate\Http\Response
     */
    public function edit(CourseModule $courseModule)
    {
        //dd($courseModule);exit();

        $data['courseModules']         = $courseModule;
        /**
        $data['courseUnits'] = CourseUnit::where('course_module_id', $courseModule->id)
            ->paginate()
            ->transform(function ($unit) {
                return [
                    'id' => $unit->id,
                    'web_url' => $unit->web_url,
                    'file_name' => $unit->file_name,
                    'vidcon_link' => $unit->vidcon_link,
                    'link_to_unit' => $unit->link_to_unit,
                    'is_active' => $unit->is_active,
                    'course_module_id' => $unit->courseModule,
                ];
            });
         */

        return Inertia::render('CourseModules/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CourseModule  $courseModule
     * @return \Illuminate\Http\Response
     */
    public function update(\Illuminate\Http\Request $request, CourseModule $courseModule)
    {
        $rules = [
            'title' => ['required', 'max:255'],
            'date' => ['required'],
            // 'schedule_start_at' => ['required'],
            // 'schedule_end_at' => ['required'],
            'course_id' => ['required'],
            'is_active' => ['required'],
        ];
        $messages = [
            'title.required' => 'Judul course module tidak boleh kosong',
            'date.required' => 'Tanggal tidak boleh kosong',
            // 'schedule_start_at.required' => 'Waktu mulai tidak boleh kosong',
            // 'schedule_end_at.required' => 'Waktu selesai tidak boleh kosong',
            'course_id.required' => 'Course tidak boleh kosong',
        ];
        $request->validate($rules, $messages);

        $request->merge(['updated_by' => Auth::id()]);

        $existModule = CourseModule::where('course_id', $request->course_id)
            ->where('id', '!=', $courseModule->id)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('schedule_start_at', [$request->schedule_start_at, $request->schedule_end_at])
                    ->orWhereBetween('schedule_end_at', [$request->schedule_start_at, $request->schedule_end_at]);
            })
            ->first();

        if ($existModule) {
            return Redirect::back()->with('error', 'There is already a module on that date and time.');
        }

        $courseModule->update(
            $request->all()
        );

        return Redirect::back()->with('success', 'Pertemuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CourseModule  $courseModule
     * @return \Illuminate\Http\Response
     */
    public function destroy(CourseModule $courseModule)
    {
        $courseModule->is_active = 3;
        $courseModule->updated_by = Auth::id();
        $courseModule->save();

        return Redirect::back()->with('success', 'Pertemuan berhasil dinonaktifkan');
    }

    public function restore(CourseModule $course_module)
    {
        $course_module->is_active = 1;
        $course_module->updated_by = Auth::id();
        $course_module->save();

        return Redirect::back()->with('success', ' Pertemuan berhasil di-aktifkan');
    }
}
