<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;         
use App\HeadMaster;
use Inertia\Inertia;

class HeadMasterController extends Controller
{
    public function index()
    {
        $data['filters'] = Request::all('search');
        $data['headmasters'] =  HeadMaster::filter(Request::only('search'))
        ->orderBy('id')
            ->paginate()
            ->transform(function ($headmasters) {
                return [
                    'id' => $headmasters->id,
                    'tahun_ajaran' => $headmasters->tahun_ajaran,
                    'name' => $headmasters->name,
                    'is_active' => $headmasters->is_active,
                ];
            });

        return Inertia::render('HeadMaster/Index', $data);
    }

    public function create()
    {
        return Inertia::render('HeadMaster/Create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $rules = [
            'tahun_ajaran' => ['required', 'max:255'],
            'name' => ['required', 'max:255'],
        ];
        $messages = [
            'tahun_ajaran.required' => 'Year cannot be empty',
            'name.required' => 'Head Master Name cannot be empty',
        ];

        $request->validate($rules, $messages);
        $request->merge(['is_active' => 1]);
        HeadMaster::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('headmasters.create')->with('success', 'Head Master created successfully.');
        }
        return Redirect::back()->with('success', 'Head Master created successfully.');
    }


    public function edit(HeadMaster $headmaster)
    {
        $data['headmaster'] = $headmaster;
        $data['headmasters'] = HeadMaster::where('id','<>',$headmaster['id'])->get();

         return Inertia::render('HeadMaster/Edit', $data);
    }

    public function update(Request $request, HeadMaster $headmaster)
    {
        
        $headmaster->update(
            array_merge(
                Request::validate([
                    'tahun_ajaran' => ['required'],
                    'name' => ['required'],
                    'is_active' => ['required'],
                ]),
            )
        );

        return Redirect::back()->with('success', 'Head Master updated.');
    }

    public function destroy(HeadMaster $headmaster)
    {
        $hd = HeadMaster::where('id', $headmaster->id)
                ->where('is_active', 1)
                ->first();

        $hd->is_active = 0;
        $hd->save();

        return Redirect::back()->with('success', 'Head Master deleted');
    }

    public function restore(HeadMaster $headmaster)
    {
        $headmaster->is_active = 1;
        $headmaster->save();

        return Redirect::back()->with('success', 'Head Master restored.');
    }
}
