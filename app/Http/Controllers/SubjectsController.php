<?php

namespace App\Http\Controllers;

use App\importLog;
use App\Imports\SubjectsImport;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Subject;
use Excel;
use Auth;

class SubjectsController extends Controller
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
    public function create()
    {
        return Inertia::render('Subjects/Create');
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
        ];
        $messages = [
            'name.required' => 'Subject cannot be empty',
            'description.required' => 'Description cannot be empty',
            'is_active.required' => 'Status cannot be empty',
        ];

        $request->validate($rules, $messages);
        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);
        Subject::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('subjects.create')->with('success', 'Subject created successfully.');
        }
        return Redirect::route('subjects')->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

        return Redirect::back()->with('success', 'Subject deleted');
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
        $path1 = Request::file('subject_data')->store('temp');
        $path = storage_path('app').'/'.$path1;
        $result = Excel::import(new SubjectsImport, Request::file('subject_data'));
        importLog::create([
            'name'          => 'Import Subjects',
            'description'   => Request::input('description'),
            'subject_code'  => Request::input('subject_code'),
            'sks'           => Request::input('sks'),
            'subject_type'  => Request::input('subject_type'),
            'createdBy'     => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Import Subjects successfully');
    }
    public function template()
    {
        return (Response::download(public_path('asset/sample/template_input_subjects.xlsx')));
    }
}
