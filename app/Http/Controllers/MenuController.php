<?php

namespace App\Http\Controllers;

use App\Menu;
// use Illuminate\Http\Request;
use App\Group;
use App\User;
use Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listMenus = Menu::where('isactive', 1)->paginate()
                        ->transform(function ($menu){
                            return [
                                'id' => $menu->id,
                                'name' => $menu->name,
                                'icon' => $menu->icon,
                                'route' => $menu->route,                                
                                'order_menu' => $menu->order_menu,
                                'parent' => $menu->parent ? $menu->parent : '-',
                            ];
                        });

        return Inertia::render('Menus/Index', [
            'filters' => Request::all('search', 'trashed'),
            'menus' => $listMenus
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $listMenus = Menu::where('isactive', 1)
        ->where('parentId',0)
        ->get();

        return Inertia::render('Menus/Create',[
            'listMenus' => $listMenus,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $menuData = Request::validate([
                'name' => ['required'],
                'icon' => [],
                'description' => [],
                'route' => ['required'],
                'parentId' => ['required'],
                'order_menu' => ['required'],
            ]);
        // dd($menuData);
        $menuData['isactive'] = 1;
        $menuData['createdBy'] = Auth::id();
        $menu = Menu::create($menuData);
        return Redirect::route('menus')->with('success', 'Menus created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        return Inertia::render('Menus/Edit', [
            'menu' => $menu->toArray(),
            'listMenus' => Menu::where('isactive', 1)
            ->where('id', '!=', $menu->id)
            ->where('parentId',0)->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Menu $menu)
    {
        $menu->update(
            array_merge(Request::validate([
                'name' => ['required'],
                'icon' => [],
                'description' => [],
                'route' => ['required'],
                'parentId' => ['required'],
                'order_menu' => ['required'],
            ], ['updatedBy' => Auth::id()]))
        );

        return Redirect::back()->with('success', 'Menu updated.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        $menu->isactive = 0;
        $menu->save();
        return Redirect::route('menus')->with('success', 'Menus deleted.');
    }

    public function restore(Menu $menu)
    {
        $menu->isactive = 1;
        $menu->save();

        return Redirect::back()->with('success', 'Menu restored.');
    }
}
