<?php

namespace App\Http\Controllers;

use App\Level;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['filters'] = Request::all('search');
        $data['levels'] =  Level::filter(Request::only('search'))
            ->orderBy('title')
            ->paginate()
            ->transform(function ($level) {
                return [
                    'id' => $level->id,
                    'title' => $level->title,
                ];
            });

        return Inertia::render('Levels/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Levels/Create');
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
            'title' => ['required', 'max:255'],
        ];
        $messages = [
            'title.required' => 'Level title cannot be empty',
        ];

        $request->validate($rules, $messages);
        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);
        Level::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('levels.create')->with('success', 'Level created successfully.');
        }
        return Redirect::route('levels.index')->with('success', 'Level created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function show(Level $level)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function edit(Level $level)
    {
        $data['level'] = $level;
        return Inertia::render('Levels/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Level $level)
    {
        $level->update(
            array_merge(
                Request::validate([
                    'title' => ['required'],
                ]),
                ['updated_by' => Auth::id()]
            )
        );

        return Redirect::back()->with('success', 'Level updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function destroy(Level $level)
    {
        $level->delete();
        return Redirect::route('levels.index')->with('success', 'Level deleted');
    }

    public function restore()
    {

    }
}
