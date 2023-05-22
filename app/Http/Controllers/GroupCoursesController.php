<?php

namespace App\Http\Controllers;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\GroupCourses;
use App\Course;
use App\Group;
use Auth;

class GroupCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $menuName = "Group Course";

    public function index()
    {
        $data['filters'] = Request::all('search', 'trashed');
        $data['GroupCourses'] =  GroupCourses::filter(Request::only('search'))
            ->orderBy('id')
            ->paginate()
            ->transform(function ($groupCourse) {
                return [
                    'id' => $groupCourse->id,
                    'course' => $groupCourse->course ? $groupCourse->course : null,
                    'group' => $groupCourse->group ? $groupCourse->group : null,
                ];
            });
        $data['menuName'] = $this->menuName;
        return Inertia::render('GroupCourses/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['courses'] = Course::all();
        $data['groups'] = Group::all();
        $data['menuName'] = $this->menuName;

        return Inertia::render('GroupCourses/Create', $data);
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
            'course_id' => ['required'],
            'group_id' => ['required'],
        ];
        $messages = [
            'course_id.required' => 'Courses tidak boleh kosong',
            'group_id.required' => 'Group tidak boleh kosong',
        ];

        $request->validate($rules, $messages);
        $request->merge(['created_by' => Auth::id(), 'updated_by' => Auth::id()]);


       // echo $request['course_id']."-".$request['group_id'];
        $ada=GroupCourses::where([['course_id', $request['course_id']], ['group_id', $request['group_id']]])->first();
       
        if($ada)
            return Redirect::back()->with('error', 'Group Course telah dibuat sebelumnya');
        
        GroupCourses::create($request->all());

        if ($request->addAgain) {
            return Redirect::route('group_courses.create')->with('success', $this->menuName.' berhasil dibuat.');
        }
        return Redirect::route('group_courses')->with('success', $this->menuName.' berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GroupCourses  $GroupCourses
     * @return \Illuminate\Http\Response
     */
    public function show(GroupCourses $GroupCourses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GroupCourses  $GroupCourses
     * @return \Illuminate\Http\Response
     */
    public function edit(groupCourses $groupCourses)
    {
        $data['group_courses'] = $groupCourses;
        $data['courses'] = Course::all();
        $data['groups'] = Group::all();
        
        $data['menuName'] = $this->menuName;

        return Inertia::render('GroupCourses/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GroupCourses  $GroupCourses
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, groupCourses $groupCourses)
    {
        $groupCourses->update(
            array_merge(
                Request::validate([
                    'course_id' => ['required'],
                    'group_id' => ['required'],
                ]),
                ['updated_by' => Auth::id()]
            )
        );


        return Redirect::back()->with('success', $this->menuName.' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GroupCourses  $GroupCourses
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        //var_dump($id);exit();

        GroupCourses::where('id',$id)->delete(); 

        return Redirect::route('group_courses')->with('success', $this->menuName.' berhasil dihapus.');
    }

}
