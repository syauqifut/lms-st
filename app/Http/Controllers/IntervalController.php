<?php

namespace App\Http\Controllers;

use App\Interval;
use App\Level;
use App\ImportLog;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Excel;
use App\Imports\IntervalImport;
use App\Exports\IntervalExport;
use App\Exports\MasterExport;


class IntervalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['filters'] = Request::all('search');
        $data['interval'] =  Interval::filter(Request::only('search'))
            ->paginate()
            ->transform(function ($interval) {
                return [
                    'id' => $interval->id,
                    'minmark' => $interval->minmark,
                    'maxmark' => $interval->maxmark,
                    'minavg' => $interval->minavg,
                    'maxavg' => $interval->maxavg,
                    'alphabet' => $interval->alphabet,
                    'status' => $interval->status,
                    'level' => $interval->level,
                ];
            });

            // dd($data['interval']);

        return Inertia::render('Interval/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $level = Level::where('title', '=', 'STAI AL-FITHRAH')->get();
        return Inertia::render('Interval/Create', [
            'level' => $level,
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
        // dd($request->all());
        $rules = [
            'minmark' => ['required', 'max:11'],
            'maxmark' => ['required', 'max:11'],
            'minavg' => ['required', 'max:11'],
            'maxavg' => ['required', 'max:11'],
            'alphabet' => ['required', 'max:255'],
            'status' => ['required', 'max:255'],
            'level_id' => ['required', 'max:255'],
        ];
        $messages = [
            'minmark.required' => 'Min mark cannot be empty',
            'maxmark.required' => 'Max mark cannot be empty',
            'minavg.required' => 'Min average cannot be empty',
            'maxavg.required' => 'Max average cannot be empty',
            'alphabet.required' => 'Alphabet cannot be empty',
            'status.required' => 'Status cannot be empty',
            'level_id.required' => 'Level cannot be empty',
        ];

        $request->validate($rules, $messages);
        // dd($request->all());

        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);
        Interval::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('interval.create')->with('success', 'Interval created successfully.');
        }
        return Redirect::route('interval.index')->with('success', 'Interval created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Interval  $interval
     * @return \Illuminate\Http\Response
     */
    public function show(Interval $interval)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Interval  $interval
     * @return \Illuminate\Http\Response
     */
    public function edit(Interval $interval)
    {
        $data['interval'] = $interval;
        $data['level'] = Level::where('title', '=', 'STAI AL-FITHRAH')->get();
        return Inertia::render('Interval/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interval  $interval
     * @return \Illuminate\Http\Response
     */
    public function update(\Illuminate\Http\Request $request, Interval $interval)
    {
        $interval->update(
            array_merge(
                Request::validate([
                    'minmark' => ['required'],
                    'maxmark' => ['required'],
                    'minavg' => ['required'],
                    'maxavg' => ['required'],
                    'alphabet' => ['required'],
                    'status' => ['required'],
                    'level_id' => ['required'],
                ]),
                ['updated_by' => Auth::id()]
            )
        );

        return Redirect::back()->with('success', 'Interval updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Interval  $interval
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interval $interval)
    {
        {
            $interval->delete();
            return Redirect::route('interval.index')->with('success', 'Interval deleted');
        }
    }
    public function import()
    {
        return Inertia::render('Interval/Import');
    }

    public function export()
    {
        return Excel::download(new IntervalExport, 'interval.xlsx');
    }

    public function processImport()
    {
        Request::validate([
            'data' => ['required', 'file'],
        ]);
        $path1 = Request::file('data')->store('temp');
        $path = storage_path('app').'/'.$path1;
        $result = Excel::import(new IntervalImport, Request::file('data'));
        importLog::create([
            'name' => 'Import Interval',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Successfully import Interval ');
    }

    public function template()
    {
        // dd('a');
        return (Response::download(public_path('asset/sample/template_input_interval.xlsx')));
    }
}
