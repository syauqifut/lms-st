<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use App\ImportLog;
use App\Organization;
use App\Level;
use App\Category;
use App\GroupUser;
use App\UserAccess;
use Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
// use Illuminate\Http\Request;
use Excel;
use App\Imports\GroupsImport;
use App\Exports\GroupsExport;
use App\Exports\MasterExport;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::user()->role === 2)
            return redirect('group-users-student');

        return Inertia::render('Groups/Index', [
            'filters' => Request::all('search', 'trashed'),
            'groups' => Group::filter(Request::only('search', 'trashed'))
                ->isactive()
                // ->when(user_teacher(), function ($query) {
                //     $query->where('mainteacher', Auth::id());
                // })
                ->paginate()
                ->transform(function ($group) {
                    return [
                        'id' => $group->id,
                        'classes' => $group->classes,
                        'huruf' => $group->huruf,
                        'kel_kelas' => $group->kel_kelas,
                        'level' => $group->level,
                        'year' => $group->year,
                        'academicterms' => $group->academicterms,
                        'mainteacherr' => $group->mainteacherr ? $group->mainteacherr : null,
                        'code' => $group->code,
                    ];
                }),
        ]);
    }

    public function groups_non_active(){
        if (Auth::user()->role === 2)
            return redirect('group-users-student');

        return Inertia::render('Groups/NoActive', [
            'filters' => Request::all('search', 'trashed'),
            'groups' => Group::filter(Request::only('search', 'trashed'))
                ->whereIn('is_active', [0, 2])
                // ->when(user_teacher(), function ($query) {
                //     $query->where('mainteacher', Auth::id());
                // })
                ->paginate()
                ->transform(function ($group) {
                    return [
                        'id' => $group->id,
                        'classes' => $group->classes,
                        'huruf' => $group->huruf,
                        'kel_kelas' => $group->kel_kelas,
                        'level' => $group->level,
                        'year' => $group->year,
                        'academicterms' => $group->academicterms,
                        'mainteacherr' => $group->mainteacherr ? $group->mainteacherr : null,
                        'code' => $group->code,
                    ];
                }),
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roleAdmin = [1,4];
        if (!in_array(Auth::user()->usertype_id, $roleAdmin)) {
            return redirect()->route('groups')->withErrors('admin only');
        }

        $users = $this->getTeachers();
        $levels = Level::where('title', '=', 'STAI AL-FITHRAH')->get();
        $groups = Group::isactive()->get();
        $listCategoryId = Category::whereNotNull('parent_category_id')
        ->distinct()
        ->pluck('parent_category_id');

        $category = Category::whereNotIn('id', $listCategoryId)->get();
        return Inertia::render('Groups/Create', [
            'users' => $users,
            'levels' => $levels,
            'groups' => $groups,
            'categories' => $category
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(\Illuminate\Http\Request $request)
    {

        $arGroup = Request::validate([
            'classes' => ['required'],
            'year' => ['required'],
            'academicterms' => ['required'],
            'mainteacher' => ['required'],
            'huruf' => ['nullable'],
            'kel_kelas' => ['nullable'],
            'level_id' => ['required'],
            'category_id' => ['required'],
        ]);
        $arGroup['is_active'] = 1;
        $code = $this->getKode(15);
        while (Group::where('code', $code)->count() > 0) {
            $code = $this->getKode(15);
        }
        $arGroup['code'] = $code;
        $arGroup['createdBy'] = Auth::id();
//   dd($arGroup);
        $groups = Group::where([['classes', $arGroup['classes']], ['huruf', $arGroup['huruf'],['kel_kelas', $arGroup['kel_kelas']]] ,['year', $arGroup['year']], ['academicterms', $arGroup['academicterms']]])->first();
        if ($groups)
            return Redirect::back()->with('error', 'Group already exists');
      
        $group = Group::create($arGroup);
        if ($group) {
            $arTeacher['created_by']    = Auth::id();
            $arTeacher['group_id']      = $group->id;
            $arTeacher['user_id']       = $request->mainteacher;
            $arTeacher['is_active']     = 1;
            GroupUser::create($arTeacher);

            // MASUKIN COPY USER MURID DARI GROUP LAMA DISINI
            if ($request->group_id != null) {
                $groupCopy = Group::with(['groupUsers.student'])
                ->whereHas('groupUsers.student')
                ->findOrFail($request->group_id);

                // $arrStudent = [];
                foreach($groupCopy->groupUsers as $user) {
                    if ($user->student != null) {
                        // array_push($arrStudent, $user->student);
                        $arrStudent['created_by'] = Auth::id();
                        $arrStudent['group_id'] = $group->id;
                        $arrStudent['user_id'] = $user->student->id;
                        $arrStudent['is_active'] = 1;
                        GroupUser::create($arrStudent);
                    }
                }
                // dd($arrStudent);
            }
        }
        if (Request::input('addAgain')) {
            return Redirect::route('groups.create')->with('success', 'Group created.');
        }
        return Redirect::route('groups')->with('success', 'Groups created.');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Groups  $groups
     * @return \Illuminate\Http\Response
     */
    public function show(Groups $groups)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Groups  $groups
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $roleAdmin = [1,4];
        if (!in_array(Auth::user()->usertype_id, $roleAdmin)) {
            return redirect()->route('groups')->withErrors('admin only');
        }
        $listCategoryId = Category::whereNotNull('parent_category_id')
        ->distinct()
        ->pluck('parent_category_id');

        $category = Category::whereNotIn('id', $listCategoryId)->get();

        $organization = Organization::find($group->id);
        return Inertia::render('Groups/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'address' => $organization->address,
                'city' => $organization->city,
                'region' => $organization->region,
                'country' => $organization->country,
                'postal_code' => $organization->postal_code,
                'deleted_at' => $organization->deleted_at,
                'contacts' => $organization->contacts()->orderByName()->get()->map->only('id', 'name', 'city', 'phone'),
            ],
            'group' => $group->toArray(),
            'users' => $this->getTeachers(),
            'levels'=> Level::where('title', '=', 'STAI AL-FITHRAH')->get(),
            'categories' => $category,
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Groups  $groups
     * @return \Illuminate\Http\Response
     */
    public function update(Group $group)
    {
        $group->update(
            array_merge(Request::validate([
                'classes' => ['required'],
                'year' => ['required'],
                'academicterms' => ['required'],
                'mainteacher' => ['required'],
                'huruf' => ['nullable'],
                'kel_kelas' => ['nullable'],
                'level_id' => ['required'],
                'is_active' => ['required'],
                'category_id'=>['required'],
            ], ['updatedBy' => Auth::id()]))
        );

        return Redirect::back()->with('success', 'Group updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Groups  $groups
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->is_active = 0;
        $group->save();
        return Redirect::back()->with('success', 'Group deleted.');
    }

    public function restore(Group $group)
    {
        $group->is_active = 1;
        $group->save();

        return Redirect::back()->with('success', 'Group restored.');
    }

    private function getTeachers()
    {
        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $USERTYPE_TEACHER = 3;
        return Auth::user()->account->users()
            ->where('usertype_id', $USERTYPE_TEACHER)
            ->whereIn('id', $access)
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

    public function import()
    {
        return Inertia::render('Groups/Import');
    }

    public function export()
    {
        return Excel::download(new GroupsExport, 'groups.xlsx');
    }
    public function exportmaster()
    {
        return Excel::download(new MasterExport, 'master.xlsx');
    }

    public function processImport()
    {
        Request::validate([
            'data' => ['required', 'file'],
        ]);
        $path1 = Request::file('data')->store('temp');
        $path = storage_path('app').'/'.$path1;
        $result = Excel::import(new GroupsImport, Request::file('data'));
        // dd($path);

        importLog::create([
            'name' => 'Import Groups',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Successfully import Groups');
    }

    public function template()
    {
        return (Response::download(public_path('asset/sample/template_input_groups.xlsx')));
    }
}
