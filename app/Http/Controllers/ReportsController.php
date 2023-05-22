<?php

namespace App\Http\Controllers;

use DB;
use App\Rapor;
use App\Subject;
use App\Group;
use App\User;
use App\Course;
use App\Category;
use Inertia\Inertia;
use Carbon\Carbon;
use App\UserAccess;
use Excel;
use PDF;
use App\Exports\RaporExport;
use App\Exports\RaporExportSiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class ReportsController extends Controller
{

    public function __invoke()
    {
        return Inertia::render('Reports/Index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function reportGuru(\Illuminate\Http\Request $request)
    {
        $arrayRole = [1, 3, 4]; // 1 = administrator, 3 = guru, 4 = admin
        if (!in_array(Auth::user()->usertype_id, $arrayRole))
            return Redirect::back()->with('error', 'Halaman ini hanya bisa diakses oleh Admin dan Guru.');

        $data['matkul'] = Subject::select(DB::raw("CONCAT_WS(' - ', name,subject_code) AS namecode"), 'id')->where('is_active', 1)->orderBy('name')->get();
        $data['kelas'] = Group::where('is_active', 1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'classes' => $group->classes . " " . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')',
                ];
            });

        if (!empty($request->subject && $request->kelas)) {
            $data['subject'] = Subject::find($request->subject);
            $data['group'] = Group::find($request->kelas);

            $data['report'] = Rapor::leftJoin('courses', 'courses.id', '=', 'rapor.course_id')
                ->where('courses.subject_id', $data['subject']->id)
                ->where('courses.group_id', $data['group']->id)
                ->orderBy('nama')
                ->groupBy('rapor.id')
                ->get();
            // $data['info'] = Rapor::select('subject', 'kelas', 'course_id')
            //     ->where('subject', $data['subject']->name)
            //     ->where('kelas', $data['group']->classes)
            //     ->get();
            $data['info'] = Rapor::select('rapor.subject', 'rapor.kelas', 'rapor.course_id', 'courses.subject_id', 'courses.group_id')
                ->leftJoin('courses', 'courses.id', '=', 'rapor.course_id')
                ->where('courses.subject_id', $data['subject']->id)
                ->where('courses.group_id', $data['group']->id)
                ->get();
        }
        // dd($data['info']);

        return Inertia::render('Reports/Guru', $data);
    }

    public function reportMurid(\Illuminate\Http\Request $request)
    {
        $arrayRole = [1, 3, 4]; // 1 = administrator, 3 = guru, 4 = admin
        if (!in_array(Auth::user()->usertype_id, $arrayRole))
            return Redirect::back()->with('error', 'Halaman ini hanya bisa diakses oleh Admin dan Guru.');

        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $data['murid'] = User::where('usertype_id', '=', '2')->where('is_active', 1)->whereIn('id', $access)->orderBy('fullname')->get();
        $data['kelas'] = Group::where('is_active', 1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'classes' => $group->classes . " " . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')',
                ];
            });

        if (!empty($request->murid && $request->kelas)) {
            $data['siswa'] = User::find($request->murid);
            $data['group'] = Group::find($request->kelas);
            // dd($request);

            // $data['kelas'] = Group::select('groups.classes', 'groups.id')->join('group_users', 'group_users.group_id', '=', 'groups.id')
            //                         ->where('group_users.user_id', $data['siswa']->id)
            //                         ->orderBy('groups.classes')->get();
            $data['report'] = Rapor::join('courses', 'courses.id', '=', 'rapor.course_id')
                ->where('rapor.nama', $data['siswa']->fullname)
                ->where('courses.group_id', $data['group']->id)
                ->groupBy('rapor.id')
                ->get();
            $data['info'] = Rapor::select('nama', 'kelas', 'course_id')
                ->join('courses', 'courses.id', '=', 'rapor.course_id')
                ->join('groups', 'groups.id', '=', 'courses.group_id')
                ->where('rapor.nama', $data['siswa']->fullname)
                ->where('groups.id', $data['group']->id)
                ->get();
            $data['infokelas'] = Group::where('id', $data['group']->id)->get()
                ->transform(function ($group) {
                    $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                    return [
                        'id' => $group->id,
                        'classes' => $group->classes . " " . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')',
                    ];
                });
            $data['kelasreport'] = Rapor::join('courses', 'courses.id', '=', 'rapor.course_id')->where('rapor.nama', $data['siswa']->fullname)->where('group_id', $data['group']->id)->pluck('courses.id')->first();
            // dd($data['info'][0]['course_id']);
        }
        // dd($data['rapor']);

        return Inertia::render('Reports/Murid', $data);
    }

    public function reportOrtu(\Illuminate\Http\Request $request)
    {
        // dd($request);
        if (Auth::user()->usertype_id != 5)
            return Redirect::back()->with('error', 'Halaman ini hanya bisa diakses oleh orang tua.');

        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $data['children'] = User::with(['group_users.group'])
            ->whereHas('group_users.activeGroup')
            ->where('parent_id', Auth::id())
            ->where('is_active', 1)
            ->whereIn('id', $access)
            ->get();
        $data['kelas'] = Group::where('is_active', 1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'classes' => $group->classes . " " . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')',
                ];
            });

        if (!empty($request->id_anak) && $request->kelas) {
            $data['anak'] = User::find($request->id_anak);
            $data['kelasanak'] = Group::find($request->kelas);

            $data['report'] = Rapor::join('courses', 'courses.id', '=', 'rapor.course_id')
                ->where('nim', $data['anak']->username)
                ->where('courses.group_id', $data['kelasanak']->id)
                ->groupBy('rapor.id')
                ->get();
            $data['info'] = Rapor::select('nim', 'nama', 'kelas', 'walikelas', 'course_id')
                ->join('courses', 'courses.id', '=', 'rapor.course_id')
                ->join('groups', 'groups.id', '=', 'courses.group_id')
                ->where('rapor.nama', $data['anak']->fullname)
                ->get();
            $data['infokelas'] = Rapor::join('courses', 'courses.id', '=', 'rapor.course_id')->where('rapor.nama', $data['anak']->fullname)->where('group_id', $data['kelasanak']->id)->pluck('courses.id')->first();
            // dd($data['infokelas']);
        }
        // dd($data['kelas']);

        return Inertia::render('Reports/Ortu', $data);
    }

    public function reportSiswa(\Illuminate\Http\Request $request)
    {
        if (Auth::user()->usertype_id != 2)
            return Redirect::back()->with('error', 'Halaman ini hanya bisa diakses oleh murid.');
        $user = Auth::user()->username;
        $fullname = Auth::user()->fullname;
        $murid = Auth::user()->id;

        $data['kelas'] = Group::select('groups.id', 'groups.classes', 'groups.huruf', 'groups.kel_kelas', 'groups.academicterms', 'groups.year')
            ->join('group_users', 'groups.id', '=', 'group_users.group_id')
            ->where('group_users.user_id', $murid)
            ->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'classes' => $group->classes . " " . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')',
                ];
            });

        if (!empty($request->kelas)) {
            $data['group'] = Group::find($request->kelas);
            // dd($fullname);
            $data['report'] = Rapor::join('courses', 'courses.id', '=', 'rapor.course_id')
                ->where('rapor.nama', $fullname)
                ->where('courses.group_id', $data['group']->id)
                ->groupBy('rapor.id')
                ->get();
            $data['info'] = Rapor::select('nama', 'kelas', 'course_id')
                ->join('courses', 'courses.id', '=', 'rapor.course_id')
                ->join('groups', 'groups.id', '=', 'courses.group_id')
                ->where('rapor.nim', $user)
                ->where('groups.id', $data['group']->id)
                ->get();
            $data['infokelas'] = Rapor::join('courses', 'courses.id', '=', 'rapor.course_id')->where('rapor.nim', $user)->where('group_id', $data['group']->id)->pluck('courses.id')->first();
            // dd($data['report']);
        }
        // }
        // dd($info[0]['walikelas']);

        return Inertia::render('Reports/Siswa', $data);
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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rapor  $rapor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Level $level)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rapor  $rapor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rapor $rapor)
    {
        //
    }

    public function restore()
    {
    }

    public function exportsubject(\Illuminate\Http\Request $request)
    {
        $subid = $request[0];
        $kelid = $request[1];
        $subject = Subject::where('id', $subid)->pluck('name')->first();
        $kelas = Group::where('id', $kelid)->pluck('classes')->first();
        // dd($request);
        return Excel::download(new RaporExport($subject, $kelas), 'rapor-subject.xlsx');
    }
    public function exportsiswa(\Illuminate\Http\Request $request)
    {
        $siswa = $request[0];
        $kelas = $request[1];
        return Excel::download(new RaporExportSiswa($siswa, $kelas), 'rapor-siswa.xlsx');
    }

    public function exportsiswapdf(\Illuminate\Http\Request $request)
    {
        $siswa = $request[0];
        $kelas = $request[1];
        $course = $request[2];
        //data rapor utama
        $raporkelas = Course::where('id', $course)->pluck('group_id')->first();
        $groupid = Group::where('id', $raporkelas)->pluck('category_id')->first();
        $data['kategori'] = Category::where('id', $groupid)->pluck('title')->first();
        $data['rapor'] = Rapor::leftJoin('subjects', 'subjects.name', '=', 'rapor.subject')
            ->leftJoin('courses', 'courses.id', '=', 'rapor.course_id')
            ->where('rapor.nama', $siswa)
            ->where('courses.group_id', $raporkelas)
            ->groupBy('rapor.id')
            ->get();
        $data['nama'] = Rapor::where('nama', $siswa)->pluck('nama')->first();
        $data['nim'] = Rapor::where('nama', $siswa)->pluck('nim')->first();
        $data['kelas'] = Rapor::where('nama', $siswa)->pluck('kelas')->first();
        $data['tahun'] = Group::where('classes', $kelas)->pluck('year')->first();
        $sem = Group::join('courses', 'courses.group_id', '=', 'groups.id')->where('groups.classes', $kelas)->where('courses.id', $course)->pluck('academicterms')->first();
        if ($sem == 1) {
            $data['semester'] = 'Ganjil';
        } else {
            $data['semester'] = 'Genap';
        }

        $userid = User::where('username', $data['nim'])->pluck('id')->first();
        $groupmatch = Group::select('academicterms', 'year')->where('id', $data['rapor']->first()['group_id'])->first();

        Carbon::setLocale('id');
        $data['tanggal'] = Carbon::now()->isoFormat('D MMMM Y');
        $pdf = PDF::loadview('reportsiswa', $data)->setPaper('a2');
        return $pdf->download('report-siswa.pdf');
        // dd($return);

    }
    public function exportsiswapdftes(\Illuminate\Http\Request $request)
    {
        $siswa = $request[0];
        $kelas = $request[1];
        $course = $request[2];
        ///data rapor utama
        $raporkelas = Course::where('id', $course)->pluck('group_id')->first();
        $groupid = Group::where('id', $raporkelas)->pluck('category_id')->first();

        $data['kategori'] = Category::where('id', $groupid)->pluck('title')->first();
        $data['rapor'] = Rapor::leftJoin('subjects', 'subjects.name', '=', 'rapor.subject')
            ->leftJoin('courses', 'courses.id', '=', 'rapor.course_id')
            ->where('rapor.nama', $siswa)
            ->where('courses.group_id', $raporkelas)
            ->groupBy('rapor.id')
            ->get();
        $data['nama'] = Rapor::where('nama', $siswa)->pluck('nama')->first();
        $data['nim'] = Rapor::where('nama', $siswa)->pluck('nim')->first();
        $data['kelas'] = Rapor::where('nama', $siswa)->pluck('kelas')->first();
        $data['tahun'] = Group::where('classes', $kelas)->pluck('year')->first();
        $sem = Group::join('courses', 'courses.group_id', '=', 'groups.id')->where('groups.classes', $kelas)->where('courses.id', $course)->pluck('academicterms')->first();
        if ($sem == 1) {
            $data['semester'] = 'Ganjil';
        } else {
            $data['semester'] = 'Genap';
        }

        $userid = User::where('username', $data['nim'])->pluck('id')->first();
        $groupmatch = Group::select('academicterms', 'year')->where('id', $data['rapor']->first()['group_id'])->first();
        Carbon::setLocale('id');
        $data['tanggal'] = Carbon::now()->isoFormat('D MMMM Y');
        return view('reportsiswa', $data);
    }
}
