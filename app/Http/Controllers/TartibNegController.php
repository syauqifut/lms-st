<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Excel;
use Auth;
use App\importLog;
use App\Tartib;
use App\User;
use App\Group;
use App\GroupUser;
use App\TartibNegSiswa;
use App\UserAccess;
use Illuminate\Support\Facades\URL;

class TartibNegController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $data['filters'] = Request::all('search', 'trashed');
        $data['subjects'] =  Subject::filter(Request::only('search', 'trashed'))
            ->orderBy('name')
            ->paginate()
            ->transform(function ($subject) {
                return [
                    'id'            => $subject->id,
                    'name'          => $subject->name,
                    'description'   => $subject->description,
                    'subject_code'  => $subject->subject_code,
                    'subject_type'  => $subject->subject_type,
                    'sks'           => $subject->sks,
                    'is_active'     => $subject->is_active,
                ];
            });

        return Inertia::render('Subjects/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $groupUser = GroupUser::find($id);
        $user = User::find($groupUser->user_id);
        $group = Group::find($groupUser->group_id);
        $sumpoin = $user->perilakuNegatif->sum('poin');

        //   dd($sumpoin);

        $tartibuser = TartibNegSiswa::where('user_id', $groupUser->user_id)
            ->where('group_id', $groupUser->group_id)
            ->join('tartibs as t', 't.id', 'tartibnegsiswa.tartib_id')
            ->join('users as u', 'u.id', 'tartibnegsiswa.userlapor_id')
            ->orderBy('date')
            ->select('Tartibnegsiswa.id', 'date', 'u.fullname', 't.nama_pelanggaran', 't.kode_pelanggaran', 'poin','catatan')
            ->get()
            ->transform(function ($tartibneg) {
                return [
                    'id' => $tartibneg->id,
                    'date' => $tartibneg->date,
                    'fullname' => $tartibneg->fullname,
                    'nama_pelanggaran' => $tartibneg->nama_pelanggaran,
                    'kode_pelanggaran' => $tartibneg->kode_pelanggaran,
                    'poin' => $tartibneg->poin,
                    'catatan' => $tartibneg->catatan,
                ];
            });

        //dd($tartibuser);
        return Inertia::render('Tartibneg/Show', [
            'user'    => $user,
            'group'    => $group,
            'groupUser' => $groupUser,
            'sumpoin'  => $sumpoin,
            'tartibuser' => $tartibuser,
        ]);
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
        $data['loginid']=Auth::id();
        $data['tartibs'] = Tartib::where('jenis', 'NEGATIF')
            ->orderBy('skor')
            ->get()
            ->transform(function ($tartib) {
                return [
                    'id' => $tartib->id,
                    'name' => $tartib->kategori . ' - ' . $tartib->nama_pelanggaran . ' [' . $tartib->skor . ']',
                ];
            });
        return Inertia::render('Tartibneg/Create', $data);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        // dd($request->all());
        $rules = [
            'date' => ['required'],
            'tartib_id' => ['required'],
            'userlapor_id' => ['required'],
        ];
        $messages = [
            'date.required' => 'Violation Date cannot be empty',
            'tartib_id.required' => 'Violation cannot be empty',
            'userlapor_id.required' => 'Reporter cannot be empty',
        ];
        $request->validate($rules, $messages);

        $groupUser = GroupUser::find($request->group_user_id);
        $group = Group::find($groupUser->group_id);

        $tartib = Tartib::find($request->tartib_id);
        TartibNegSiswa::create([
            'date' => $request->date,
            'group_id' => $group->id,
            'user_id' => $request->user_id,
            'tartib_id' => $tartib->id,
            'userlapor_id' => $request->userlapor_id,
            'poin' => $tartib->skor,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'catatan'=> $request->catatan,
        ]);

        return Redirect::route('tartibneg_users.show', $request->group_user_id)->with('success', 'Successfully add violation.');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject)
    {
        $data['subject'] = $subject;
        return Inertia::render('Subjects/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Subject $subject)
    {
        $oke = $subject->update(
            array_merge(
                Request::validate([
                    'name' => ['required'],
                    'description' => ['required'],
                    'is_active' => ['required'],
                    'sks' => ['max:5'],
                    'subject_code' => ['max:15'],
                    'subject_type' => ['max:200'],
                ]),
                ['updated_by' => Auth::id()]
            )
        );

        //var_dump($oke);
        //exit();

        return Redirect::back()->with('success', 'Subject updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        $subject->is_active = 0;
        $subject->delete();
        $subject->save();

        return Redirect::back()->with('success', 'Subject berhasil dihapus');
    }

    public function restore(Subject $subject)
    {
        $subject->restore();
        $subject->is_active = 1;
        $subject->save();

        return Redirect::back()->with('success', 'Subject restored.');
    }

    public function import()
    {
        return Inertia::render('Subjects/Import');
    }

    public function processImport()
    {


        Request::validate([
            'subject_data' => ['required', 'file'],
        ]);
        $result = Excel::import(new SubjectsImport, Request::file('subject_data')->path());
        importLog::create([
            'name'          => 'Import Subjects',
            'description'   => Request::input('description'),
            'subject_code'  => Request::input('subject_code'),
            'sks'           => Request::input('sks'),
            'subject_type'  => Request::input('subject_type'),
            'createdBy'     => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Berhasil import Subjects ');
    }
    public function template()
    {
        return (Response::download(public_path('asset/sample/template_input_subjects.xlsx')));
    }
}
