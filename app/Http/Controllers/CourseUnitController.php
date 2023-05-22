<?php


/**
type Unit

1 =     text
2 =     file ([pdf,image])
3 =     web_url
4 =     vidcon_link

*/



namespace App\Http\Controllers;

use App\CourseModule;
use App\CourseUnit;
use App\CourseUnitComplete;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($course_module_id = null)
    {
        $data['dataCourseModules'] = CourseModule::find($course_module_id);
        $data['courseUnits'] = CourseUnit::all()
            ->transform(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name . ' - ' . $unit->content,
                ];
            });

        $next   = CourseUnit::where('course_module_id', $course_module_id)
            ->select('order_course_units')
            ->orderBy('order_course_units','desc')
            ->first();
        if($next){
            $data['orderNext'] = $next->order_course_units + 1;
        }
        else{

            $data['orderNext'] = 1;
        }

        return Inertia::render('CourseUnits/Create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(\Illuminate\Http\Request $request)
    {

        $dataCourseModule = CourseModule::find($request->course_module_id);

        // dd($request->all());

        if ($request->create_unit == 'new') {

            if($request->type_course_units == '1'){
                $rules = [
                    'text'              => ['required'],
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'text.required'              => 'Text harus diisi',
                    'unit_is_active.required'   => 'Status tidak boleh kosong',
                    'name.required'   => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                ];

                $content = $request->text;

            }
            elseif($request->type_course_units == '2'){

                //echo "file";
                //exit();

                $rules = [
                    'file_name'         => ['mimes:pdf,xls,xlsx,doc,docx,ppt,pptx', 'required'],
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'file_name.mimes'           => 'Format file yang boleh diupload hanya pdf, doc, xls, ppt',
                    'unit_is_active.required'   => 'Status tidak boleh kosong',
                    'name.required'   => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                ];
                //dd($content); exit();
            }

            elseif($request->type_course_units == '3'){
                $rules = [
                    'web_url'           => ['required'],
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'web_url'                   => 'Web URL Link harus diisi',
                    'unit_is_active.required'   => 'Status tidak boleh kosong',
                    'name.required'   => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                ];
                $content = $request->web_url;
            }

            elseif($request->type_course_units == '4'){
                $rules = [
                    'vidcon_link'           => ['required'],
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'vidcon_link'                   => 'Video Conference Link harus diisi',
                    'unit_is_active.required'       => 'Status tidak boleh kosong',
                    'name.required'                 => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                ];
                $content = $request->vidcon_link;
            }
            elseif($request->type_course_units == '5'){
                $rules = [
                    'youtube'           => ['required'],
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'youtube'                       => 'Url Video Youtube harus diisi',
                    'unit_is_active.required'       => 'Status tidak boleh kosong',
                    'name.required'                 => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                ];
                $content = $request->youtube;
            }
            elseif($request->type_course_units == '6'){

                //echo "file";
                //exit();

                $rules = [
                    'file_name_img'     => ['mimes:jpg,jpeg,png', 'required'],
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'file_name_img.mimes'          => 'Format file yang boleh diupload hanya jpg, jpeg, png',
                    'unit_is_active.required'      => 'Status tidak boleh kosong',
                    'name.required'                => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'  => 'Urutan Materi tidak boleh kosong',
                ];
                //dd($content); exit();
            }
            else{
                $content = null;
            }


            //var_dump();exit();
            //dd($request->validate($rules, $messages));exit();
            $request->validate($rules, $messages);

            if($request->type_course_units == '2'){
                $content_extension  =   $request->file('file_name')->extension();
                $content            =   $request->hasFile('file_name') ? CourseUnit::setFile($request->file_name) : null;
            }
            elseif($request->type_course_units == '6'){
                $content_extension  =   $request->file('file_name_img')->extension();
                $content            =   $request->hasFile('file_name_img') ? CourseUnit::setFile($request->file_name_img) : null;
            }
            else{
                $content            = $content;
                $content_extension  = "";
            }

            $query = CourseUnit::create([
                'name'              => $request->name,
                'type_course_units' => $request->type_course_units,
                'content'           => $content,
                'content_extension' => $content_extension,
                'is_active'         => $request->unit_is_active,
                'course_module_id'  => $request->course_module_id,
                'order_course_units'  => $request->order_course_units,
                'created_by'        => Auth::id(),
                'updated_by'        => Auth::id(),
            ]);


           // dd($query->toSql());
            //exit();
            return Redirect::route('course-modules.get_by_course', $dataCourseModule->course_id)->with('success', 'Course Unit berhasil dibuat.');
            //return Redirect::back()->with('success', 'Materi berhasil ditambahkan.');
        } elseif ($request->create_unit == 'old') {
            $rules = [
                'unit_is_active'    => ['required', 'max:255'],
                'name'              => ['required'],
                'order_course_units'              => ['required'],
                'course_unit_id' => ['required'],
            ];
            $messages = [
                'unit_is_active.required'   => 'Status tidak boleh kosong',
                'name.required'   => 'Nama Materi tidak boleh kosong',
                'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                'course_unit_id.required' => 'Materi harus dipilih',
            ];
            $request->validate($rules, $messages);

            $oldUnit = CourseUnit::findOrFail($request->course_unit_id);
            // dd($oldUnit);

            $newUnit = CourseUnit::create([
                'name'              => $request->name,
                'type_course_units' => $oldUnit->type_course_units,
                'content'           => $oldUnit->content,
                'content_extension' => $oldUnit->content_extension,
                'is_active'         => $request->unit_is_active,
                'course_module_id'  => $request->course_module_id,
                'order_course_units'  => $request->order_course_units,
                'created_by'        => Auth::id(),
                'updated_by'        => Auth::id(),
            ]);

            return Redirect::route('course-modules.get_by_course', $dataCourseModule->course_id)->with('success', 'Course Unit berhasil dibuat.');

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CourseUnit  $courseUnit
     * @return \Illuminate\Http\Response
     */
    public function show(CourseUnit $courseUnit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CourseUnit  $courseUnit
     * @return \Illuminate\Http\Response
     */
    public function edit(\Illuminate\Http\Request $request,$courseUnitId)
    {


        $data['dataCourseUnit']     = CourseUnit::find($courseUnitId);
        $data['dataCourseModules']  = CourseModule::find($data['dataCourseUnit']->course_module_id);

        if($request){
            $data['fromLink'] = "see";
        }else{
            $data['fromLink'] = null;
        }

        if($data['dataCourseUnit']->type_course_units == '1'){
            $data['show_text']          = true;
            $data['show_web_url']       = false;
            $data['show_file_name']     = false;
            $data['show_file_name_img'] = false;
            $data['show_vidcon_link']   = false;
        }
        elseif($data['dataCourseUnit']->type_course_units == '2'){
            $data['show_text']          = false;
            $data['show_web_url']       = false;
            $data['show_file_name']     = true;
            $data['show_file_name_img'] = false;
            $data['show_vidcon_link']   = false;
        }
        elseif($data['dataCourseUnit']->type_course_units == '3'){
            $data['show_text']          = false;
            $data['show_web_url']       = true;
            $data['show_file_name']     = false;
            $data['show_file_name_img'] = false;
            $data['show_vidcon_link']   = false;
        }
        elseif($data['dataCourseUnit']->type_course_units == '4'){
            $data['show_text']          = false;
            $data['show_web_url']       = false;
            $data['show_file_name']     = false;
            $data['show_file_name_img'] = false;
            $data['show_vidcon_link']   = true;
        }
        elseif($data['dataCourseUnit']->type_course_units == '5'){
            $data['show_text']          = false;
            $data['show_web_url']       = false;
            $data['show_file_name']     = false;
            $data['show_file_name_img'] = false;
            $data['show_vidcon_link']   = false;
            $data['show_youtube']       = true;
        }
        elseif($data['dataCourseUnit']->type_course_units == '6'){
            $data['show_text']          = false;
            $data['show_web_url']       = false;
            $data['show_file_name']     = false;
            $data['show_file_name_img'] = true;
            $data['show_vidcon_link']   = false;
            $data['show_youtube']       = false;
        }

        return Inertia::render('CourseUnits/Edit', $data);
    }

    public function update(\Illuminate\Http\Request $request, CourseUnit $courseUnit)
    {

        //dd($request);
       // var_dump($courseUnit->id);exit();
        $dataCourseModule   = CourseModule::find($courseUnit->course_module_id);


        if($request->type_course_units == '1'){
            $rules = [
                'text'              => ['required'],
                'unit_is_active'    => ['required', 'max:255'],
                'name'              => ['required'],
                'order_course_units'              => ['required'],
            ];
            $messages = [
                'text.required'              => 'Text harus diisi',
                'unit_is_active.required'   => 'Status tidak boleh kosong',
                'name.required'   => 'Nama Materi tidak boleh kosong',
                'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
            ];

            $content = $request->text;

        }
        elseif($request->type_course_units == '2'){

            //echo "file";
            //exit();
            if($request->hasFile('file_name')){
                $rules = [
                    'file_name'         => ['mimes:pdf,xls,xlsx,doc,docx,ppt,pptx', 'required'],
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'file_name.mimes'           => 'Format file yang boleh diupload hanya pdf, doc, xls, ppt',
                    'unit_is_active.required'   => 'Status tidak boleh kosong',
                    'name.required'   => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                ];
            }else{
                $rules = [
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'unit_is_active.required'   => 'Status tidak boleh kosong',
                    'name.required'   => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                ];
            }

            //dd($content); exit();
        }
        elseif($request->type_course_units == '3'){

            $rules = [
                'web_url'           => ['required'],
                'unit_is_active'    => ['required', 'max:255'],
                'name'              => ['required'],
                'order_course_units'              => ['required'],
            ];
            $messages = [
                'web_url'                   => 'Web URL Link harus diisi',
                'unit_is_active.required'   => 'Status tidak boleh kosong',
                'name.required'   => 'Nama Materi tidak boleh kosong',
                'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
            ];
            $content = $request->web_url;
        }

        elseif($request->type_course_units == '4'){
            $rules = [
                'vidcon_link'           => ['required'],
                'unit_is_active'    => ['required', 'max:255'],
                'name'              => ['required'],
                'order_course_units'              => ['required'],
            ];
            $messages = [
                'vidcon_link'                   => 'Video Conference Link harus diisi',
                'unit_is_active.required'       => 'Status tidak boleh kosong',
                'name.required'                 => 'Nama Materi tidak boleh kosong',
                'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
            ];
            $content = $request->vidcon_link;
        }
        elseif($request->type_course_units == '5'){
            $rules = [
                'youtube'           => ['required'],
                'unit_is_active'    => ['required', 'max:255'],
                'name'              => ['required'],
                'order_course_units'              => ['required'],
            ];
            $messages = [
                'youtube'                       => 'Url Video Youtube harus diisi',
                'unit_is_active.required'       => 'Status tidak boleh kosong',
                'name.required'                 => 'Nama Materi tidak boleh kosong',
                'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
            ];
            $content = $request->youtube;
        }
        elseif($request->type_course_units == '6'){

            if($request->hasFile('file_name_img')){
                $rules = [
                    'file_name_img'     => ['mimes:jpg,jpeg,png', 'required'],
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'file_name_img.mimes'           => 'Format file yang boleh diupload hanya jpg, jpeg, png',
                    'unit_is_active.required'       => 'Status tidak boleh kosong',
                    'name.required'                 => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                ];
            }else{
                $rules = [
                    'unit_is_active'    => ['required', 'max:255'],
                    'name'              => ['required'],
                    'order_course_units'              => ['required'],
                ];
                $messages = [
                    'unit_is_active.required'   => 'Status tidak boleh kosong',
                    'name.required'   => 'Nama Materi tidak boleh kosong',
                    'order_course_units.required'   => 'Urutan Materi tidak boleh kosong',
                ];
            }
        }
        else{
            $content = null;
        }



        $content_extension  = "";
        //var_dump();exit();
        //dd($request->validate($rules, $messages));exit();
        $request->validate($rules, $messages);

        if($request->type_course_units == '2'){
            if($request->hasFile('file_name')){
                $content_extension  =   $request->file('file_name')->extension();
                $content = $request->hasFile('file_name') ? CourseUnit::setFile($request->file_name) : null;
            }else{
                $content = $courseUnit->content;
            }
        }
        if($request->type_course_units == '6'){
            if($request->hasFile('file_name_img')){
                $content_extension  =   $request->file('file_name_img')->extension();
                $content = $request->hasFile('file_name_img') ? CourseUnit::setFile($request->file_name_img) : null;
            }else{
                $content = $courseUnit->content;
            }
        }
        else{
            $content = $content;
        }

        CourseUnit::where('id', $courseUnit->id)->update([
            'name'              => $request->name,
            'type_course_units' => $request->type_course_units,
            'order_course_units'  => $request->order_course_units,
            'content'           => $content,
            'is_active'         => $request->unit_is_active,
            'course_module_id'  => $request->course_module_id,
            'content_extension' => $content_extension,
            'created_by'        => Auth::id(),
            'updated_by'        => Auth::id(),
        ]);

        if($request->fromLink == 'see'){
            return Redirect::route('course-unit.see', $courseUnit->id)->with('success', 'Materi berhasil diedit.');

        }
        else{
            return Redirect::route('course-modules.get_by_course', $dataCourseModule->course_id)->with('success', 'Course Unit berhasil diedit.');

        }
    }


    public function see($courseUnitId)
    {
        //dd(Auth);



        $data['dataCourseUnit']     = CourseUnit::find($courseUnitId);
        if( $data['dataCourseUnit']->type_course_units == '5'){
            $explode = explode("v=",$data['dataCourseUnit']->content);

            //dd(is_array($explode));

            if(!isset($explode[1])){
                $data['embedYoutube'] = "";
            }
            else{
                $explode = substr($explode[1],0,11);
                $data['embedYoutube'] = "https://www.youtube.com/embed/".$explode."?rel=0";

            }

        }
        else{
            $data['embedYoutube'] = "";
        }


        if( $data['dataCourseUnit']->type_course_units == '2'){
            if(course_unit_show_width_google($data['dataCourseUnit']->content_extension)){

                /// if server Online
                $contentPath = url('/')."/files/course_units/".$data['dataCourseUnit']->content;
                //error_log($contentPath);
                /// if server No Online
            //     $contentPath = "http://schoolmap.dindikptk.net/sipptendik/modul/verval/fileEkspor/RekapSIPP106.xlsx";
               //  http://view.officeapps.live.com/op/view.aspx?src=[OFFICE_FILE_URL]
                //$data['showGoogle'] = "https://drive.google.com/viewerng/viewer?url=".$contentPath."&hl=en&pid=explorer&efh=false&a=v&chrome=false&embedded=true";
                $data['showGoogle'] = "https://view.officeapps.live.com/op/embed.aspx?src=".$contentPath;
                $data['showPdf'] = "";
            }
            else{
                $content = url('/')."/files/course_units/".$data['dataCourseUnit']->content;
                //error_log($content);
                // if server Online
                //$data['showPdf'] = "http://docs.google.com/gview?url=".$content."&embedded=true";
                $data['showPdf'] = " https://docs.google.com/viewer?url=".$content."&embedded=true";
               
                // if server Offline
                //$data['showPdf'] = "http://docs.google.com/gview?url=http://infolab.stanford.edu/pub/papers/google.pdf&embedded=true";
              //  $data['showPdf'] = " https://docs.google.com/viewer?url=http://infolab.stanford.edu/pub/papers/google.pdf&embedded=true";
                // dd($data['showPdf']);
                // dd(url('/'));
                $data['showGoogle'] = "";
            }
        }
        else{
            $data['showPdf'] = "";
            $data['showGoogle'] = "";
        }

        $data['dataCourseUnitComplete']   = CourseUnitComplete::where('course_unit_id', $courseUnitId)
                ->select(CourseUnitComplete::raw('DATE_FORMAT(date_start, "%d-%m-%Y %H:%i") as date_start_indonesia'))
                ->select(CourseUnitComplete::raw('DATE_FORMAT(date_complete, "%d-%m-%Y %H:%i") as date_complete_indonesia'))
                ->where('user_id', Auth::id())
                ->first();

        if(!$data['dataCourseUnitComplete']){
            $this->store_course_unit_complete(Auth::id(),$courseUnitId);
        }

        $data['dataCourseModules']  = CourseModule::find($data['dataCourseUnit']->course_module_id);


        /// from helpers
        if(checkAccessCourse( $data['dataCourseModules']->course_id, Auth::id()) ===  false){
            return Redirect::route('courses.index');
        };

        $typeUnit   = "";

        $prevUnit   = CourseUnit::where('course_module_id', $data['dataCourseModules']->id)
        ->where('is_active','1')
        ->where('order_course_units','<',$data['dataCourseUnit']->order_course_units)
        ->orderBy('order_course_units','DESC')
        ->first();

        if($prevUnit){
            $data['prevUnit']  =  $prevUnit->id;


            /// if link vid conference
            if($data['dataCourseUnit']->type_course_units != 4){

                if(Auth::guard()->user()->usertype_id == '2'){

                    $cekPrevUnitNotComplete = CourseUnitComplete::select('*')
                    ->where('user_id', Auth::id())
                    ->where('course_unit_id',  $prevUnit->id)
                    ->first();

                    //dd( $cekPrevUnitNotComplete->date_complete );

                    if($cekPrevUnitNotComplete){
                        if(!$cekPrevUnitNotComplete->date_complete){
                            $data['cekPrevUnitNotComplete']     = $prevUnit->id;
                            $data['namePrevUnitNotComplete']    = $prevUnit->name;
                        }
                        else{
                            $data['cekPrevUnitNotComplete']     = 0;
                            $data['namePrevUnitNotComplete']    = "";
                        }

                    }
                    else{
                        $data['cekPrevUnitNotComplete']     = $prevUnit->id;
                        $data['namePrevUnitNotComplete']    = $prevUnit->name;
                    }
                }
                else{
                    $data['cekPrevUnitNotComplete']     = 0;
                    $data['namePrevUnitNotComplete']    = "";
                }

            }
            else{
                $data['cekPrevUnitNotComplete']     = 0;
                $data['namePrevUnitNotComplete']    = "";
            }

        }
        else{
            $data['prevUnit']  =   0;
            $data['cekPrevUnitNotComplete'] = 0;
            $data['namePrevUnitNotComplete']= "";
        }


        $queryNextUnit   = CourseUnit::where('course_module_id', $data['dataCourseModules']->id)
        ->where('is_active','1')
        ->where('order_course_units','>',$data['dataCourseUnit']->order_course_units)
         ->orderBy('order_course_units','asc')
        ->first();

        if($data['dataCourseUnit']->type_course_units != 4){

            if(Auth::guard()->user()->usertype_id == '2'){
                if($data['dataCourseUnitComplete']){
                    if($data['dataCourseUnitComplete']->date_complete_indonesia != ''){

                        if($queryNextUnit){
                            $data['nextUnit']  =  $queryNextUnit->id;
                        }
                        else{
                            $data['nextUnit']  =  0;
                        }

                    }
                    else{
                        $data['nextUnit']  =  0;
                    }
                }
                else{
                    $data['nextUnit']  =   0;
                }

            }
            else{
                if( $queryNextUnit){
                    $data['nextUnit']  =  $queryNextUnit->id;
                }
                else{
                    $data['nextUnit']  =  0;
                }
            }

        }
        else{
            if( $queryNextUnit){
                $data['nextUnit']  =  $queryNextUnit->id;
            }
            else{
                $data['nextUnit']  =  0;
            }

        }


        return Inertia::render('CourseUnits/See', $data);
    }

    public function store_course_unit_complete($user_id,$course_unit_id)
    {
        CourseUnitComplete::create([
            'course_unit_id'        => $course_unit_id,
            'user_id'               => $user_id,
            'date_start'            => date('Y-m-d H:i:s')
        ]);

    }

    public function complete(\Illuminate\Http\Request $request, $course_unit_id)
    {
        CourseUnitComplete::where('course_unit_id', $course_unit_id)
        ->where('user_id', Auth::id())
        ->update([
            'date_complete'           => date("Y-m-d H:i:s")
        ]);

        return Redirect::route('course-unit.see', $course_unit_id);

    }
    public function cancel_complete(\Illuminate\Http\Request $request, $course_unit_id)
    {
        CourseUnitComplete::where('course_unit_id', $course_unit_id)
        ->where('user_id', Auth::id())
        ->update([
            'date_complete'           => null
        ]);

        return Redirect::route('course-unit.see', $course_unit_id);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CourseUnit  $courseUnit
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CourseUnit  $courseUnit
     * @return \Illuminate\Http\Response
     */
    public function destroy(CourseUnit $course_unit_id)
    {
        $dataCourseModule = CourseModule::find($course_unit_id->course_module_id);
        $course_unit_id->delete();

        return Redirect::route('course-modules.get_by_course', $dataCourseModule->course_id)->with('success', 'Course Unit berhasil dihapus.');
    }
}
