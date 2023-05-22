<?php

namespace App\Http\Controllers;

use App\Persentase;
use App\Category;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;


class PersentaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['filters'] = Request::all('search');
        $data['persen'] =  Persentase::filter(Request::only('search'))
                            ->orderBy('category_id')
                            ->paginate()
                            ->transform(function ($persen) {
                                return [
                                    'id' => $persen->id,
                                    'persen' => $persen->persen,
                                    'task_type' => $persen->task_type,
                                    'category' => $persen->category,
                                ];
                            });

        return Inertia::render('Persentase/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kategori = Category::
                    whereNOTIn('id', function ($query){
                        $query->select('parent_category_id')->from('categories')->where('parent_category_id', '!=', 0);
                    })
                    ->get();
        return Inertia::render('Persentase/Create', [
            'kategori' => $kategori,
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
        $rules = [
            'persen' => ['required', 'max:11'],
            'task_type' => ['required', 'max:255'],
            'category_id' => ['required', 'max:255'],
        ];
        $messages = [
            'persen.required' => 'Min mark cannot be empty',
            'task_type.required' => 'Task type mark cannot be empty',
            'category_id.required' => 'Category cannot be empty',
        ];

        $request->validate($rules, $messages);

        if (Persentase::where('task_type', '=', $request->task_type)->where('category_id', '=', $request->category_id)->exists()){
            return Redirect::back()->with('error', 'Persentase already exist');
        }else {
            $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);
            Persentase::create($request->all());

            if ($request->addAgain) {
                return Redirect::route('persentase.create')->with('success', 'Persentase created successfully.');
            }
            return Redirect::route('persentase')->with('success', 'Persentase created successfully.');
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Persentase  $persentase
     * @return \Illuminate\Http\Response
     */
    public function show(Persentase $persentase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Persentase  $persentase
     * @return \Illuminate\Http\Response
     */
    public function edit(Persentase $persentase)
    {
        $data['persentase'] = $persentase;
        $data['kategori'] = Category::whereNOTIn('id', function ($query){
                                $query->select('parent_category_id')->from('categories')->where('parent_category_id', '!=', 0);
                            })
                            ->get();
        $data['namakategori'] = Category::where('id', $persentase->category_id)->pluck('title')->first();
        return Inertia::render('Persentase/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Persentase  $persentase
     * @return \Illuminate\Http\Response
     */
    public function update(\Illuminate\Http\Request $request, Persentase $persentase)
    {
        if (Persentase::where('task_type', '=', $request->task_type)->where('category_id', '=', $request->category_id)->exists()){
            if($request->task_type == $persentase->task_type){
                $persentase->update(
                    array_merge(
                        Request::validate([
                            'persen' => ['required'],
                            'task_type' => ['required'],
                        ]),
                        ['updated_by' => Auth::id()]
                    )
                );
                return Redirect::route('persentase')->with('success', 'Persentase updated.');
                // $isi = 'enek kat enek tugas iki tugase';
            }
            else{
                return Redirect::back()->with('error', 'Persentase already exist');
                // $isi = 'enek kat enek tugas bedo tugase';
            }
        }else{
            $persentase->update(
                array_merge(
                    Request::validate([
                        'persen' => ['required'],
                        'task_type' => ['required'],
                    ]),
                    ['updated_by' => Auth::id()]
                )
            );
            return Redirect::route('persentase')->with('success', 'Persentase updated.');
            // $isi = 'enek kat gak enek tugas';
        }
        // dd($isi);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Persentase  $persentase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Persentase $persentase)
    {
        $persentase->delete();
        return Redirect::route('persentase')->with('success', 'Persentase deleted');
    }
}
