<?php

namespace App\Http\Controllers;

use App\TartibPosSiswa;
use App\Tartib;
use App\User;
use App\Group;
use App\GroupUser;
use App\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Inertia\Inertia;

class TartibPosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function create($group_user_id)
    {
        $groupUser = GroupUser::find($group_user_id);

        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $data['groupUserId'] = $group_user_id;
        $data['user'] = User::find($groupUser->user_id);
        $data['teachers'] =  User::where('usertype_id', 3)->whereIn('id', $access)->whereOr('usertype_id', 4)->get()
            ->transform(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'fullname' => $teacher->name,
                ];
            });

        $dataPositif = TartibPosSiswa::where('user_id', $data['user']->id)->pluck('tartib_id');
        $data['loginid']=Auth::id();
        $data['tartibs'] = Tartib::where('jenis', 'POSITIF')
            ->whereNotIn('id', $dataPositif)
            ->orderBy('skor')
            ->get()
            ->transform(function ($tartib) {
                return [
                    'id' => $tartib->id,
                    'name' => $tartib->kategori . ' - ' . $tartib->nama_pelanggaran . ' [' . $tartib->skor . ']',
                ];
            });

        return Inertia::render('Tartibpos/Create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'date' => ['required'],
            'tartib_id' => ['required'],
            'userlapor_id' => ['required'],
        ];
        $messages = [
            'date.required' => 'Possitive Attitude Date cannot be empty',
            'tartib_id.required' => 'Possitive Attitude cannot be empty',
            'userlapor_id.required' => 'Reporter cannot be empty',
        ];
        $request->validate($rules, $messages);

        $groupUser = GroupUser::find($request->group_user_id);
        $group = Group::find($groupUser->group_id);

        $tartib = Tartib::find($request->tartib_id);
        TartibPosSiswa::create([
            'date' => $request->date,
            'group_id' => $group->id,
            'user_id' => $request->user_id,
            'tartib_id' => $tartib->id,
            'userlapor_id' => $request->userlapor_id,
            'poin' => $tartib->skor,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'catatan' =>$request->catatan,
        ]);

        return Redirect::route('tartibpos_users.show', $request->group_user_id)->with('success', 'Successfully add positiv attitude.');
    }

    public function show($group_user_id)
    {
        $groupUser = GroupUser::find($group_user_id);
        $user = User::find($groupUser->user_id);
        $group = Group::find($groupUser->group_id);
        $sumpoin = $user->perilakuPositif->sum('poin');

        //   dd($sumpoin);

        $tartibuser = TartibPosSiswa::where('user_id', $groupUser->user_id)
            ->where('group_id', $groupUser->group_id)
            ->join('tartibs as t', 't.id', 'tartibpossiswa.tartib_id')
            ->join('users as u', 'u.id', 'tartibpossiswa.userlapor_id')
            ->orderBy('date')
            ->select('tartibpossiswa.id', 'date', 'u.fullname', 't.nama_pelanggaran', 't.kode_pelanggaran', 'poin','catatan')
            ->get()
            ->transform(function ($tartibpos) {
                return [
                    'id' => $tartibpos->id,
                    'date' => $tartibpos->date,
                    'fullname' => $tartibpos->fullname,
                    'nama_pelanggaran' => $tartibpos->nama_pelanggaran,
                    'kode_pelanggaran' => $tartibpos->kode_pelanggaran,
                    'poin' => $tartibpos->poin,
                    'catatan' =>$tartibpos->catatan,
                ];
            });

        //dd($tartibuser);
        return Inertia::render('Tartibpos/Show', [
            'user'    => $user,
            'group'    => $group,
            'groupUser' => $groupUser,
            'sumpoin'  => $sumpoin,
            'tartibuser' => $tartibuser,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
