<?php

namespace App\Http\Controllers;
// use Illuminate\Http\Request;

use App\Exports\GroupUsersExport;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\GroupUser;
use App\User;
use App\UserAccess;
use App\Organization;
use App\Group;
use Auth;

use Illuminate\Support\Facades\Response;
use App\ImportLog;
use Excel;
use App\Imports\GroupUsersImport;

class GroupUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $menuName = "Group User";

    public function index()
    {
        return Inertia::render('GroupsUsers/Index', [
            'filters' => Request::all('search', 'trashed'),
            'groups' => Group::filter(Request::only('search', 'trashed'))
                ->isactive()
                ->when(user_teacher(), function ($query) {
                    $query->where('mainteacher', Auth::id());
                })
                ->paginate()
                ->transform(function ($group) {
                    return [
                        'id' => $group->id,
                        'classes' => $group->classes,
                        'year' => $group->year,
                        'academicterms' => $group->academicterms,
                        'mainteacherr' => $group->mainteacherr ? $group->mainteacherr : null,
                        'code' => $group->code,
                    ];
                }),
            'menuName' => $this->menuName
        ]);
    }

    public function list_approve()
    {
        $group_users = GroupUser::filter(Request::only('search', 'trashed'))
            ->with('group')->with('user')->whereHas('group', function ($query) {
                $query->where('mainteacher', Auth::id());
            })->where('is_active', 0)->get()->toArray();
        return Inertia::render('GroupsUsers/list-approve', [
            'filters' => Request::all('search', 'trashed'),
            'groups' => $group_users,
            'menuName' => $this->menuName
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $rules = [
            'user_id' => ['required'],
            'group_id' => ['required'],
        ];
        $messages = [
            'user_id.required' => 'User cannot be empty',
            'group_id.required' => 'Group cannot be empty',
        ];
        $request->validate($rules, $messages);

        $user = User::find($request->user_id);
        $group = $request->group_id; 
        $userExist = GroupUser::where('user_id', $user->id)->where('group_id', $group)->first();
        // dd($group, $user, $userExist);
        if ($userExist) {
            $userExist->update(['is_active' => 1]);
        } else {
            $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id(), 'is_active' => 1]);
            GroupUser::create($request->all());
        }

        return Redirect::route('group_users.show', $request->group_id)->with('success', $user->fullname . ' added successfully');
    }

    public function import()
    {
        return Inertia::render('GroupsUsers/Import');
    }

    public function processImport()
    {
        Request::validate([
            'data' => ['required', 'file'],
        ]);
        $path1 = Request::file('data')->store('temp');
        $path = storage_path('app') . '/' . $path1;
        $result = Excel::import(new GroupUsersImport, Request::file('data'));
        importLog::create([
            'name' => 'Import Group Users',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Successfully import Group Users ');
    }

    public function template()
    {
        return (Response::download(public_path('asset/sample/template_input_group-users.xlsx')));
    }

    public function exportExcel($groupId)
    {
        $group = Group::findOrFail($groupId);
        return Excel::download(new GroupUsersExport($groupId), 'group user - ' . $group->classes . '.xlsx');
    }

    public function show(Group $group)
    {
        $showDownloadExcel = false;
        if (in_array(Auth::user()->usertype_id, [1, 3, 4])) // 1 = Administrator, 3 = Guru, 4 = Admin
            $showDownloadExcel = true;

        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $listUsers      = GroupUser::where('group_users.group_id', $group->id)
            ->join('users', 'users.id', '=', 'group_users.user_id')
            ->select('group_users.id', 'group_users.user_id', 'group_users.is_active', 'users.fullname', 'users.email')
            ->where('group_users.is_active', '=', '1')
            ->where('users.usertype_id', '=', '2')
            ->orderBy('group_users.is_active', 'desc')
            ->orderBy('users.fullname', 'asc')
            ->get();

        $listGurus      = GroupUser::where('group_users.group_id', $group->id)
            ->join('users', 'users.id', '=', 'group_users.user_id')
            ->select('group_users.id', 'group_users.user_id', 'users.fullname', 'users.email')
            ->where('group_users.is_active', '=', '1')
            ->where('users.usertype_id', '=', '3')
            ->orderBy('users.fullname', 'asc')
            ->get();

        $groupId =  $group->id;

        $userForSelectOption = User::with('usertype')
            ->whereNOTIn('id', function ($query) use ($groupId) {
                $query->select('user_id')->from('group_users')
                    ->where('group_users.group_id', '=', $groupId)
                    ->where('group_users.is_active', 1);
            })
            ->whereIn('users.usertype_id', [2, 3])
            ->whereIn('users.id', $access)
            ->orderBy('users.usertype_id', 'desc')
            ->orderBy('users.fullname', 'asc')
            ->get()
            ->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'fullname' => $user->fullname . ' - ' . $user->usertype->name,
                    'email' => $user->email,
                ];
            });

        $teacherUserForSelectOption = User::with('usertype')
            ->whereNOTIn('id', function ($query) use ($groupId) {
                $query->select('user_id')->from('group_users')
                    ->where('group_users.group_id', '=', $groupId)
                    ->where('group_users.is_active', 1);
            })
            ->where('users.usertype_id', 2)
            ->whereIn('users.id', $access)
            ->orderBy('users.usertype_id', 'desc')
            ->orderBy('users.fullname', 'asc')
            ->get()
            ->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'fullname' => $user->fullname . ' - ' . $user->usertype->name,
                    'email' => $user->email,
                ];
            });

        $teacher    = User::where('id', $group->mainteacher)->first();

        return Inertia::render('GroupsUsers/Show', [
            'users_in_group'    => $listUsers,
            'user_for_select_option'    => $userForSelectOption,
            'teacher_user_for_select_option'    => $teacherUserForSelectOption,
            'group'             => $group->toArray(),
            'teacher'           => $teacher,
            'gurus'             => $listGurus,
            'showDownloadExcel' => $showDownloadExcel
        ]);
    }



    public function destroy($id)
    {
        $group    = GroupUser::where('group_users.id', $id)
            ->join('users', 'users.id', '=', 'group_users.user_id')
            ->select('group_users.id', 'group_users.group_id', 'users.fullname', 'users.email')
            ->first();

        //var_dump($group );exit();
        $group->is_active = -2;
        $group->save();
        // GroupUser::where('id',$id)->delete();

        return Redirect::route('group_users.show', $group->group_id)->with('success', $group->fullname . ' deleted from group.');
    }



    private function getTeacher($id)
    {
        return Auth::user()->account->users()
            ->where('id', $id)
            ->orderByName()
            ->filter(Request::only('search', 'role', 'trashed'))
            ->first()
            ->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'owner' => $user->owner,
                    'photo' => $user->photoUrl(['w' => 40, 'h' => 40, 'fit' => 'crop']),
                    'deleted_at' => $user->deleted_at,
                ];
            });
    }

    public function getKode($n)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    public function list_group()
    {
        return Inertia::render('Students/list_group', [
            'filters' => Request::all(),
            'groups' => Group::filter(Request::only('search', 'trashed', 'tahun', 'academicterms', 'mainteacher', 'classes'))
                ->isactive()
                ->whereDoesntHave('groupUsers', function ($query) {
                    $query->where('user_id', Auth::id());
                    $query->whereIn('is_active', [0, 1]);
                })
                ->paginate()
                ->transform(function ($group) {
                    return [
                        'id' => $group->id,
                        'classes' => $group->classes,
                        'huruf' => $group->huruf,
                        'kel_kelas' => $group->kel_kelas,
                        'year' => $group->year,
                        'academicterms' => $group->academicterms,
                        'mainteacherr' => $group->mainteacherr ? $group->mainteacherr : null,
                        'total_peserta' => $group->groupUsers->where('is_active', 1)->count(),
                    ];
                }),
            'listTeachers' => $this->getTeachers(),
            'menuName' => 'Group User'
        ]);
    }

    public function student_groups()
    {
        return Inertia::render('Students/list_group_join', [
            'filters' => Request::all(),
            'groups' => Group::filter(Request::only('search', 'trashed', 'tahun', 'academicterms', 'mainteacher', 'classes'))
                ->isactive()
                ->whereHas('groupUsers', function ($query) {
                    $query->where('user_id', Auth::id());
                    // $query->where('is_active', 1);
                })
                ->paginate()
                ->transform(function ($group) {
                    return [
                        'id' => $group->id,
                        'classes' => $group->classes,
                        'huruf' => $group->huruf,
                        'kel_kelas' => $group->kel_kelas,
                        'year' => $group->year,
                        'academicterms' => $group->academicterms,
                        'mainteacherr' => $group->mainteacherr ? $group->mainteacherr : null,
                        'total_peserta' => $group->groupUsers->where('is_active', 1)->count(),
                        'status' => $group->groupUsers->where('user_id', Auth::id())->first()->is_active,
                    ];
                }),
            'listTeachers' => $this->getTeachers(),
            'menuName' => 'Group User'
        ]);
    }

    public function join_group()
    {
        $request = (Request::validate(
            [
                'group_id' => 'required',
                'code' => 'required'
            ]
        ));
        $group = Group::find($request['group_id']);
        if ($group->code == strtoupper($request['code'])) {
            // dd([
            //     'group_id' => $group->id,
            //     'user_id' => Auth::id()
            //     ]);
            GroupUser::updateOrCreate(
                [
                    'group_id' => $group->id,
                    'user_id' => Auth::id()
                ],
                [
                    'created_by' => Auth::id(),
                    'is_active' => 0,
                ]
            );
            return Redirect::back()->with('success', 'Successfully request to join the group, waiting for validation from the admin / teacher');
        } else
            return Redirect::back()->with('error', 'The class group code is wrong, please contact the relevant teacher');
    }

    private function getTeachers()
    {
        $USERTYPE_TEACHER = 3;
        return Auth::user()->account->users()
            ->where('usertype_id', $USERTYPE_TEACHER)
            ->orderByName()
            ->filter(Request::only('search', 'role', 'trashed'))
            ->get()
            ->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'owner' => $user->owner,
                    'photo' => $user->photoUrl(['w' => 40, 'h' => 40, 'fit' => 'crop']),
                    'deleted_at' => $user->deleted_at,
                ];
            });
    }

    public function student_groups_exit(Group $group)
    {
        // dd($group);
        $groupUser = GroupUser::where('group_id', $group->id)->where('user_id', Auth::id())->first();
        $groupUser->is_active = -2;
        $groupUser->save();
        return Redirect::route('student-groups')->with('success', 'Successfully leave the group');
    }

    public function approve_group_student(GroupUser $group)
    {
        $group->is_active = 1;
        $group->approved_by = Auth::id();
        $group->save();
        return Redirect::back()->with('success', 'Successfully approved');
    }

    public function decline_group_student(GroupUser $group)
    {
        $group->is_active = -1;
        $group->save();
        return Redirect::back()->with('success', 'Successfully refused');
    }
}
