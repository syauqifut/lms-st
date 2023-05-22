<?php

namespace App\Http\Controllers;

use App\importLog;
use App\Imports\TartibsImport;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Tartib;
use Excel;
use Auth;

class TartibsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['filters'] = Request::all('search', 'trashed');
        $data['tartibs'] =  Tartib::filter(Request::only('search', 'trashed'))
            ->orderBy('jenis')
            ->orderBy('kode_pelanggaran')
            ->paginate()
            ->transform(function ($tartib) {
                return [
                    'id'            => $tartib->id,
                    'jenis'          => $tartib->jenis,
                    'kategori'   => $tartib->kategori,
                    'nama_pelanggaran'  => $tartib->nama_pelanggaran,
                    'kode_pelanggaran'  => $tartib->kode_pelanggaran,
                    'skor'           => $tartib->skor,
                    'is_active'     => $tartib->is_active,
                ];
            });

        return Inertia::render('Tartibs/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Tartibs/Create');
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
            'nama_pelanggaran' => ['required', 'max:255'],
            'kode_pelanggaran' => ['required'],
            'is_active' => ['required'],
            'jenis' => ['required'],
            'kategori' => ['required'],
            'skor' => ['required'],
        ];
        $messages = [
            'nama_pelanggaran.required' => 'Tartib name cannot be empty',
            'kode_pelanggaran.required' => 'Kode cannot be empty',
            'is_active.required' => 'Status cannot be empty',
        ];

        $request->validate($rules, $messages);
        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);
        Tartib::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('tartibs.create')->with('success', 'Tartib created successfully.');
        }
        return Redirect::route('tartibs')->with('success', 'Tartib created successfully.');
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
    public function edit(Tartib $tartib)
    {
        $data['tartib'] = $tartib;
        return Inertia::render('Tartibs/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Tartib $tartib)
    {
        $oke = $tartib->update(
            array_merge(
                Request::validate([
                    'nama_pelanggaran' => ['required', 'max:255'],
                    'kode_pelanggaran' => ['required'],
                    'is_active' => ['required'],
                    'jenis' => ['required'],
                    'kategori' => ['required'],
                    'skor' => ['required'],  
                ]),
                ['updated_by' => Auth::id()]
            )
        );

        //var_dump($oke);
        //exit();

        return Redirect::back()->with('success', 'Tartib updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tartib $tartib)
    {
        $tartib->is_active = 0;
        $tartib->delete();
        $tartib->save();

        return Redirect::back()->with('success', 'Tartib deleted');
    }

    public function restore(Tartib $tartib)
    {
        $tartib->restore();
        $tartib->is_active = 1;
        $tartib->save();

        return Redirect::back()->with('success', 'Tartib restored.');
    }

    public function import()
    {
        return Inertia::render('Tartibs/Import');
    }

    public function processImport()
    {

        
        Request::validate([
            'tartib_data' => ['required', 'file'],
        ]);
        //error harus sesuai sample
        $path1 = Request::file('tartib_data')->store('temp');
        $path = storage_path('app').'/'.$path1;
        $result = Excel::import(new TartibsImport, Request::file('tartib_data'));
        importLog::create([
            'name'          => 'Import Tartibs',
            'description'   => Request::input('description'),
           
            'createdBy'     => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Import Tartibs successfully');
    }
    public function template()
    {
        return (Response::download(public_path('asset/sample/template_input_tartibs.xlsx')));
    }
}
