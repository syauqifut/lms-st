<?php

namespace App\Http\Controllers;

use App\Access;
use App\UserAccess;
use App\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class AccessController extends Controller
{
    public function index()
    {
        // $data['menus'] = Access::where('isactive', 1)->get()
        //     ->transform(function ($access){
        //         return [
        //             'id' => $access->id,
        //             'name' => $access->name,
        //             'logo' => $access->logo,
        //             'route' => $access->route,
        //         ];
        //     })
        // ;
        $userid = Auth::id();
        // dd($userid);
        $data['menus'] = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('user_accesses.user_id', $userid)
            ->where('accesses.isactive', 1)
            ->get()
            ->transform(function ($access){
                return [
                    'id' => $access->id,
                    'name' => $access->name,
                    'logo' => $access->logo,
                    'route' => $access->route,
                ];
            })
        ;
        // dd($data);
        return Inertia::render('Access/Select', $data);
    }

    public function list()
    {
        $listMenus = Access::where('isactive', 1)->paginate()
                        ->transform(function ($menu){
                            return [
                                'id' => $menu->id,
                                'name' => $menu->name,
                                'icon' => $menu->logo,
                                'route' => $menu->route,
                            ];
                        });
        // dd($listMenus);
        return Inertia::render('Access/Index', [
            'filters' => Request::all('search', 'trashed'),
            'menus' => $listMenus
        ]);
    }

    public function create()
    {
        return Inertia::render('Access/Create');
    }

    public function store()
    {
        $menuData = Request::validate([
                'name' => ['required'],
                'logo' => [],
                'description' => [],
                'route' => ['required'],
            ]);
        // dd($menuData);
        $menuData['isactive'] = 1;
        $menuData['createdBy'] = Auth::id();
        $menu = Access::create($menuData);
        return Redirect::route('access.list')->with('success', 'Access created.');
    }

    public function edit(Access $access)
    {
        return Inertia::render('Access/Edit', [
            'menu' => $access->toArray()
        ]);
    }

    public function update(Access $access)
    {
        $access->update(
            array_merge(Request::validate([
                'name' => ['required'],
                'icon' => [],
                'description' => [],
                'route' => ['required'],
            ], ['updatedBy' => Auth::id()]))
        );

        return Redirect::back()->with('success', 'Access updated.');

    }

    public function destroy(Access $access)
    {
        Access::where('id',$access->id)->delete();

        return Redirect::route('access.list')->with('success', 'Access has been deleted.');
    }

    public function user(Access $access)
    {
        $accessid = $access->id;
        $listUsers      = UserAccess::where('user_accesses.access_id', $accessid)
            ->join('users', 'users.id', '=', 'user_accesses.user_id')
            ->select('user_accesses.id', 'user_accesses.user_id', 'users.fullname', 'users.email')
            ->orderBy('users.fullname', 'asc')
            ->get();
        
        $userForSelectOption = User::with('usertype')
            ->whereNOTIn('id', function ($query) use ($accessid) {
                $query->select('user_id')->from('user_accesses')
                    ->where('user_accesses.access_id', '=', $accessid);
            })
            ->orderBy('users.usertype_id', 'asc')
            ->orderBy('users.fullname', 'asc')
            ->get()
            ->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'fullname' => $user->usertype->name . ' - ' . $user->fullname,
                    'email' => $user->email,
                ];
            });
        // dd($listUsers);
        return Inertia::render('Access/User', [
            'users_in_group'    => $listUsers,
            'user_for_select_option'    => $userForSelectOption,
            'access' => $access
        ]);
    }

    public function storeUser(\Illuminate\Http\Request $request)
    {
        // dd($request);
        $rules = [
            'user_id' => ['required'],
            'access_id' => ['required'],
        ];
        $messages = [
            'user_id.required' => 'User tidak boleh kosong',
            'access_id.required' => 'Access tidak boleh kosong',
        ];
        $request->validate($rules, $messages);

        $user = User::find($request->user_id);
        // $group = $request->group_id; 

        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);
        UserAccess::create($request->all());

        return Redirect::route('access.user', $request->access_id)->with('success', $user->fullname . ' added successfully.');
    }

    public function destroyUser($id)
    {
        $access    = UserAccess::where('user_accesses.id', $id)
            ->join('users', 'users.id', '=', 'user_accesses.user_id')
            ->select('user_accesses.id', 'user_accesses.access_id', 'users.fullname', 'users.email')
            ->first();

        UserAccess::where('id',$id)->delete();

        return Redirect::route('access.user', $access->access_id)->with('success', $access->fullname . ' removed from access.');
    }
}
