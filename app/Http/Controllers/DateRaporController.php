<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;         
use App\DateRapor;
use App\Group;
use Inertia\Inertia;

class DateRaporController extends Controller
{
    public function index()
    {
        $data['filters'] = Request::all('search');
        $data['daterapors'] =  DateRapor::filter(Request::only('search'))
        ->orderBy('id')
            ->paginate()
            ->transform(function ($daterapors) {
                return [
                    'id' => $daterapors->id,
                    'group' => $daterapors->group->classes,
                    'year' => $daterapors->group->year,
                    'date_rapor' => $daterapors->date_rapor,
                    'is_active' => $daterapors->is_active,
                ];
            });

        return Inertia::render('DateRapor/Index', $data);
    }

    public function create()
    {
        $data['groups'] = Group::where('is_active', 1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'fullname' => $group->classes . " " . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')',
                ];
            });

        return Inertia::render('DateRapor/Create', $data);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $rules = [
            'group_id' => ['required', 'max:255'],
            'date_rapor' => ['required', 'max:255'],
        ];
        $messages = [
            'group_id.required' => 'Group Year cannot be empty',
            'date_rapor.required' => 'Report date cannot be empty',
        ];

        $exist = DateRapor::where('group_id', $request->group_id)->exists();

        if($exist){
            return Redirect::back()->with('error', 'Report date in this group already exist');
        }

        $request->validate($rules, $messages);
        $request->merge(['is_active' => 1]);
        DateRapor::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('daterapors.create')->with('success', 'Report date created successfully.');
        }
        return Redirect::back()->with('success', 'Report date created successfully.');
    }


    public function edit(DateRapor $daterapor)
    {
        $data['daterapor'] = $daterapor;
        $data['daterapors'] = DateRapor::where('id','<>',$daterapor['id'])->get();
        $class = Group::where('id', $daterapor->group_id)->pluck('classes')->first();
        $year = Group::where('id', $daterapor->group_id)->pluck('year')->first();
        $data['groups'] = [$class . "(" . $year . ")"];

         return Inertia::render('DateRapor/Edit', $data);
    }

    public function update(Request $request, DateRapor $daterapor)
    {
        
        $daterapor->update(
            array_merge(
                Request::validate([
                    'date_rapor' => ['required'],
                    'is_active' => ['required'],
                ]),
            )
        );

        return Redirect::back()->with('success', 'Report date updated.');
    }

    public function destroy(DateRapor $daterapor)
    {
        $hd = DateRapor::where('id', $daterapor->id)
                ->where('is_active', 1)
                ->first();

        $hd->is_active = 0;
        $hd->save();

        return Redirect::back()->with('success', 'Report date deleted');
    }

    public function restore(DateRapor $daterapor)
    {
        $daterapor->is_active = 1;
        $daterapor->save();

        return Redirect::back()->with('success', 'Report date restored');
    }
}
