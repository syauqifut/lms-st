<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use App\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['filters'] = Request::all('search');
        $data['categories'] =  Category::filter(Request::only('search'))
        ->orderBy('parent_category_id')    
        ->orderBy('id')
            ->paginate()
            ->transform(function ($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                    'category_parent' => $category->categoryParent,
                    'is_active' => $category->is_active,
                ];
            });

        return Inertia::render('Categories/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['categories'] = Category::all();
        return Inertia::render('Categories/Create', $data);
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
            'title.required' => 'Category name cannot be empty',
        ];

        $request->validate($rules, $messages);
        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);
        Category::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('categories.create')->with('success', 'Cateory successfully created.');
        }
        return Redirect::route('categories.index')->with('success', 'Category successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
      //  echo $category['id'];
        $data['category'] = $category;
        $data['categories'] = Category::where('id','<>',$category['id'])->get();

         return Inertia::render('Categories/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
     // print_r(Request::all());
        
        $category->update(
            array_merge(
                Request::validate([
                    'title' => ['required'],
                    'is_active' => ['required'],
                    'parent_category_id' => ['nullable'],
                ]),
                ['updated_by' => Auth::id()]
            )
        );

        return Redirect::back()->with('success', 'Category updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if ($category->parent_category_id == null) {
            $childCategoryActive = Category::where('parent_category_id', $category->id)
                ->where('is_active', 1)
                ->first();
            if ($childCategoryActive) {
                return Redirect::back()->with('error', 'Failed to delete, because there are children categories that still active');
            }
        }

        $category->is_active = 0;
        $category->save();

        return Redirect::back()->with('success', 'Category deleted');
    }

    public function restore(Category $category)
    {
        $category->is_active = 1;
        $category->save();

        return Redirect::back()->with('success', 'Category restored');
    }
}
