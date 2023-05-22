<?php

namespace App\Http\Controllers;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\SubjectModule;
use App\Subject;
use App\Group;
use Auth;

class SubjectModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['filters'] = Request::all('search', 'trashed');
        $data['subjectModules'] =  SubjectModule::filter(Request::only('search'))
            ->orderBy('name')
            ->paginate()
            ->transform(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject' => $subject->subject ? $subject->subject : null,
                    'group' => $subject->group ? $subject->group : null,
                    'name' => $subject->name,
                    'description' => $subject->description,
                    'is_active' => $subject->is_active,
                ];
            });
        return Inertia::render('SubjectModules/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['subjects'] = Subject::all();
        $data['groups'] = Group::all();

        return Inertia::render('SubjectModules/Create', $data);
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
            'name' => ['required', 'max:255'],
            'description' => ['required'],
            'is_active' => ['required'],
            'subject_id' => ['required'],
            'group_id' => ['required'],
        ];
        $messages = [
            'name.required' => 'Nama modul tidak boleh kosong',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'is_active.required' => 'Status tidak boleh kosong',
            'subject_id.required' => 'Subject tidak boleh kosong',
            'group_id.required' => 'Group tidak boleh kosong',
        ];

        $request->validate($rules, $messages);
        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);
        SubjectModule::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('subject_modules.create')->with('success', 'Modul berhasil dibuat.');
        }
        return Redirect::route('subject_modules')->with('success', 'Subject berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SubjectModule  $subjectModule
     * @return \Illuminate\Http\Response
     */
    public function show(SubjectModule $subjectModule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubjectModule  $subjectModule
     * @return \Illuminate\Http\Response
     */
    public function edit(SubjectModule $subjectModule)
    {
        $data['subjectModule'] = $subjectModule;
        $data['subjects'] = Subject::all();
        $data['groups'] = Group::all();

        return Inertia::render('SubjectModules/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SubjectModule  $subjectModule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubjectModule $subjectModule)
    {
        $subjectModule->update(
            array_merge(
                Request::validate([
                    'name' => ['required'],
                    'description' => ['required'],
                    'is_active' => ['required'],
                    'subject_id' => ['required'],
                    'group_id' => ['required'],
                ]),
                ['updated_by' => Auth::id()]
            )
        );

        return Redirect::back()->with('success', 'Modul berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubjectModule  $subjectModule
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubjectModule $subjectModule)
    {
        $subjectModule->is_active = 0;
        $subjectModule->save();

        return Redirect::back()->with('success', 'Modul berhasil dihapus');
    }

    public function restore(Subject $subjectModule)
    {
        $subjectModule->is_active = 1;
        $subjectModule->save();

        return Redirect::back()->with('success', 'Modul berhasil di-restore.');
    }
}
