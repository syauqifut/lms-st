<?php

namespace App\Http\Controllers;

use App\User;
use App\Pekerjaan;
use App\Pendidikan;
use App\Penghasilan;
use App\importLog;
use App\KodePos;
use App\Userblock;
use App\Access;
use App\UserAccess;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use App\Usertype;
use App\UserProfile;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Excel;
use App\Imports\UsersImport;
use App\Imports\UsersParentImport;
use App\Imports\UsersBlockImport;
use App\Exports\UserallExport;
use Illuminate\Support\Facades\Hash;

use App\GroupUser;
use App\CourseUser;



class UsersController extends Controller
{
    public function index()
    {
        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
                        ->where('accesses.name', '=', 'stai')
                        ->pluck('user_accesses.user_id');

        $usertypes = Usertype::where('is_active', '1')->get();
        $users = [];
        foreach ($usertypes as $key => $value) {
            $arId = ($value->users->pluck('id'));
            $usertype = $value->toArray();
            $usertype['users'] = User::whereIn('id', $arId)
                ->orderByName()
                ->filter(Request::only('search', 'role', 'trashed'))
                ->whereIn('id', $access)
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
            $users[] = $usertype;
        };
        
        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'users' => Auth::user()->account->users()
                ->orderByName()
                ->filter(Request::only('search', 'role', 'trashed'))
                ->whereIn('id', $access)
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
                }),
            'types' => $users,
        ]);
    }

    public function kodepos()
    {
        $kode = KodePos::when(Request::has('search'), function($query){
            $query->where('kecamatan', 'like', '%'.Request::input('search').'%');
            $query->orWhere('kelurahan', 'like', '%'.Request::input('search').'%');
            $query->orWhere('kabupaten', 'like', '%'.Request::input('search').'%');
            $query->orWhere('provinsi', 'like', '%'.Request::input('search').'%');
            $query->orWhere('kodepos', 'like', '%'.Request::input('search').'%');
        })
        ->get()->take(200)->transform(function($query){
            return [
                'label' => $query->provinsi. ', '. $query->kabupaten.', '. $query->kecamatan.', '.$query->kelurahan.', '. $query->kodepos,
                'id' => $query->id
            ];
        })->toArray();
        return json_encode($kode);
    }

    public function create($role_id = null, $representative = null)
    {
        //$listUsers = User::all();
        $listUsers = User::where('usertype_id', '!=', '2')->get()
                     ->transform(function($query){
                        return [
                            'id' => $query->id,
                            'fullname' => $query->fullname
                        ];
                    });
        $listTypes = Usertype::where('is_active', '1')->get();
        $role = Usertype::find($role_id);
        $kodepos = KodePos::get()->take(100)
        ->transform(function($query){
            return [
                'label' => $query->provinsi. ', '. $query->kabupaten.', '. $query->kecamatan.', '.$query->kelurahan.', '. $query->kodepos,
                'id' => $query->id
            ];
        });
        return Inertia::render('Users/Create', compact('listUsers', 'listTypes', 'role', 'representative'));
    }

    public function import()
    {
        return Inertia::render('Users/Import');
    }

    public function importparent()
    {
        return Inertia::render('Users/ImportUparent');
    }

    public function processImport()
    {
        Request::validate([
            'user_data' => ['required', 'file'],
        ]);
        $path1 = Request::file('user_data')->store('temp');
        $path = storage_path('app').'/'.$path1;
        $result = Excel::import(new UsersImport, Request::file('user_data'));
        importLog::create([
            'name' => 'Import User',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Import Users successfully');
    }

    public function processImportParent()
    {
        Request::validate([
            'user_data' => ['required', 'file'],
        ]);
        //error userparentimport model isinya null
        $path1 = Request::file('user_data')->store('temp');
        $path = storage_path('app').'/'.$path1;
        $result = Excel::import(new UsersParentImport, Request::file('user_data'));
        importLog::create([
            'name' => 'Import User',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Import Users successfully');
    }

    public function template()
    {
        return (Response::download(public_path('asset/sample/template_input_users.xlsx')));
    }

    public function templateparent()
    {
        return (Response::download(public_path('asset/sample/template_input_userparent.xlsx')));
    }

    public function store()
    {
        // dd(is_null(Request::input('representative')));
        $arData = Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['nullable', 'max:50', 'email', Rule::unique('users')],
            'username' => ['required', 'max:50', Rule::unique('users')],
            'password' => ['nullable'],
            'photo_path' => ['nullable', 'image'],
            'adress' => ['required'],
            'city' => ['required'],
            'gender' => ['required'],
            'country' => ['required'],
            'mobilephone' => ['required', 'numeric', 'digits_between:8,13'],
            'birthplace' => ['required'],
            'birthdate' => ['required'],
            'usertype_id' => ['required'],
            'parent_id' => ['nullable'],
        ]);
        // dd($arData);
        // $kodepos = KodePos::where('id', $arData['city'])->first();
        // $arData['city'] = $kodepos->kabupaten;
        // dd(Request::file('photo_path') ? Request::file('photo_path')->store('users') : 'no');

        $arData = array_merge($arData, [
            'fullname' => $arData['first_name'] . ' ' . $arData['last_name'],
            'is_active' => 1,
            'createdBy' => Auth::id(),
            'owner' => 0,
            'photo_path' => Request::file('photo_path') ? Request::file('photo_path')->store('users') : null,
        ]);
        $newUser = Auth::user()->account->users()->create($arData);
        // dd($newUser->id);

        //input to useraccess
        $accessid = Access::where('name', '=', 'stai')->pluck('id')->first();
        $userid = $newUser->id; 
        
        $useraccess = new UserAccess;
        $useraccess->access_id = $accessid;
        $useraccess->user_id = $userid;
        $useraccess->created_by = Auth::id();
        $useraccess->save();

        // update data murid untuk ortu baru
        if (!is_null(Request::input('representative'))) {
            $murid = User::find(Request::input('representative'));
            $murid->update(['parent_id' => $newUser->id]);
        }

        $profiles = UserProfile::firstOrCreate(['user_id' => $newUser->id]);
        // $profiles->prov = $kodepos->provinsi;
        // $profiles->kec = $kodepos->kecamatan;
        // $profiles->save();

        return Redirect::route('users')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $listUsers = User::where('usertype_id', '!=', '2')->get();
        $listTypes = Usertype::where('is_active', '1')->get();
        $aruser = User::where('id', $user->id)->with('parent')->first()->toArray();
        $aruser['birthdate'] = $user->birthdate;
        $aruser['photo_path'] = $user->photoUrl([]);
       
        $userChild = User::where('parent_id', $aruser['id'])->get();

        $kodepos = KodePos::where('id', $user->city)->get()->take(100)
        ->transform(function($query){
            return [
                'label' => $query->provinsi. ', '. $query->kabupaten.', '. $query->kecamatan.', '.$query->kelurahan.', '. $query->kodepos,
                'id' => $query->id
            ];
        });

        // dd($user->usertype_id);
            return Inertia::render('Users/Edit', [
                'user' => $aruser,
                'listUsers' => $listUsers,
                'listTypes' => $listTypes,
                'userChild' => $userChild,
            ]);
    }

    public function userprofile(User $user)
    {
        $listUsers = User::where('usertype_id', '!=', '2')->get();
        $listTypes = Usertype::where('is_active', '1')->get();
        $aruser = User::where('id', $user->id)->with('parent')->first()->toArray();
        $aruser['birthdate'] = $user->birthdate;
        $aruser['photo_path'] = $user->photoUrl([]);
        $userChild = User::where('parent_id', $aruser['id'])->get();

        $kodepos = KodePos::where('id', $user->city)->get()->take(100)
        ->transform(function($query){
            return [
                'label' => $query->provinsi. ', '. $query->kabupaten.', '. $query->kecamatan.', '.$query->kelurahan.', '. $query->kodepos,
                'id' => $query->id
            ];
        });

        // dd($user->usertype_id);
            return Inertia::render('Users/UserProfile', [
                'user' => $aruser,
                'listUsers' => $listUsers,
                'listTypes' => $listTypes,
                'userChild' => $userChild,
            ]);
    }

    public function userprofilepassword(User $user)
    {
        $listUsers = User::where('usertype_id', '!=', '2')->get();
        $listTypes = Usertype::where('is_active', '1')->get();
        $aruser = User::where('id', $user->id)->with('parent')->first()->toArray();
        $aruser['birthdate'] = $user->birthdate;
        $aruser['photo_path'] = $user->photoUrl([]);
        $aruser['new_password'] = null;
        $aruser['new_confirmation'] = null;
        $userChild = User::where('parent_id', $aruser['id'])->get();

        $kodepos = KodePos::where('id', $user->city)->get()->take(100)
        ->transform(function($query){
            return [
                'label' => $query->provinsi. ', '. $query->kabupaten.', '. $query->kecamatan.', '.$query->kelurahan.', '. $query->kodepos,
                'id' => $query->id
            ];
        });

        // dd($user->usertype_id);
            return Inertia::render('Users/ChangePassword', [
                'user' => $aruser,
                'listUsers' => $listUsers,
                'listTypes' => $listTypes,
                'userChild' => $userChild,
            ]);
    }

    public function profiles(User $user)
    {
        $profiles = UserProfile::firstOrCreate(['user_id' => $user->id]);


        $kodepos = KodePos::where('id', $profiles->prov)->get()->take(100)
        ->transform(function($query){
            return [
                'label' => $query->provinsi. ', '. $query->kabupaten.', '. $query->kecamatan.', '.$query->kelurahan.', '. $query->kodepos,
                'id' => $query->id
            ];
        });
        return Inertia::render('Users/Profile', [
            'user' => $user,
            'profile' => $profiles,
            'pekerjaans' => Pekerjaan::get(),
            'pendidikans' => Pendidikan::get(),
            'penghasilans' => Penghasilan::get(),
            'kodepos' =>$kodepos,
        ]);
    }

    public function store_profiles(User $user)
    {
        // dd(Request::all());
        $user->user_profiles->update(array_merge(Request::all(),['created_by' =>Auth::id()]));
        return Redirect::back()->with('success', 'User updated.');
    }

    public function update(User $user)
    {
        if (App::environment('demo') && $user->isDemoUser()) {
            return Redirect::back()->with('error', 'Updating the demo user is not allowed.');
        }

        // dd(Request::input());
        $arData = Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['nullable', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'max:50', Rule::unique('users')->ignore($user->id)],
            // 'password' => ['nullable'],
            // 'owner' => ['required', 'boolean'],
            // 'photo_path' => ['nullable', 'image'],
            'adress' => ['required'],
            'gender' => ['required'],
            'city' => ['required'],
            'country' => ['required'],
            'mobilephone' => ['required', 'numeric', 'digits_between:8,13'],
            'birthplace' => ['required'],
            'birthdate' => ['required'],
            'usertype_id' => ['required'],
            'parent_id' => ['nullable'],
        ]);

        $arData = array_merge($arData, [
            'fullname' => $arData['first_name'] . ' ' . $arData['last_name'],
            'updatedBy' => Auth::id(),
        ]);
        // $user->update(Request::only('first_name', 'last_name', 'email', 'owner'));
        $user->update($arData);
        if (Request::file('photo_path')) {
            if ($user->photo_path) {
                Storage::delete($user->photo_path);
            }
            $user->update(['photo_path' => Request::file('photo_path')->store('users')]);
        }
        $profiles = UserProfile::firstOrCreate(['user_id' => $user->id]);

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
        }

        return Redirect::back()->with('success', 'User updated.');
    }

    
    public function changePassword(User $user){
        $request = Request::validate([
    		'password' => 'required',
    		'new' => 'required|min:8',
    		'new_confirmation' => 'required'
    	],[
            'password.required' => 'The Old Password field is required.',
            'new.required' => 'The New Password field is required.',
            'new_confirmation.required' => 'The Confirmation New Password field is required.',
            'new.confirmed' => 'New Password doesnt match',
            'new.min' => 'The New Password must be at least 8 characters.',
        ]);
        
    	if (Hash::check($request['password'], Auth::user()->password)) {
            // return Redirect::back()->with('success', 'Masuk password');
            if($request['new']==$request['new_confirmation']){
                $user = User::find(Auth::id());
                $user->password = Hash::make($request['new']);
                $user->save();
                return Redirect::back()->with('success', 'Password changed.');
            }else{
                return Redirect::back()->with('error', 'New Password not match');
            }
    	}else{
            return Redirect::back()->with('error', 'Wrong Old Password.');
        }
    }

    public function destroy(User $user)
    {
        if (App::environment('demo') && $user->isDemoUser()) {
            return Redirect::back()->with('error', 'Deleting the demo user is not allowed.');
        }

       
        
        if( $user->usertype_id==2){
            $group = GroupUser::where('user_id', $user->id)
                     ->update(['is_active' => '-2']);
            // dd($group);
            // $group->is_active = -2;
            // $group->save();

             $course_user = CourseUser::where('user_id', $user->id)
                     ->update(['is_active' => '-2']);
            // $course_user->is_active = -2;
            // $course_user->save();
        // }else{

        }
         $user->delete();
   
        

        return Redirect::back()->with('success', 'User deleted.');
    }

    public function restore(User $user)
    {
        $user->restore();

        return Redirect::back()->with('success', 'User restored.');
    }

    public function exportExcel($typeid)
    {
        // dd($request->all());
        // $groupid = Group::where('id', $raporkelas)->pluck('category_id')->first();
        $usertype=Usertype::where('id',$typeid)->pluck('name')->first();
        return Excel::download(new UserallExport($typeid), 'users_'.$usertype.'.xlsx');
    }

    public function block()
    {
        $data['userblock'] = Userblock::select('*', 'userblocks.id')
            ->join('users', 'userblocks.user_id', '=', 'users.id')
            ->orderBy('users.fullname', 'asc')
            ->get();
        // dd($data['userblocks']);
        return Inertia::render('Users/Block', $data);
    }

    public function destroyblock($id)
    {
        $group = Userblock::where('userblocks.id', $id)
        ->join('users', 'userblocks.user_id', '=', 'users.id')
        ->update(['status'=>0]);
        // dd($group);
        // $group->save;
        return Redirect::route('users.blocks')->with('success', 'user unblocked');
    }

    public function templateuserblock()
    {
        return (Response::download(public_path('asset/sample/template_input_userblock.xlsx')));
    }

    public function processImportUserblock()
    {
        Request::validate([
            'user_data' => ['required', 'file'],
        ]);
        $truncate = Userblock::truncate();
        $path1 = Request::file('user_data')->store('temp');
        $path = storage_path('app').'/'.$path1;
        $result = Excel::import(new UsersBlockImport, Request::file('user_data'));
        
        importLog::create([
            'name' => 'Import User Block',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Import Userblock successfully');
    }

    public function blockview()
    {
        return Inertia::render('Auth/LoginError');
    }
}
