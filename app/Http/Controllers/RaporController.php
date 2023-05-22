<?php

namespace App\Http\Controllers;

use App\Rapor;
use App\TaskFileUser;
use App\Presences;
use App\Subject;
use App\Course;
use App\User;
use App\Group;
use App\GroupUser;
use App\CourseUser;
use App\Interval;
use App\Persentase;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use DB;

class RaporController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(\Illuminate\Http\Request $request)
    {
        // dd($request);
        if (Auth::user()->role === 1 || Auth::user()->role === 4 ){
            if (!empty($request->subject && $request->kelas)){
                $data['subject'] = Subject::find($request->subject);
                $data['group'] = Group::find($request->kelas);
               
                $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
                            ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
                            ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
                            ->join('courses', 'courses.id', '=', 'course_users.course_id')
                            ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                            ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                            ->join('users', 'users.id', '=', 'task_file_users.user_id')
                            ->join('groups', 'groups.id', '=', 'courses.group_id')
                            ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                            ->join('users as u', 'u.id', '=', 'courses.teacher_id')
                            ->leftJoin('rapor', function($join){
                                $join->on('rapor.nim', '=', 'users.username');
                                $join->on('rapor.subject', '=', 'subjects.name');
                                $join->on('rapor.course_id', '=', 'courses.id');
                            })
                            ->where('subjects.id',$data['subject']->id)
                            ->where('groups.id',$data['group']->id)
                            ->where('courses.is_active', 1)
                            ->where('users.usertype_id', '2')
                            ->orderBy('groups.id')
                            ->orderBy('subjects.name')
                            ->orderBy('users.fullname')
                            ->groupBy('subjects.id')
                            ->groupBy('groups.id')
                            ->groupBy('users.id')
                            ->paginate()
                            ->transform(function ($rapors) {
                                $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                return [
                                    'id' => $rapors->id,
                                    'courseid' => $rapors->courseid,
                                    'subjectid' => $rapors->subjectid,
                                    'username' => $rapors->username,
                                    'fullname' => $rapors->fullname,
                                    'teacher' => $rapors->teacher,
                                    'groupid' => $rapors->groupid,
                                    'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                                    'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                                    'raporid' => $rapors->raporid,
                                ];
                            });
            }else if (!empty($request->subject)){
                $data['subject'] = Subject::find($request->subject);

                $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
                            ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
                            ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
                            ->join('courses', 'courses.id', '=', 'course_users.course_id')
                            ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                            ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                            ->join('users', 'users.id', '=', 'task_file_users.user_id')
                            ->join('groups', 'groups.id', '=', 'courses.group_id')
                            ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                            ->join('users as u', 'u.id', '=', 'courses.teacher_id')
                            ->leftJoin('rapor', function($join){
                                $join->on('rapor.nim', '=', 'users.username');
                                $join->on('rapor.subject', '=', 'subjects.name');
                                $join->on('rapor.course_id', '=', 'courses.id');
                            })
                            ->where('subjects.id',$data['subject']->id)
                            ->where('courses.is_active', 1)
                            ->where('users.usertype_id', '2')
                            ->orderBy('groups.id')
                            ->orderBy('subjects.name')
                            ->orderBy('users.fullname')
                            ->groupBy('subjects.id')
                            ->groupBy('groups.id')
                            ->groupBy('users.id')
                            ->paginate()
                            ->transform(function ($rapors) {
                                $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                return [
                                    'id' => $rapors->id,
                                    'courseid' => $rapors->courseid,
                                    'subjectid' => $rapors->subjectid,
                                    'username' => $rapors->username,
                                    'fullname' => $rapors->fullname,
                                    'teacher' => $rapors->teacher,
                                    'groupid' => $rapors->groupid,
                                    'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                                    'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                                    'raporid' => $rapors->raporid,
                                ];
                            });
            }else if (!empty($request->kelas)){
                $data['group'] = Group::find($request->kelas);

                $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
                            ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
                            ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
                            ->join('courses', 'courses.id', '=', 'course_users.course_id')
                            ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                            ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                            ->join('users', 'users.id', '=', 'task_file_users.user_id')
                            ->join('groups', 'groups.id', '=', 'courses.group_id')
                            ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                            ->join('users as u', 'u.id', '=', 'courses.teacher_id')
                            ->leftJoin('rapor', function($join){
                                $join->on('rapor.nim', '=', 'users.username');
                                $join->on('rapor.subject', '=', 'subjects.name');
                                $join->on('rapor.course_id', '=', 'courses.id');
                            })
                            ->where('groups.id',$data['group']->id)
                            ->where('courses.is_active', 1)
                            ->where('users.usertype_id', '2')
                            ->orderBy('groups.id')
                            ->orderBy('subjects.name')
                            ->orderBy('users.fullname')
                            ->groupBy('subjects.id')
                            ->groupBy('groups.id')
                            ->groupBy('users.id')
                            ->paginate()
                            ->transform(function ($rapors) {
                                $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                return [
                                    'id' => $rapors->id,
                                    'courseid' => $rapors->courseid,
                                    'subjectid' => $rapors->subjectid,
                                    'username' => $rapors->username,
                                    'fullname' => $rapors->fullname,
                                    'teacher' => $rapors->teacher,
                                    'groupid' => $rapors->groupid,
                                    'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                                    'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                                    'raporid' => $rapors->raporid,
                                ];
                            });
            }else{
                $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
                                ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
                                ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
                                ->join('courses', 'courses.id', '=', 'course_users.course_id')
                                ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                ->join('users', 'users.id', '=', 'task_file_users.user_id')
                                ->join('groups', 'groups.id', '=', 'courses.group_id')
                                ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                                ->join('users as u', 'u.id', '=', 'courses.teacher_id')
                                ->leftJoin('rapor', function($join){
                                    $join->on('rapor.nim', '=', 'users.username');
                                    $join->on('rapor.subject', '=', 'subjects.name');
                                    $join->on('rapor.course_id', '=', 'courses.id');
                                })
                                ->where('courses.is_active', 1)
                                ->where('users.usertype_id', '2')
                                ->orderBy('groups.id')
                                ->orderBy('subjects.name')
                                ->orderBy('users.fullname')
                                ->groupBy('subjects.id')
                                ->groupBy('groups.id')
                                ->groupBy('users.id')
                                ->paginate()
                                ->transform(function ($rapors) {
                                    $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                    return [
                                        'id' => $rapors->id,
                                        'courseid' => $rapors->courseid,
                                        'subjectid' => $rapors->subjectid,
                                        // 'nilai' => $rapors->nilau,
                                        // 'tugas' => $rapors->task_type === 'Tugas' ? $rapors->nilau : '',
                                        // 'uts' => $rapors->task_type === 'UTS' ? $rapors->nilau : '',
                                        // 'uas' => $rapors->task_type === 'UAS' ? $rapors->nilau : '',
                                        // 'mark' => $rapors->mark,
                                        // 'task_type' => $rapors->task_type,
                                        'username' => $rapors->username,
                                        'fullname' => $rapors->fullname,
                                        // 'sakit' => $rapors->presence->where('status','=','S')->count(),
                                        // 'izin' => $rapors->presence->where('status','=','I')->count(),
                                        // 'alpha' => $rapors->presence->where('status','=','A')->count(),
                                        'teacher' => $rapors->teacher,
                                        'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                                        'groupid' => $rapors->groupid,
                                        // 'course' => $rapors->title,
                                        // 'courseid' => $rapors->courseid,
                                        // 'userid' => $rapors->userid,
                                        // 'courses' => $rapors->titlem,
                                        'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                                        'raporid' => $rapors->raporid,
                                    ];
                                });}

        }else if (Auth::user()->role === 3){
            if (!empty($request->subject && $request->kelas)){
                $data['subject'] = Subject::find($request->subject);
                $data['group'] = Group::find($request->kelas);

                $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
                            ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
                            ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
                            ->join('courses', 'courses.id', '=', 'course_users.course_id')
                            ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                            ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                            ->join('users', 'users.id', '=', 'task_file_users.user_id')
                            ->join('groups', 'groups.id', '=', 'courses.group_id')
                            ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                            ->join('users as u', 'u.id', '=', 'courses.teacher_id')
                            ->leftJoin('rapor', function($join){
                                $join->on('rapor.nim', '=', 'users.username');
                                $join->on('rapor.subject', '=', 'subjects.name');
                                $join->on('rapor.course_id', '=', 'courses.id');
                            })
                            ->where('courses.teacher_id', Auth::id())
                            ->where('subjects.id',$data['subject']->id)
                            ->where('groups.id',$data['group']->id)
                            ->where('courses.is_active', 1)
                            ->where('users.usertype_id', '2')
                            ->orderBy('groups.id')
                            ->orderBy('subjects.name')
                            ->orderBy('users.fullname')
                            ->groupBy('subjects.id')
                            ->groupBy('groups.id')
                            ->groupBy('users.id')
                            ->paginate()
                            ->transform(function ($rapors) {
                                $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                return [
                                    'id' => $rapors->id,
                                    'courseid' => $rapors->courseid,
                                    'subjectid' => $rapors->subjectid,
                                    'username' => $rapors->username,
                                    'fullname' => $rapors->fullname,
                                    'teacher' => $rapors->teacher,
                                    'groupid' => $rapors->groupid,
                                    'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                                    'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                                    'raporid' => $rapors->raporid,
                                ];
                            });
            }else if (!empty($request->subject)){
                $data['subject'] = Subject::find($request->subject);

                $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
                            ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
                            ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
                            ->join('courses', 'courses.id', '=', 'course_users.course_id')
                            ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                            ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                            ->join('users', 'users.id', '=', 'task_file_users.user_id')
                            ->join('groups', 'groups.id', '=', 'courses.group_id')
                            ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                            ->join('users as u', 'u.id', '=', 'courses.teacher_id')
                            ->leftJoin('rapor', function($join){
                                $join->on('rapor.nim', '=', 'users.username');
                                $join->on('rapor.subject', '=', 'subjects.name');
                                $join->on('rapor.course_id', '=', 'courses.id');
                            })
                            ->where('courses.teacher_id', Auth::id())
                            ->where('subjects.id',$data['subject']->id)
                            ->where('courses.is_active', 1)
                            ->where('users.usertype_id', '2')
                            ->orderBy('groups.id')
                            ->orderBy('subjects.name')
                            ->orderBy('users.fullname')
                            ->groupBy('subjects.id')
                            ->groupBy('groups.id')
                            ->groupBy('users.id')
                            ->paginate()
                            ->transform(function ($rapors) {
                                $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                return [
                                    'id' => $rapors->id,
                                    'courseid' => $rapors->courseid,
                                    'subjectid' => $rapors->subjectid,
                                    'username' => $rapors->username,
                                    'fullname' => $rapors->fullname,
                                    'teacher' => $rapors->teacher,
                                    'groupid' => $rapors->groupid,
                                    'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                                    'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                                    'raporid' => $rapors->raporid,
                                ];
                            });
            }else if (!empty($request->kelas)){
                $data['group'] = Group::find($request->kelas);

                $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
                            ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
                            ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
                            ->join('courses', 'courses.id', '=', 'course_users.course_id')
                            ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                            ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                            ->join('users', 'users.id', '=', 'task_file_users.user_id')
                            ->join('groups', 'groups.id', '=', 'courses.group_id')
                            ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                            ->join('users as u', 'u.id', '=', 'courses.teacher_id')
                            ->leftJoin('rapor', function($join){
                                $join->on('rapor.nim', '=', 'users.username');
                                $join->on('rapor.subject', '=', 'subjects.name');
                                $join->on('rapor.course_id', '=', 'courses.id');
                            })
                            ->where('courses.teacher_id', Auth::id())
                            ->where('groups.id',$data['group']->id)
                            ->where('courses.is_active', 1)
                            ->where('users.usertype_id', '2')
                            ->orderBy('groups.id')
                            ->orderBy('subjects.name')
                            ->orderBy('users.fullname')
                            ->groupBy('subjects.id')
                            ->groupBy('groups.id')
                            ->groupBy('users.id')
                            ->paginate()
                            ->transform(function ($rapors) {
                                $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                return [
                                    'id' => $rapors->id,
                                    'courseid' => $rapors->courseid,
                                    'subjectid' => $rapors->subjectid,
                                    'username' => $rapors->username,
                                    'fullname' => $rapors->fullname,
                                    'teacher' => $rapors->teacher,
                                    'groupid' => $rapors->groupid,
                                    'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                                    'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                                    'raporid' => $rapors->raporid,
                                ];
                            });
            }else{
                $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
                ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
                                ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
                                ->join('courses', 'courses.id', '=', 'course_users.course_id')
                                ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                ->join('users', 'users.id', '=', 'task_file_users.user_id')
                                ->join('groups', 'groups.id', '=', 'courses.group_id')
                                ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                                ->join('users as u', 'u.id', '=', 'courses.teacher_id')
                                ->leftJoin('rapor', function($join){
                                    $join->on('rapor.nim', '=', 'users.username');
                                    $join->on('rapor.subject', '=', 'subjects.name');
                                    $join->on('rapor.course_id', '=', 'courses.id');
                                })
                                ->where('courses.teacher_id', Auth::id())
                                ->where('courses.is_active', 1)
                                ->where('users.usertype_id', '2')
                                ->orderBy('groups.id')
                                ->orderBy('subjects.name')
                                ->orderBy('users.fullname')
                                ->groupBy('subjects.id')
                                ->groupBy('groups.id')
                                ->groupBy('users.id')
                                ->paginate()
                                ->transform(function ($rapors) {
                                    $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                    return [
                                        'id' => $rapors->id,
                                        'courseid' => $rapors->courseid,
                                        'subjectid' => $rapors->subjectid,
                                        'username' => $rapors->username,
                                        'fullname' => $rapors->fullname,
                                        'teacher' => $rapors->teacher,
                                        'groupid' => $rapors->groupid,
                                        'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                                        'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                                        'raporid' => $rapors->raporid,
                                    ];
                                });}
        } elseif (Auth::user()->role===2){
            $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
            ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
            ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
            ->join('courses', 'courses.id', '=', 'course_users.course_id')
            ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
            ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
            ->join('users', 'users.id', '=', 'task_file_users.user_id')
            ->join('groups', 'groups.id', '=', 'courses.group_id')
            ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
            ->join('users as u', 'u.id', '=', 'courses.teacher_id')
            ->leftJoin('rapor', function($join){
                $join->on('rapor.nim', '=', 'users.username');
                $join->on('rapor.subject', '=', 'subjects.name');
                $join->on('rapor.course_id', '=', 'courses.id');
            })
            ->where('courses.is_active', 1)
            ->where('users.usertype_id', '2')
            ->where('users.id',Auth::user()->id)
            ->orderBy('groups.id')
            ->orderBy('subjects.name')
            ->orderBy('users.fullname')
            ->groupBy('subjects.id')
            ->groupBy('groups.id')
            ->groupBy('users.id')
            ->paginate()
            ->transform(function ($rapors) {
                $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $rapors->id,
                    'courseid' => $rapors->courseid,
                    'subjectid' => $rapors->subjectid,
                    // 'nilai' => $rapors->nilau,
                    // 'tugas' => $rapors->task_type === 'Tugas' ? $rapors->nilau : '',
                    // 'uts' => $rapors->task_type === 'UTS' ? $rapors->nilau : '',
                    // 'uas' => $rapors->task_type === 'UAS' ? $rapors->nilau : '',
                    // 'mark' => $rapors->mark,
                    // 'task_type' => $rapors->task_type,
                    'username' => $rapors->username,
                    'fullname' => $rapors->fullname,
                    // 'sakit' => $rapors->presence->where('status','=','S')->count(),
                    // 'izin' => $rapors->presence->where('status','=','I')->count(),
                    // 'alpha' => $rapors->presence->where('status','=','A')->count(),
                    'teacher' => $rapors->teacher,
                    'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                    'groupid' => $rapors->groupid,
                    // 'course' => $rapors->title,
                    // 'courseid' => $rapors->courseid,
                    // 'userid' => $rapors->userid,
                    // 'courses' => $rapors->titlem,
                    'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                    'raporid' => $rapors->raporid,
                    'authuser' => Auth::user()->role,
                ];
            });

        } elseif (Auth::user()->role===5){
            $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
            ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
            ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
            ->join('courses', 'courses.id', '=', 'course_users.course_id')
            ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
            ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
            ->join('users', 'users.id', '=', 'task_file_users.user_id')
            ->join('groups', 'groups.id', '=', 'courses.group_id')
            ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
            ->join('users as u', 'u.id', '=', 'courses.teacher_id')
            ->leftJoin('rapor', function($join){
                $join->on('rapor.nim', '=', 'users.username');
                $join->on('rapor.subject', '=', 'subjects.name');
                $join->on('rapor.course_id', '=', 'courses.id');
            })
            ->where('courses.is_active', 1)
            ->where('users.usertype_id', '2')
            ->where('users.parent_id',Auth::user()->id)
            ->orderBy('groups.id')
            ->orderBy('subjects.name')
            ->orderBy('users.fullname')
            ->groupBy('subjects.id')
            ->groupBy('groups.id')
            ->groupBy('users.id')
            ->paginate()
            ->transform(function ($rapors) {
                $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $rapors->id,
                    'courseid' => $rapors->courseid,
                    'subjectid' => $rapors->subjectid,
                    // 'nilai' => $rapors->nilau,
                    // 'tugas' => $rapors->task_type === 'Tugas' ? $rapors->nilau : '',
                    // 'uts' => $rapors->task_type === 'UTS' ? $rapors->nilau : '',
                    // 'uas' => $rapors->task_type === 'UAS' ? $rapors->nilau : '',
                    // 'mark' => $rapors->mark,
                    // 'task_type' => $rapors->task_type,
                    'username' => $rapors->username,
                    'fullname' => $rapors->fullname,
                    // 'sakit' => $rapors->presence->where('status','=','S')->count(),
                    // 'izin' => $rapors->presence->where('status','=','I')->count(),
                    // 'alpha' => $rapors->presence->where('status','=','A')->count(),
                    'teacher' => $rapors->teacher,
                    'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                    'groupid' => $rapors->groupid,
                    // 'course' => $rapors->title,
                    // 'courseid' => $rapors->courseid,
                    // 'userid' => $rapors->userid,
                    // 'courses' => $rapors->titlem,
                    'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                    'raporid' => $rapors->raporid,
                    'authuser' => Auth::user()->role,
                ];
            });

        }
        // dd($data['rapor']);
        $data['filters'] = Request::all('search');

        $data['matkul'] = Subject::
            orderBy('name', 'asc')
            ->get()
            ->transform(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name . ' (' .$subject->subject_code. ')',
                ];
            });

        $data['kelas'] = Group::isactive()
            ->orderBy('classes', 'asc')
            ->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'classes' => $group->classes . ' ' .$group->huruf. ' ('. $periode .' / ' .$group->year. ')',
                ];
            });

            $data['authusers']=Auth::user()->role;
        return Inertia::render('Rapor/Index', $data);
    }

    public function generateall($subject, $kelas){
        $data['rapor'] =  TaskFileUser::filter(Request::only('search'))
                        ->select(DB::raw("SUM(task_file_users.mark) as nilau"), 'task_file_users.id', 'courses.id as courseid', 'task_file_users.user_id', 'subjects.id as subjectid', 'users.username', 'users.fullname', 'users.id as userid', 'task_file_users.mark as nilai', 'tasks.task_type',  'groups.classes',  'subjects.name', 'u.fullname as teacher', 'courses.title as title', 'course_modules.title as titlem', 'rapor.id as raporid', 'subjects.subject_code', 'groups.id as groupid', 'groups.academicterms',  'groups.year')
                        ->join('course_users', 'course_users.user_id', '=', 'task_file_users.user_id')
                        ->join('courses', 'courses.id', '=', 'course_users.course_id')
                        ->join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                        ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                        ->join('users', 'users.id', '=', 'task_file_users.user_id')
                        ->join('groups', 'groups.id', '=', 'courses.group_id')
                        ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                        ->join('users as u', 'u.id', '=', 'courses.teacher_id')
                        ->leftJoin('rapor', function($join){
                            $join->on('rapor.nim', '=', 'users.username');
                            $join->on('rapor.subject', '=', 'subjects.name');
                            $join->on('rapor.course_id', '=', 'courses.id');
                        })
                        ->where('courses.is_active', 1)
                        ->where('users.usertype_id', '2')
                        ->where('subjects.id', $subject)
                        ->where('groups.id', $kelas)
                        ->orderBy('groups.id')
                        ->orderBy('subjects.name')
                        ->orderBy('users.fullname')
                        ->groupBy('subjects.id')
                        ->groupBy('groups.id')
                        ->groupBy('users.id')
                        ->paginate()
                        ->transform(function ($rapors) {
                            $periode = $rapors->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                return [
                                    'id' => $rapors->id,
                                    'courseid' => $rapors->courseid,
                                    'subjectid' => $rapors->subject,
                                    // 'nilai' => $rapors->nilau,
                                    // 'tugas' => $rapors->task_type === 'Tugas' ? $rapors->nilau : '',
                                    // 'uts' => $rapors->task_type === 'UTS' ? $rapors->nilau : '',
                                    // 'uas' => $rapors->task_type === 'UAS' ? $rapors->nilau : '',
                                    // 'mark' => $rapors->mark,
                                    // 'task_type' => $rapors->task_type,
                                    'username' => $rapors->username,
                                    'fullname' => $rapors->fullname,
                                    // 'sakit' => $rapors->presence->where('status','=','S')->count(),
                                    // 'izin' => $rapors->presence->where('status','=','I')->count(),
                                    // 'alpha' => $rapors->presence->where('status','=','A')->count(),
                                    'teacher' => $rapors->teacher,
                                    'group' => $rapors->classes. ' ('. $periode .' / ' .$rapors->year. ')',
                                    'groupid' => $rapors->groupid,
                                    // 'course' => $rapors->title,
                                    // 'courseid' => $rapors->courseid,
                                    // 'userid' => $rapors->userid,
                                    // 'courses' => $rapors->titlem,
                                    'subject' => $rapors->name. ' ('.$rapors->subject_code. ')',
                                    'raporid' => $rapors->raporid,
                                ];
                        });
                        

                        foreach($data['rapor'] as $rapor){
                            $course = Course::where('id', $rapor['courseid'])->first();
                            $data['rapor'] = $rapor;
                            $data['course'] = $course;
                            $groupdata = Course::join('groups','groups.id','=','courses.group_id')->where('courses.id',$course->id)->first();
                            $periode = $groupdata['academicterms'] == 1 ? 'GANJIL' : 'GENAP';
                            $data['groupinfo'] = $groupdata['classes'].' ('.$periode.' / '.$groupdata['year'].')';
                            $user = User::where('username', $rapor['username'])->first();
                            // dd($groupid);
                    
                            $kategori = Course::where('id', $course->id)->pluck('category_id')->first();
                            $data['persentugas'] = Persentase::where('category_id', $kategori)->where('task_type', 'tugas')->pluck('persen')->first();
                            $data['persenuts'] = Persentase::where('category_id', $kategori)->where('task_type', 'uts')->pluck('persen')->first();
                            $data['persenuas'] = Persentase::where('category_id', $kategori)->where('task_type', 'uas')->pluck('persen')->first();
                            $data['persenperform'] = Persentase::where('category_id', $kategori)->where('task_type', 'perform')->pluck('persen')->first();
                            $data['persensakit'] = Persentase::where('category_id', $kategori)->where('task_type', 'sakit')->pluck('persen')->first();
                            $data['persenizin'] = Persentase::where('category_id', $kategori)->where('task_type', 'izin')->pluck('persen')->first();
                            $data['persenalpha'] = Persentase::where('category_id', $kategori)->where('task_type', 'alpha')->pluck('persen')->first();
                            // dd($course->id, $kategori, $tugas);
                    
                            $subject = Course::where('id',$course->id)->pluck('subject_id')->first();
                            $data['subject'] = Subject::leftJoin('courses','courses.subject_id','=','subjects.id')->where('courses.id',$course->id)->pluck('subjects.name')->first();
                            $data['subjectcode'] = Subject::leftJoin('courses','courses.subject_id','=','subjects.id')->where('courses.id',$course->id)->pluck('subjects.subject_code')->first();
                            // dd($data['subject']);
                            $data['tugas'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                                           ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                                           ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                           ->whereIN('tasks.task_type', ['Tugas', 'UH'])
                                                           ->where('courses.id', '=',$course->id)
                                                           ->where('courses.subject_id', '=',$subject)
                                                           ->where('task_file_users.user_id', '=',$user->id)
                                                           ->sum('mark');
                            $data['bagitugas'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                                              ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                                              ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                              ->whereIN('tasks.task_type', ['Tugas', 'UH'])
                                                              ->where('courses.id', '=',$course->id)
                                                              ->where('courses.subject_id', '=',$subject)
                                                              ->where('task_file_users.user_id', '=',$user->id)
                                                              ->count('mark');
                            $data['uts'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                                         ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                                         ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                         ->where('tasks.task_type', '=', 'UTS')
                                                         ->where('courses.id', '=',$course->id)
                                                         ->where('courses.subject_id', '=',$subject)
                                                         ->where('task_file_users.user_id', '=',$user->id)
                                                         ->sum('mark');
                            $countUts = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                                         ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                                         ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                         ->where('tasks.task_type', '=', 'UTS')
                                                         ->where('courses.id', '=',$course->id)
                                                         ->where('courses.subject_id', '=',$subject)
                                                         ->where('task_file_users.user_id', '=',$user->id)
                                                         ->count('mark');
                            $data['uts'] = ($countUts != 0) ? $data['uts'] / $countUts : 0; 
                            $data['uas'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                                         ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                                         ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                         ->where('tasks.task_type', '=', 'UAS')
                                                         ->where('courses.id', '=',$course->id)
                                                         ->where('courses.subject_id', '=',$subject)
                                                         ->where('task_file_users.user_id', '=',$user->id)
                                                         ->sum('mark');
                            $countUas = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                                         ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                                         ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                         ->where('tasks.task_type', '=', 'UAS')
                                                         ->where('courses.id', '=',$course->id)
                                                         ->where('courses.subject_id', '=',$subject)
                                                         ->where('task_file_users.user_id', '=',$user->id)
                                                         ->count('mark');
                            $data['uas'] = ($countUas != 0) ? $data['uas'] / $countUas : 0; 
                            $data['perform'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                                         ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                                         ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                         ->where('tasks.task_type', '=', 'Perform')
                                                        //  ->where('courses.id', '=',$course->id)
                                                         ->where('courses.id', '=',$course->id)
                                                         ->where('courses.subject_id', '=',$subject)
                                                         ->where('task_file_users.user_id', '=',$user->id)
                                                         ->sum('mark');
                            $countPerform = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                                         ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                                         ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                         ->where('tasks.task_type', '=', 'Perform')
                                                        //  ->where('courses.id', '=',$course->id)
                                                         ->where('courses.id', '=',$course->id)
                                                         ->where('courses.subject_id', '=',$subject)
                                                         ->where('task_file_users.user_id', '=',$user->id)
                                                         ->count('mark');
                            $data['perform'] = ($countPerform != 0) ? $data['perform'] / $countPerform : 0; 
                            $data['sakit'] = User::join('presences', 'presences.student_user_id', '=', 'users.id')
                                                   ->join('course_modules', 'course_modules.id', '=', 'presences.coursemodule_id')
                                                   ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                   ->where('presences.status','=', 'S')
                                                   ->where('courses.id', '=',$course->id)
                                                   ->where('courses.subject_id', '=',$subject)
                                                   ->where('users.id','=',$user->id)
                                                   ->count('presences.id');
                            $data['izin'] = User::join('presences', 'presences.student_user_id', '=', 'users.id')
                                                  ->join('course_modules', 'course_modules.id', '=', 'presences.coursemodule_id')
                                                  ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                  ->where('presences.status','=', 'L')
                                                  ->where('courses.id', '=',$course->id)
                                                  ->where('courses.subject_id', '=',$subject)
                                                  ->where('users.id','=',$user->id)
                                                  ->count('presences.id');
                            $data['alpha'] = User::join('presences', 'presences.student_user_id', '=', 'users.id')
                                                   ->join('course_modules', 'course_modules.id', '=', 'presences.coursemodule_id')
                                                   ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                                   ->where('presences.status','=', 'A')
                                                   ->where('courses.id', '=',$course->id)
                                                   ->where('courses.subject_id', '=',$subject)
                                                   ->where('users.id','=',$user->id)
                                                   ->count('presences.id');
                            $data['teacher'] = Course::join('users', 'users.id', '=', 'courses.teacher_id')
                                                      ->where('courses.id', '=',$course->id)
                                                      ->where('courses.subject_id','=',$subject)
                                                      ->get('users.fullname');
                            $data['userdata'] = TaskFileUser::select('users.username','users.fullname')
                                                              ->join('users', 'users.id', '=', 'task_file_users.user_id')
                                                              ->where('users.id','=',$user->id)
                                                              ->get();
                            $data['groupuser'] = GroupUser::select('groups.classes','users.fullname')
                                                            ->join('groups', 'group_users.group_id', '=', 'groups.id')
                                                            ->join('users', 'users.id', '=', 'groups.mainteacher')
                                                            ->where('user_id','=',$user->id)
                                                            ->get();
                            $data['courseuser'] = CourseUser::select('subjects.name', 'users.fullname')
                                                              ->join('courses', 'course_users.course_id', '=', 'courses.id')
                                                              ->join('users', 'users.id', '=', 'courses.teacher_id')
                                                              ->join('course_modules', 'course_modules.course_id', '=', 'courses.id')
                                                              ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                                                            //   ->where('subjects.id','=',$subject->id)
                                                              ->where('user_id','=',$user->id)->get();
                            $data['authuser']=Auth::user()->role;

                            if($rapor['raporid'] == null){
                                $kategori = Course::where('id', $rapor['courseid'])->pluck('category_id')->first();
                                $tugas = Persentase::where('category_id', $kategori)->where('task_type', 'tugas')->pluck('persen')->first();
                                $uts = Persentase::where('category_id', $kategori)->where('task_type', 'uts')->pluck('persen')->first();
                                $uas = Persentase::where('category_id', $kategori)->where('task_type', 'uas')->pluck('persen')->first();
                                $perform = Persentase::where('category_id', $kategori)->where('task_type', 'perform')->pluck('persen')->first();
                                $sakit = Persentase::where('category_id', $kategori)->where('task_type', 'sakit')->pluck('persen')->first();
                                $izin = Persentase::where('category_id', $kategori)->where('task_type', 'izin')->pluck('persen')->first();
                                $alpha = Persentase::where('category_id', $kategori)->where('task_type', 'alpha')->pluck('persen')->first();
                        
                                // if (isset($tugas) and isset($uts) and isset($uas) and isset($uts) and isset($perform) and isset($sakit) and isset($izin) and isset($alpha)){
                                //     $nilai = ((($request->uts*($uts/100)) + ($request->tugas*($tugas/100)) + ($request->uas*($uas/100)) + ($request->perform*($perform/100))) - (($request->sakit*($sakit/100)) + ($request->izin*($izin/100)) + ($request->alpha*($alpha/100))));
                                // }else{
                                //     $nilai = ((($request->uts*3) + ($request->tugas*3) + ($request->uas*2) + ($request->perform*2)) - (($request->sakit*2) + ($request->izin*3) + ($request->alpha*5)));
                                // }
                                if ($data['uas'] !== 0){
                                    $nilai = ((($data['uts']*($uts/100)) + ($data['tugas']*($tugas/100)) + ($data['uas']*($uas/100)) + ($data['perform']*($perform/100))) - (($data['sakit']*($sakit/100)) + ($data['izin']*($izin/100)) + ($data['alpha']*($alpha/100))));
                                }else{
                                    $nilai = 0;
                                };
                                // dd($nilai);
                        
                                $level = Group::where('id', '=',$rapor['groupid'])->pluck('level_id');
                                $group = Group::where('id', '=',$rapor['groupid'])->first();
                                $huruf = Interval::where('minmark', '<=', $nilai)
                                                   ->where('maxmark', '>=', $nilai)
                                                   ->where('level_id', '=', $level)
                                                   ->pluck('alphabet')->first();
                        
                                $idsubject = Course::where('id', $rapor['courseid'])->pluck('subject_id')->first();
                                // $kkm = Kkm::where('subject_id', $idsubject)->pluck('nilai')->first();       
                                // if($nilai < $kkm){
                                //     $keterangan = 'Belum Tuntas';
                                // }else if($nilai == $kkm){
                                //     $keterangan = 'Tuntas';
                                // }else if($nilai > $kkm){
                                //     $keterangan = 'Terlampaui';
                                // };
                                // dd($data['groupuser']);
                        
                                Rapor::create([
                                    'nim' => $user->username,
                                    'nama' => $user->fullname,
                                    'tugas' => $data['tugas'],
                                    'uts' => $data['uts'],
                                    'uas' => $data['uas'],
                                    'perform' => $data['perform'],
                                    'sakit' => $data['sakit'],
                                    'izin' => $data['izin'],
                                    'alpha' => $data['alpha'],
                                    'nilai' => $nilai,
                                    'kelas' => $group->classes,
                                    'created_by' => Auth::id(), 
                                    'updated_by' => Auth::id(), 
                                    'huruf' => $huruf, 
                                    'walikelas' => $rapor['teacher'],
                                    'gurupengajar' => $rapor['teacher'],
                                    'subject' => $data['subject'],
                                    'course_id' => $rapor['courseid'],
                                    // 'keterangan' => $keterangan
                                ])->save();
                            }
                        }

                        return Redirect::route('rapor.index')->with('success', 'Generate all report successfully.');
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if (Auth::user()->role === 3){
            $data['rapor'] = Rapor::filter(Request::only('search'))
                        ->select('rapor.id', 'rapor.nim', 'rapor.nama', 'rapor.tugas', 'rapor.uts', 'rapor.uas', 'rapor.perform', 'rapor.sakit', 'rapor.izin', 'rapor.alpha', 'rapor.nilai', 'rapor.huruf', 'rapor.kelas', 'rapor.walikelas', 'rapor.subject', 'rapor.gurupengajar', 'groups.academicterms', 'groups.year', 'subjects.subject_code')
                        ->join('courses','courses.id','=','rapor.course_id')
                        ->join('groups','groups.id','=','courses.group_id')
                        ->join('subjects','subjects.id','=','courses.subject_id')
                        ->where('rapor.gurupengajar', Auth::user()->fullname)
                        ->orderBy('subject')
                        ->orderBy('nama')
                        ->paginate()
                        ->transform(function ($rapor) {
                            $periode = $rapor->academicterms == 1 ? 'GANJIL' : 'GENAP';
                            return [
                                'id' => $rapor->id,
                                'nim' => $rapor->nim,
                                'nama' => $rapor->nama,
                                'tugas' => $rapor->tugas,
                                'uts' => $rapor->uts,
                                'uas' => $rapor->uas,
                                'perform' => $rapor->perform,
                                'sakit' => $rapor->sakit,
                                'izin' => $rapor->izin,
                                'alpha' => $rapor->alpha,
                                'nilai' => $rapor->nilai,
                                'huruf' => $rapor->huruf,
                                'kelas' => $rapor->kelas. ' ('. $periode .' / ' .$rapor->year. ')',
                                'walikelas' => $rapor->walikelas,
                                'subject' => $rapor->subject. ' ('.$rapor->subject_code.')',
                                'gurupengajar' => $rapor->gurupengajar,
                            ];
                        });
        }else if(Auth::user()->role === 1){
            $data['rapor'] = Rapor::filter(Request::only('search'))
                            ->select('rapor.id', 'rapor.nim', 'rapor.nama', 'rapor.tugas', 'rapor.uts', 'rapor.uas', 'rapor.perform', 'rapor.sakit', 'rapor.izin', 'rapor.alpha', 'rapor.nilai', 'rapor.huruf', 'rapor.kelas', 'rapor.walikelas', 'rapor.subject', 'rapor.gurupengajar', 'groups.academicterms', 'groups.year', 'subjects.subject_code')
                            ->join('courses','courses.id','=','rapor.course_id')
                            ->join('groups','groups.id','=','courses.group_id')
                            ->join('subjects','subjects.id','=','courses.subject_id')
                            ->orderBy('subject')
                            ->orderBy('nama')
                            ->paginate()
                            ->transform(function ($rapor) {
                                $periode = $rapor->academicterms == 1 ? 'GANJIL' : 'GENAP';
                                return [
                                    'id' => $rapor->id,
                                    'nim' => $rapor->nim,
                                    'nama' => $rapor->nama,
                                    'tugas' => $rapor->tugas,
                                    'uts' => $rapor->uts,
                                    'uas' => $rapor->uas,
                                    'perform' => $rapor->perform,
                                    'sakit' => $rapor->sakit,
                                    'izin' => $rapor->izin,
                                    'alpha' => $rapor->alpha,
                                    'nilai' => $rapor->nilai,
                                    'huruf' => $rapor->huruf,
                                    'kelas' => $rapor->kelas. ' ('. $periode .' / ' .$rapor->year. ')',
                                    'walikelas' => $rapor->walikelas,
                                    'subject' => $rapor->subject. ' ('.$rapor->subject_code.')',
                                    'gurupengajar' => $rapor->gurupengajar,
                                ];
                            });
        }
        $data['filter'] = Request::all('search');
        $data['subject'] = Subject::orderBy('name')->get();
        $data['group'] = Group::orderBy('classes')->get();
        $data['user'] = User::orderBy('fullname')->get();
        return Inertia::render('Rapor/Create', $data);
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
            'nim' => ['required', 'max:255'],
            'nama' => ['required', 'max:255'],
            'kelas' => ['required', 'max:255'],
            'walikelas' => ['required', 'max:255'],
            'subject' => ['required', 'max:255'],
            'gurupengajar' => ['required', 'max:255'],
        ];
        $messages = [
            'nim.required' => 'NIM cannot be empty',
            'nama.required' => 'Name cannot be empty',
            'kelas.required' => 'Class cannot be empty',
            'walikelas.required' => 'Teacher Class cannot be empty',
            'subject.required' => 'Subject cannot be empty',
            'gurupengajar.required' => 'Teacher cannot be empty',
        ];
        
        $kategori = Course::where('id', $request->course_id)->pluck('category_id')->first();
        $tugas = Persentase::where('category_id', $kategori)->where('task_type', 'tugas')->pluck('persen')->first();
        $uts = Persentase::where('category_id', $kategori)->where('task_type', 'uts')->pluck('persen')->first();
        $uas = Persentase::where('category_id', $kategori)->where('task_type', 'uas')->pluck('persen')->first();
        $perform = Persentase::where('category_id', $kategori)->where('task_type', 'perform')->pluck('persen')->first();
        $sakit = Persentase::where('category_id', $kategori)->where('task_type', 'sakit')->pluck('persen')->first();
        $izin = Persentase::where('category_id', $kategori)->where('task_type', 'izin')->pluck('persen')->first();
        $alpha = Persentase::where('category_id', $kategori)->where('task_type', 'alpha')->pluck('persen')->first();

        $request->validate($rules, $messages);
        if ($request->uas !== 0){
            // $nilai = ((($request->uts*4) + ($request->tugas*3) + ($request->uas*3) + ($request->perform*3)) - (($request->sakit*20) + ($request->izin*30) + ($request->alpha*50)))/250;
            // $nilai = ((($request->uts*$uts) + ($request->tugas*$tugas) + ($request->uas*$uas) + ($request->perform*$perform)) - (($request->sakit*$sakit) + ($request->izin*$izin) + ($request->alpha*$alpha)));
            $nilai = ((($request->uts*($uts/100)) + ($request->tugas*($tugas/100)) + ($request->uas*($uas/100)) + ($request->perform*($perform/100))) - (($request->sakit*($sakit/100)) + ($request->izin*($izin/100)) + ($request->alpha*($alpha/100))));
        }else{
            $nilai = 0;
        };
        // dd($nilai);


        $level = Group::where('classes', '=',$request->kelas)->pluck('level_id') ;
        $huruf = Interval::where('minmark', '<=', $nilai)
                           ->where('maxmark', '>=', $nilai)
                           ->where('level_id', '=', $level)
                           ->pluck('alphabet')->first();
        // dd($huruf);
        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id(), 'nilai' => $nilai, 'huruf' => $huruf]);
        if (Rapor::where('nim', '=', $request->nim)
                   ->where('subject', '=', $request->subject)
                   ->where('course_id', '=', $request->course_id)
                   ->exists()){
            return Redirect::back()->with('error', 'Rapor already exist');
        }else {
            Rapor::create($request->all());
            return Redirect::route('rapor.index')->with('success', 'Report generated successfully.');
        }

        if ($request->addAgain) {
            return Redirect::route('rapor.edit')->with('success', 'Report generated successfully.');
        }
        return Redirect::route('rapor.index')->with('success', 'Report generated successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Rapor  $rapor
     * @return \Illuminate\Http\Response
     */
    public function show(Rapor $rapor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rapor  $rapor
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskFileUser $rapor, Course $course)
    {
        $data['rapor'] = $rapor;
        $data['course'] = $course;
        $groupdata = Course::join('groups','groups.id','=','courses.group_id')->where('courses.id',$course->id)->first();
        $periode = $groupdata['academicterms'] == 1 ? 'GANJIL' : 'GENAP';
        $data['groupinfo'] = $groupdata['classes'].' ('.$periode.' / '.$groupdata['year'].')';
        // dd($groupid);

        $kategori = Course::where('id', $course->id)->pluck('category_id')->first();
        $data['persentugas'] = Persentase::where('category_id', $kategori)->where('task_type', 'tugas')->pluck('persen')->first();
        $data['persenuts'] = Persentase::where('category_id', $kategori)->where('task_type', 'uts')->pluck('persen')->first();
        $data['persenuas'] = Persentase::where('category_id', $kategori)->where('task_type', 'uas')->pluck('persen')->first();
        $data['persenperform'] = Persentase::where('category_id', $kategori)->where('task_type', 'perform')->pluck('persen')->first();
        $data['persensakit'] = Persentase::where('category_id', $kategori)->where('task_type', 'sakit')->pluck('persen')->first();
        $data['persenizin'] = Persentase::where('category_id', $kategori)->where('task_type', 'izin')->pluck('persen')->first();
        $data['persenalpha'] = Persentase::where('category_id', $kategori)->where('task_type', 'alpha')->pluck('persen')->first();
        // dd($course->id, $kategori, $tugas);

        $subjectid = Course::where('id',$course->id)->pluck('subject_id')->first();
        $data['subject'] = Subject::leftJoin('courses','courses.subject_id','=','subjects.id')->where('courses.id',$course->id)->pluck('subjects.name')->first();
        $data['subjectcode'] = Subject::leftJoin('courses','courses.subject_id','=','subjects.id')->where('courses.id',$course->id)->pluck('subjects.subject_code')->first();
        // dd($data['subject']);
        $data['tugas'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                       ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                       ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                       ->whereIN('tasks.task_type', ['Tugas', 'UH'])
                                       ->where('courses.id', '=',$course->id)
                                       ->where('courses.subject_id', '=',$subjectid)
                                       ->where('task_file_users.user_id', '=',$rapor->user_id)
                                       ->sum('mark');
        $data['bagitugas'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                          ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                          ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                          ->whereIN('tasks.task_type', ['Tugas', 'UH'])
                                          ->where('courses.id', '=',$course->id)
                                          ->where('courses.subject_id', '=',$subjectid)
                                          ->where('task_file_users.user_id', '=',$rapor->user_id)
                                          ->count('mark');
        $data['uts'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'UTS')
                                     ->where('courses.id', '=',$course->id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$rapor->user_id)
                                     ->sum('mark');
        $countUts = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'UTS')
                                     ->where('courses.id', '=',$course->id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$rapor->user_id)
                                     ->count('mark');
        $data['uts'] = ($countUts != 0) ? $data['uts'] / $countUts : 0;          
        $data['uas'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'UAS')
                                     ->where('courses.id', '=',$course->id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$rapor->user_id)
                                     ->sum('mark');
        $countUas = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'UAS')
                                     ->where('courses.id', '=',$course->id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$rapor->user_id)
                                     ->count('mark');
        $data['uas'] = ($countUas != 0) ? $data['uas'] / $countUas : 0;          
        $data['perform'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'Perform')
                                     ->where('courses.id', '=',$course->id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$rapor->user_id)
                                     ->sum('mark');
        $countPerform = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'Perform')
                                     ->where('courses.id', '=',$course->id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$rapor->user_id)
                                     ->count('mark');
        $data['perform'] = ($countPerform != 0) ? $data['perform'] / $countPerform : 0;   
        $data['sakit'] = User::join('presences', 'presences.student_user_id', '=', 'users.id')
                               ->join('course_modules', 'course_modules.id', '=', 'presences.coursemodule_id')
                               ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                               ->where('presences.status','=', 'S')
                               ->where('courses.id', '=',$course->id)
                               ->where('courses.subject_id', '=',$subjectid)
                               ->where('users.id','=',$rapor->user_id)
                               ->count('presences.id');
        $data['izin'] = User::join('presences', 'presences.student_user_id', '=', 'users.id')
                              ->join('course_modules', 'course_modules.id', '=', 'presences.coursemodule_id')
                              ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                              ->where('presences.status','=', 'L')
                              ->where('courses.id', '=',$course->id)
                              ->where('courses.subject_id', '=',$subjectid)
                              ->where('users.id','=',$rapor->user_id)
                              ->count('presences.id');
        $data['alpha'] = User::join('presences', 'presences.student_user_id', '=', 'users.id')
                               ->join('course_modules', 'course_modules.id', '=', 'presences.coursemodule_id')
                               ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                               ->where('presences.status','=', 'A')
                               ->where('courses.id', '=',$course->id)
                               ->where('courses.subject_id', '=',$subjectid)
                               ->where('users.id','=',$rapor->user_id)
                               ->count('presences.id');
        $data['teacher'] = Course::join('users', 'users.id', '=', 'courses.teacher_id')
                                  ->where('courses.id', '=',$course->id)
                                  ->where('courses.subject_id','=',$subjectid)
                                  ->get('users.fullname');
        $data['userdata'] = TaskFileUser::select('users.username','users.fullname')
                                          ->join('users', 'users.id', '=', 'task_file_users.user_id')
                                          ->where('users.id','=',$rapor->user_id)
                                          ->get();
        $data['groupuser'] = GroupUser::select('groups.classes','users.fullname')
                                        ->join('groups', 'group_users.group_id', '=', 'groups.id')
                                        ->join('users', 'users.id', '=', 'groups.mainteacher')
                                        ->where('user_id','=',$rapor->user_id)
                                        ->get();
        $data['courseuser'] = CourseUser::select('subjects.name', 'users.fullname')
                                          ->join('courses', 'course_users.course_id', '=', 'courses.id')
                                          ->join('users', 'users.id', '=', 'courses.teacher_id')
                                          ->join('course_modules', 'course_modules.course_id', '=', 'courses.id')
                                          ->join('subjects', 'subjects.id', '=', 'courses.subject_id')
                                        //   ->where('subjects.id','=',$subject->id)
                                          ->where('user_id','=',$rapor->user_id)->get();
        $data['authuser']=Auth::user()->role;
        //  dd($data['uas']);
        return Inertia::render('Rapor/Edit', $data);
    }

    public function editrapor(Rapor $rapor)
    {
        $data['rapor'] = $rapor;
        $userid = User::where('fullname', $rapor->nama)->pluck('id')->first();
        $subjectid = Course::where('id', $rapor->course_id)->pluck('subject_id')->first();
        $groupdata = Course::join('groups','groups.id','=','courses.group_id')->where('courses.id',$rapor->course_id)->first();
        $subjectdata = Subject::where('id',$subjectid)->first();
        $periode = $groupdata['academicterms'] == 1 ? 'GANJIL' : 'GENAP';
        $data['groupinfo'] = $groupdata['classes'].' ('.$periode.' / '.$groupdata['year'].')';
        $data['subjectinfo'] = $subjectdata['name'].' ('.$subjectdata['subject_code'].')';
        // dd($tes);

        $data['tugas'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                       ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                       ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                       ->whereIN('tasks.task_type', ['Tugas', 'UH'])
                                       ->where('courses.id', '=',$rapor->course_id)
                                       ->where('courses.subject_id', '=',$subjectid)
                                       ->where('task_file_users.user_id', '=',$userid)
                                       ->sum('mark');
        $data['bagitugas'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                          ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                          ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                          ->whereIN('tasks.task_type', ['Tugas', 'UH'])
                                          ->where('courses.id', '=',$rapor->course_id)
                                          ->where('courses.subject_id', '=',$subjectid)
                                          ->where('task_file_users.user_id', '=',$userid)
                                          ->count('mark');
        $data['uts'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'UTS')
                                     ->where('courses.id', '=',$rapor->course_id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$userid)
                                     ->sum('mark');
        $countUts = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'UTS')
                                     ->where('courses.id', '=',$rapor->course_id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$userid)
                                     ->count('mark');
        $data['uts'] = ($countUts != 0) ? $data['uts'] / $countUts : 0;
        $data['uas'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'UAS')
                                     ->where('courses.id', '=',$rapor->course_id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$userid)
                                     ->sum('mark');
        $countUas = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'UAS')
                                     ->where('courses.id', '=',$rapor->course_id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$userid)
                                     ->count('mark');
        $data['uas'] = ($countUas != 0) ? $data['uas'] / $countUas : 0;
        $data['perform'] = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'Perform')
                                     ->where('courses.id', '=',$rapor->course_id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$userid)
                                     ->sum('mark');
        $countPerform = TaskFileUser::join('tasks', 'tasks.id', '=', 'task_file_users.task_id')
                                     ->join('course_modules', 'course_modules.id', '=', 'tasks.course_module_id')
                                     ->join('courses', 'courses.id', '=', 'course_modules.course_id')
                                     ->where('tasks.task_type', '=', 'Perform')
                                     ->where('courses.id', '=',$rapor->course_id)
                                     ->where('courses.subject_id', '=',$subjectid)
                                     ->where('task_file_users.user_id', '=',$userid)
                                     ->count('mark');
        $data['perform'] = ($countPerform != 0) ? $data['perform'] / $countPerform : 0;
        // dd($data['tugas'], $data['bagitugas'], $data['uts'], $data['uas']);
        return Inertia::render('Rapor/Editrapor', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TaskFileUser  $taskfileuser
     * @return \Illuminate\Http\Response
     */
    public function updaterapor(\Illuminate\Http\Request $request, Rapor $rapor)
    {
        // dd($request, $rapor->course_id);
        $kategori = Course::where('id', $rapor->course_id)->pluck('category_id')->first();
        $tugas = Persentase::where('category_id', $kategori)->where('task_type', 'tugas')->pluck('persen')->first();
        $uts = Persentase::where('category_id', $kategori)->where('task_type', 'uts')->pluck('persen')->first();
        $uas = Persentase::where('category_id', $kategori)->where('task_type', 'uas')->pluck('persen')->first();
        $perform = Persentase::where('category_id', $kategori)->where('task_type', 'perform')->pluck('persen')->first();
        $sakit = Persentase::where('category_id', $kategori)->where('task_type', 'sakit')->pluck('persen')->first();
        $izin = Persentase::where('category_id', $kategori)->where('task_type', 'izin')->pluck('persen')->first();
        $alpha = Persentase::where('category_id', $kategori)->where('task_type', 'alpha')->pluck('persen')->first();
       

        if ($request->uas != 0){
            $nilai = ((($request->uts*($uts/100)) + ($request->tugas*($tugas/100)) + ($request->uas*($uas/100)) + ($request->perform*($perform/100))) - (($request->sakit*($sakit/100)) + ($request->izin*($izin/100)) + ($request->alpha*($alpha/100))));
            // dd($nilai);
        }else{
            $nilai = 0;
        }
        // dd($nilai);
        $level = Group::where('classes', '=',$request->kelas)->pluck('level_id');
        $huruf = Interval::where('minmark', '<=', $nilai)
                           ->where('maxmark', '>=', $nilai)
                           ->where('level_id', '=', $level)
                           ->pluck('alphabet')->first();
        // dd($huruf);
        $rapor->update(
            array_merge(
                Request::validate([
                    'nim' => ['required'],
                    'nama' => ['required'],
                    'kelas' => ['required'],
                    'walikelas' => ['required'],
                    'subject' => ['required'],
                    'gurupengajar' => ['required'],
                    'tugas' => ['required'],
                    'uts' => ['required'],
                    'uas' => ['required'],
                    'sakit' => ['required'],
                    'izin' => ['required'],
                    'alpha' => ['required'],
                    'perform' => ['required'],
                ]),
                ['updated_by' => Auth::id(), 'nilai' => $nilai, 'huruf' => $huruf]
            )
        );
        // dd($request);
        return Redirect::back()->with('success', 'Report updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rapor  $rapor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rapor $rapor)
    {
        $rapor->delete();
        return Redirect::route('rapor.index')->with('success', 'Report deleted');
    }

}
