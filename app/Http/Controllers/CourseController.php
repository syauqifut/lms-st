<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseUser;
use App\Subject;
use App\Category;
use App\GroupUser;
use App\Level;
use App\User;
use App\Group;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;
use App\ImportLog;
use App\UserAccess;
use Excel;
use App\Imports\CoursesImport;
use App\Imports\CourseUserImport;
use App\Imports\CourseModuleImport;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['filters'] = Request::all('search');
        $data['courses'] =  Course::filter(Request::only('search'))
            ->with(['group'])
            ->isActive()
            ->orderBy('title')
            ->when(user_student(), function ($query) {
                $query->whereHas('course_user', function ($query) {
                    $query->where('user_id', Auth::id());
                    $query->where('is_active', 1);
                });
            })
            ->when(user_teacher(), function ($query) {
                $query->whereHas('course_user', function ($query) {
                    $query->where('user_id', Auth::id());
                    $query->where('is_active', 1);
                })
                    // ->orWhere('teacher_id', Auth::id())
                ;
            })
            ->paginate()
            ->transform(function ($course) {
                $periode =  $course->group->academicterms == 1 ? 'GANJIL' : 'GENAP';
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
                    'course' => $course->course_user->where('user_id', Auth::id())->first(),
                    'treviews' => $course->total_reviews,
                    'avgreviews' => $course->average_reviews,
                    'coursedone' => $course->course_modules->whereNotNull('actual_end_at')->count(),
                    'periode' => $periode,
                ];
            });

        return Inertia::render('Courses/Index', $data); 
    }

    public function courses_non_active(){
        $data['filters'] = Request::all('search');
        $data['courses'] =  Course::filter(Request::only('search'))
            ->with(['group'])
            ->whereIn('is_active', [0, 2])
            ->orderBy('title')
            ->when(user_student(), function ($query) {
                $query->whereHas('course_user', function ($query) {
                    $query->where('user_id', Auth::id());
                    $query->where('is_active', 1);
                });
            })
            ->when(user_teacher(), function ($query) {
                $query->whereHas('course_user', function ($query) {
                    $query->where('user_id', Auth::id());
                    $query->where('is_active', 1);
                })
                    // ->orWhere('teacher_id', Auth::id())
                ;
            })
            ->paginate()
            ->transform(function ($course) {
                $periode =  $course->group->academicterms == 1 ? 'GANJIL' : 'GENAP';
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
                    'course' => $course->course_user->where('user_id', Auth::id())->first(),
                    'treviews' => $course->total_reviews,
                    'avgreviews' => $course->average_reviews,
                    'coursedone' => $course->course_modules->whereNotNull('actual_end_at')->count(),
                    'periode' => $periode,
                ];
            });

        return Inertia::render('Courses/NonActive', $data);
    }

    public function course_users()
    {
        $data['filters'] = Request::all('search');
        $data['courses'] =  Course::filter(Request::only('search'))
            ->when(user_teacher(), function ($query) {
                $query->whereHas('course_user', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                    ->orWhere('teacher_id', Auth::id());
            })
            ->orderBy('title')
            ->paginate()
            ->transform(function ($course) {
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
                ];
            });

        return Inertia::render('CourseUsers/Index', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['subjects'] = Subject::where('is_active', 1)->get()
            ->transform(function ($subject) {
                return [
                    'id' => $subject->id,
                    'fullname' => $subject->name . ' (' . $subject->subject_code . ')'
                ];
            });

        $listCategoryId = Category::whereNotNull('parent_category_id')
            ->distinct()
            ->pluck('parent_category_id');

        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $data['categories'] = Category::whereNotIn('id', $listCategoryId)->get();
        // dd($data['categories']);
        $data['levels'] = Level::all();
        $data['groups'] = Group::where('is_active', 1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'fullname' => $group->classes . " " . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')',
                ];
            });
        $data['teachers'] = User::where('usertype_id', 3)->whereIn('id', $access)->get();



        return Inertia::render('Courses/Create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(\Illuminate\Http\Request $request)
    {
        // dd($request->all());
        $rules = [
            'title' => ['required', 'max:255'],
            'description' => ['required'],
            'subject_id' => ['required'],
            'category_id' => ['required'],
            'level_id' => ['required'],
            'photo' => ['nullable', 'image'],
            'group_id' => ['required'],
            'teacher_id' => ['required'],
        ];
        $messages = [
            'title.required' => 'Judul course tidak boleh kosong',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'subject_id.required' => 'Subject tidak boleh kosong',
            'category_id.required' => 'Kategori tidak boleh kosong',
            'level_id.required' => 'Level tidak boleh kosong',
            'photo.image' => 'Format selain foto tidak boleh.',
            'group_id.required' => 'Group tidak boleh kosong',
            'teacher_id.required' => 'Guru tidak boleh kosong',
        ];
        $request->validate($rules, $messages);

        $dolar = Course::where('group_id', '=', $request->group_id)->where('subject_id', '=', $request->subject_id)->exists();

        if ($dolar) {
            return Redirect::back()->with('error', 'Course dengan Group dan Subject tersebut telah ada');
        } else {

            $course = new Course;
            $course->title = $request->title;
            $course->description = $request->description;
            $course->photo = $request->hasFile('photo') ? $course->setPhoto($request->photo) : null;
            $course->subject_id = $request->subject_id;
            $course->category_id = $request->category_id;
            $course->level_id = $request->level_id;
            $course->is_active = $request->is_active;
            $course->created_by = Auth::id();
            $course->updated_by = Auth::id();
            do {
                $join_code = Str::random(12);
            } while (Course::where('join_code', $join_code)->first());
            $course->join_code = $join_code;
            $course->group_id = $request->group_id;
            $course->teacher_id = $request->teacher_id;


            if ($course->save()) {

                //dd($course->id);
                /*
                $courseUser = new CourseUser;

                $courseUser->created_by = Auth::id();
                $courseUser->updated_by = Auth::id();
                $courseUser->is_active  = '1';
                $courseUser->user_id    = $request->teacher_id;
                $courseUser->course_id  = $course->id;
                $courseUser->save();
                */

                $this->insertUserStudentByCourseGroup($request->group_id, $course->id, $request->teacher_id);

                if ($request->addAgain) {
                    return Redirect::route('courses.create')->with('success', 'Course berhasil dibuat.');
                }
                return Redirect::route('courses.index')->with('success', 'Course berhasil dibuat.');
            }
        }
    }

    function insertUserStudentByCourseGroup($groupId, $courseId, $teacherID)
    {

        $dataUserGroups   = GroupUser::where('group_id', $groupId)
            ->where('is_active', 1)
            ->get();

        //dd($dataUserGroups);
        foreach ($dataUserGroups as $dataUserGroup) {
            if ($teacherID <> $dataUserGroup->user_id) {
                $courseUser = new CourseUser;

                $courseUser->created_by = Auth::id();
                $courseUser->updated_by = Auth::id();
                $courseUser->is_active  = '1';
                $courseUser->user_id    = $dataUserGroup->user_id;
                $courseUser->course_id  = $courseId;

                $courseUser->save();
            }
        }
        $courseUser = new CourseUser;

        $courseUser->created_by = Auth::id();
        $courseUser->updated_by = Auth::id();
        $courseUser->is_active  = '1';
        $courseUser->user_id    = $teacherID;
        $courseUser->course_id  = $courseId;

        $courseUser->save();
    }

    public function import()
    {
        return Inertia::render('Courses/Import');
    }

    public function processImport()
    {
        Request::validate([
            'data' => ['required', 'file'],
        ]);
        $path1 = Request::file('data')->store('temp');
        $path = storage_path('app') . '/' . $path1;
        $result = Excel::import(new CoursesImport, Request::file('data'));
        importLog::create([
            'name' => 'Import Courses',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Berhasil import Courses ');
    }

    public function template()
    {
        return (Response::download(public_path('asset/sample/template_input_courses.xlsx')));
    }

    public function import_course_users()
    {
        return Inertia::render('CourseUsers/Import');
    }

    public function processImport_course_users()
    {
        Request::validate([
            'data' => ['required', 'file'],
        ]);
        $path1 = Request::file('data')->store('temp');
        $path = storage_path('app') . '/' . $path1;
        $result = Excel::import(new CourseUserImport, Request::file('data'));
        importLog::create([
            'name' => 'Import Course Users',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Berhasil import Course Users ');
    }

    public function template_course_users()
    {
        return (Response::download(public_path('asset/sample/template_input_course-users.xlsx')));
    }

    public function import_course_modules(Course $course)
    {
        $data['course'] = $course;
        return Inertia::render('CourseModules/Import', $data);
    }

    public function processImport_course_modules()
    {
        Request::validate([
            'data' => ['required', 'file'],
        ]);
        $path1 = Request::file('data')->store('temp');
        $path = storage_path('app') . '/' . $path1;
        $result = Excel::import(new CourseModuleImport, Request::file('data'));
        importLog::create([
            'name' => 'Import Course Module',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Berhasil import Course Module ');
    }

    public function template_course_modules()
    {
        return (Response::download(public_path('asset/sample/template_input_course-modules.xlsx')));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        $listUsers      = CourseUser::select('course_users.id', 'course_users.user_id', 'course_users.is_active', 'users.fullname', 'users.email')
            ->where('course_id', $course->id)->with('user')->whereHas('user', function ($query) {
                $query->where('usertype_id', 2);
            })
            ->join('users', 'users.id', '=', 'course_users.user_id')
            ->orderBy('users.fullname', 'asc')
            ->orderBy('course_users.is_active', 'desc')

            ->get()
            ->transform(function ($course_user) {
                return [
                    'id' => $course_user->id,
                    'user_id' => $course_user->user_id,
                    'is_active' => $course_user->is_active,
                    'fullname' => $course_user->user->fullname,
                    'email' => $course_user->user->email,
                ];
            });

        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $listGurus      = CourseUser::where('course_id', $course->id)->with('user')->whereHas('user', function ($query) {
            $query->where('usertype_id', 3);
        })
            // ->where('is_active', 1)
            ->orderBy('is_active', 'desc')
            ->get()
            ->transform(function ($course_user) {
                return [
                    'id' => $course_user->id,
                    'user_id' => $course_user->user_id,
                    'is_active' => $course_user->is_active,
                    'fullname' => $course_user->user->fullname,
                    'email' => $course_user->user->email,
                ];
            });

        $group = $course->group;
        $groupId =  $group->id;

        $userForSelectOptionA = User::with('usertype')
            ->whereNOTIn('id', function ($query) use ($course) {
                $query->select('user_id')->from('course_users')->where('course_users.course_id', '=', $course->id);
            })
            ->whereHas('group_users', function ($query) use ($course) {
                $query->where('group_id', $course->group_id);
            })
            ->whereIn('users.id', $access)
            ->whereIn('users.usertype_id', [2]);

        $userForSelectOptionB = User::with('usertype')
            ->whereNOTIn('id', function ($query) use ($course) {
                $query->select('user_id')->from('course_users')->where('course_users.course_id', '=', $course->id);
            })
            ->whereIn('users.usertype_id', [3])
            ->whereIn('users.id', $access)
            ->union($userForSelectOptionA)
            ->get()
            ->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'fullname' => $user->fullname . ' - ' . $user->usertype->name,
                    'email' => $user->email,
                ];
            });
        $courseTeacher = $course->teacher;
        $teacher    = User::where('id', $group->mainteacher)->first();

        return Inertia::render('CourseUsers/Show', [
            'users_in_group'    => $listUsers,
            'user_for_select_option'    => $userForSelectOptionB,
            'group'             => $group->toArray(),
            'teacher'           => $teacher,
            'course_teacher'    => $courseTeacher,
            'gurus'             => $listGurus,
            'course'            => $course,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        $data['course'] = $course;
        //$data['subjects'] = Subject::all();
        $data['subjects'] = Subject::where('is_active', 1)->get()
            ->transform(function ($subject) {
                return [
                    'id' => $subject->id,
                    'fullname' => $subject->name . ' (' . $subject->subject_code . ')'
                ];
            });

        $listCategoryId = Category::whereNotNull('parent_category_id')
            ->distinct()
            ->pluck('parent_category_id');

        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $data['categories'] = Category::whereNotIn('id', $listCategoryId)->get();

        $data['levels'] = Level::all();
        $data['groups'] = Group::where('is_active', 1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'fullname' => $group->classes . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')'
                ];
            });
        $data['teachers'] = User::where('usertype_id', 3)->whereIn('id', $access)->get();

        return Inertia::render('Courses/Edit', $data);
    }

    public function course_users_delete(Course $course, User $user = null)
    {
        if (!$user) $user = Auth::user();
        $course_user = CourseUser::where('course_id', $course->id)->where('user_id', $user->id)->first();
        $course_user->is_active = -2;
        $course_user->save();
        // dd($course_user);
        return Redirect::back()->with('success', "Sukses Menghapus user dari course ini");
    }

    public function course_users_activated(Course $course, User $user = null)
    {
        if (!$user) $user = Auth::user();
        $course_user = CourseUser::where('course_id', $course->id)->where('user_id', $user->id)->first();
        $course_user->is_active = 1;
        $course_user->save();
        return Redirect::back()->with('success', "Sukses Mengaktifkan User");
    }

    public function course_users_store()
    {
        $course_user = Request::validate([
            'user_id' => ['required'],
            'course_id' => ['required'],
        ]);

        CourseUser::create(array_merge($course_user, ['created_by' => Auth::id(), 'updated_by' => Auth::id(), 'is_active' => 1]));

        return Redirect::back()->with('success', 'User berhasil ditambahkan.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(\Illuminate\Http\Request $request, Course $course)
    {
        // dd([$request->all(), $course]);

        $rules = [
            'title' => ['required', 'max:255'],
            'description' => ['required'],
            'subject_id' => ['required'],
            'category_id' => ['required'],
            'level_id' => ['required'],
            'group_id' => ['required'],
            'teacher_id' => ['required'],
            'photo' => ['nullable', 'image'],
        ];
        $messages = [
            'title.required' => 'Judul course tidak boleh kosong',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'subject_id.required' => 'Subject tidak boleh kosong',
            'category_id.required' => 'Kategori tidak boleh kosong',
            'level_id.required' => 'Level tidak boleh kosong',
            'group_id.required' => 'Group tidak boleh kosong',
            'teacher_id.required' => 'Guru tidak boleh kosong',
            'photo.image' => 'Format selain foto tidak boleh.',
        ];
        $request->validate($rules, $messages);

        if ($request->hasFile('photo')) {
            if (!empty($course->photo)) {
                $path = public_path('images/courses/' . $course->photo);
                unlink($path);
            }
            $newPhoto = $course->setPhoto($request->photo);
        }

        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'photo' => $request->hasFile('photo') ? $newPhoto : $course->photo,
            'subject_id' => $request->subject_id,
            'category_id' => $request->category_id,
            'level_id' => $request->level_id,
            'group_id' => $request->group_id,
            'teacher_id' => $request->teacher_id,
            'is_active' => $request->is_active,
            'updated_by' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Course berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $course->is_active = 2;
        $course->save();

        return Redirect::back()->with('success', 'Course berhasil dihapus');
    }

    public function restore(Course $course)
    {
        $course->is_active = 1;
        $course->save();

        return Redirect::back()->with('success', 'Course berhasil di-restore');
    }

    public function list_courses()
    {
        $data['filters'] = Request::all('search');
        $data['courses'] =  Course::filter(Request::only('search'))
        ->with(['group'])
        ->isActive()    
        ->orderBy('title')
            // ->whereDoesntHave('course_user', function ($query) {
            //     $query->where('user_id', Auth::id());
            //     $query->whereIn('is_active', [0,1]);
            // })
            ->whereHas('course_user', function ($query) {
                //$query->where('user_id', Auth::id());
                $query->where('is_active', 1);
            })
            //->where('is_active', '1')
            ->paginate()
            ->transform(function ($course) {
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
                ];
            });

        return Inertia::render('Students/list_courses', $data);
    }

    public function list_approve_course_student()
    {
        $course_users = CourseUser::filter(Request::only('search', 'trashed'))
            ->with('course.group')->with('user')
            ->when(user_teacher(), function ($query) {
                $query->whereHas('course', function ($query) {
                    $query->where('teacher_id', Auth::id());
                });
            })
            ->where('is_active', 0)->get()->toArray();

        return Inertia::render('CourseUsers/list-approve', [
            'filters' => Request::all('search', 'trashed'),
            'courses' => $course_users,
            'menuName' => 'Course'
        ]);
    }

    public function approve_course_student(CourseUser $course)
    {
        $course->is_active = 1;
        $course->approved_by = Auth::id();
        $course->save();
        return Redirect::back()->with('success', 'Berhasil melakukan approve');
    }

    public function decline_course_student(CourseUser $course)
    {
        $course->is_active = -1;
        $course->save();
        return Redirect::back()->with('success', 'Berhasil melakukan penolakan');
    }

    public function students_join_course()
    {
        $request = (Request::validate(
            [
                'course_id' => 'required',
                'code' => 'required'
            ]
        ));
        $course = Course::find($request['course_id']);
        if ($course->join_code == ($request['code'])) {
            CourseUser::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'user_id' => Auth::id()
                ],
                [
                    'created_by' => Auth::id(),
                    'is_active' => 0,
                ]
            );
            return Redirect::back()->with('success', 'Berhasil mengajukan permintaan join group, menunggu validasi dari admin / guru');
        } else
            return Redirect::back()->with('error', 'Kode group kelas salah, silahkan hubungi guru terkait');
    }
}
