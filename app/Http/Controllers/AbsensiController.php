<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Presences;
use App\Group;
use App\GroupUser;
use App\Course;
use App\CourseModule;
use App\User;
use App\Subject;
use App\UserAccess;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use App\Exports\RekapAbsensiExport;
use App\Exports\RekapAbsensiGuruExport;
use DB;
use Excel;

class AbsensiController extends Controller
{
    public function absensiMurid(Request $request)
    {
        if (Auth::user()->usertype_id != 2)
            return Redirect::back()->with('error', 'Only students can access the page');
        $user_id = Auth::id();
        $arrKelasId = GroupUser::where('user_id', Auth::id())->pluck('group_id');
        $data['presences'] = [];
        $data['tanggal'] = [];
        $data['kelas'] = Group::select('groups.id', 'groups.classes', 'groups.huruf', 'groups.kel_kelas', 'groups.academicterms', 'groups.year')
                        ->join('group_users', 'groups.id', '=', 'group_users.group_id')
                        ->where('group_users.user_id',$user_id)
                        ->where('groups.is_active',1)
                        ->where('group_users.is_active',1)->get()
                        ->transform(function ($group) {
                            $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                            return [
                                'id' => $group->id,
                                'classes' => $group->classes. " " .$group->huruf." ".$group->kel_kelas.' ('. $periode .' ' .$group->year. ')',
                            ];
                        });

        if (!empty($request->all())) {

            $tgl_mulai = $request->tgl_mulai == null ? date('Y-m-d') : $request->tgl_mulai;
            $tgl_akhir = $request->tgl_akhir == null ? date('Y-m-d 23:59:59') : $request->tgl_akhir . " 23:59:59";
            $data['group'] = Group::find($request->kelas);
            $idgroup = Group::find($request->kelas);
// dd($data['group']->id);
            $data['tanggal'] = [
                'tgl_mulai' => $tgl_mulai,
                'tgl_akhir' => explode(' ', $tgl_akhir)[0],
            ];

            $data['presences'] = Presences::with(['user', 'courseModule'])
                ->leftJoin('course_modules','course_modules.id','=','presences.coursemodule_id')
                ->leftJoin('courses','courses.id','=','course_modules.course_id')
                ->when($request->kelas != null, function ($q)  use ($idgroup) {
                    return $q->where('courses.group_id',$idgroup->id);
                })
                ->when($request->kelas == null, function ($q)  use ($arrKelasId) {
                    return $q->whereIn('courses.group_id', $arrKelasId);
                })
                // ->where('courses.group_id', $data['group']->id)
                ->where('student_user_id', $user_id)
                ->whereBetween('date_complete', [$tgl_mulai, $tgl_akhir])
                ->get()
                ->transform(function ($presence) {
                    $jam_course_module = explode(' ', $presence->courseModule->actual_start_at)[1];
                    //CourseModule::raw('DATE_FORMAT(date_complete, "%d-%m-%Y %H:%i") as date_complete_indonesia');
                    // $presence_time1 = date('Y-m-d', strtotime($presence->courseModule->presence_time));
                    $jam_absen = explode(' ', $presence->date_complete)[1];
                    $jam_absen1 = date('Y-m-d', strtotime($presence->date_complete));
                    $keterangan = '';

                    // P hadir
                    if ($presence->status == 'P' && strtotime($jam_absen) > strtotime($jam_course_module) + 300) {
                        $keterangan = 'TERLAMBAT';
                    } elseif ($presence->status == 'P' && strtotime($jam_absen) <= strtotime($jam_course_module) + 300) {
                        $keterangan = 'HADIR';
                    } elseif ($presence->status == 'S') {
                        // SAKIT
                        $keterangan = 'SAKIT';
                    } elseif ($presence->status == 'L') {
                        // IZIN leave
                        $keterangan = 'IZIN';
                    } elseif ($presence->status == 'A') {
                        // ALFA tidak ada keterangan
                        $keterangan = 'ALFA';
                    }

                    return [
                        'id' => $presence->id,
                        'jam_course_module' => $jam_course_module,
                         //'presence_time1' => $presence,
                        'jam_absen1'=>$jam_absen1,
                        'jam_absen' => $jam_absen,
                        'presence_time' => $presence->date_complete,
                        'keterangan' => $keterangan,
                        'course' => $presence->courseModule->course,
                        'course_module' => $presence->courseModule,
                        'user' => $presence->user,
                    ];
                });

            $data['datasiswa'] = array(
                'hadir' => count($data['presences']->where('keterangan', 'HADIR')),
                'terlambat' => count($data['presences']->where('keterangan', 'TERLAMBAT')),
                'sakit' => count($data['presences']->where('keterangan', 'SAKIT')),
                'izin' => count($data['presences']->where('keterangan', 'IZIN')),
                'alpha' => count($data['presences']->where('keterangan', 'ALFA')),
            );
            // dd($data['datasiswa']);

            // $data['datasiswa'] = Presences::with(['user', 'courseModule'])
            //     ->leftJoin('course_modules','course_modules.id','=','presences.coursemodule_id')
            //     ->leftJoin('courses','courses.id','=','course_modules.course_id')
            //     ->when($request->kelas != null, function ($q)  use ($idgroup) {
            //         return $q->where('courses.group_id',$idgroup->id);
            //     })
            //     ->when($request->kelas == null, function ($q)  use ($arrKelasId) {
            //         return $q->whereIn('courses.group_id', $arrKelasId);
            //     })
            //     ->where('student_user_id', $user_id)
            //     ->whereBetween('date_complete', [$tgl_mulai, $tgl_akhir])
            //     // ->groupBy('status')
            //     ->groupBy('student_user_id')
            //     ->get()
            //     ->transform(function ($presence) use ($arrKelasId, $request){
            //         // dd($request);
            //         $jam_course_module = explode(' ', $presence->courseModule->actual_start_at)[1];
            //         $jam_absen = explode(' ', $presence->date_complete)[1];
            //         $p = '';
            //         $t = '';
            //         $s = '';
            //         $l = '';
            //         $a = '';
                    
            //         $groupid = $presence->group_id;
            //         $jmsls = strtotime($jam_course_module) + 300;
            //         $absen = $presence->date_complete;
            //         $jamselesai = date("Y-m-d H:i:s", $jmsls);
            //         // dd($absen);

            //         $t = $presence->leftJoin('course_modules','course_modules.id','=','presences.coursemodule_id')->leftJoin('courses','courses.id','=','course_modules.course_id')->when($request->kelas != null, function ($q) use($presence) {return $q->where('courses.group_id',$presence->group_id);})->when($request->kelas == null, function ($q)  use ($arrKelasId) {return $q->wherein('courses.group_id', $arrKelasId);})->where('student_user_id', $presence->student_user_id)->where('status', 'P')->where('presences.date_complete','<=', $presence->courseModule->actual_start_at)->count('presences.id');
            //         $p = $presence->leftJoin('course_modules','course_modules.id','=','presences.coursemodule_id')->leftJoin('courses','courses.id','=','course_modules.course_id')->when($request->kelas != null, function ($q) use($presence) {return $q->where('courses.group_id',$presence->group_id);})->when($request->kelas == null, function ($q)  use ($arrKelasId) {return $q->wherein('courses.group_id', $arrKelasId);})->where('student_user_id', $presence->student_user_id)->where('status', 'P')->where('presences.date_complete','>', $presence->courseModule->actual_start_at)->count('presences.id');
            //         $s = $presence->leftJoin('course_modules','course_modules.id','=','presences.coursemodule_id')->leftJoin('courses','courses.id','=','course_modules.course_id')->when($request->kelas != null, function ($q) use($presence) {return $q->where('courses.group_id',$presence->group_id);})->when($request->kelas == null, function ($q)  use ($arrKelasId) {return $q->wherein('courses.group_id', $arrKelasId);})->where('student_user_id', $presence->student_user_id)->where('status', 'S')->count('presences.id');
            //         $l = $presence->leftJoin('course_modules','course_modules.id','=','presences.coursemodule_id')->leftJoin('courses','courses.id','=','course_modules.course_id')->when($request->kelas != null, function ($q) use($presence) {return $q->where('courses.group_id',$presence->group_id);})->when($request->kelas == null, function ($q)  use ($arrKelasId) {return $q->wherein('courses.group_id', $arrKelasId);})->where('student_user_id', $presence->student_user_id)->where('status', 'L')->count('presences.id');
            //         $a = $presence->leftJoin('course_modules','course_modules.id','=','presences.coursemodule_id')->leftJoin('courses','courses.id','=','course_modules.course_id')->when($request->kelas != null, function ($q) use($presence) {return $q->where('courses.group_id',$presence->group_id);})->when($request->kelas == null, function ($q)  use ($arrKelasId) {return $q->wherein('courses.group_id', $arrKelasId);})->where('student_user_id', $presence->student_user_id)->where('status', 'A')->count('presences.id');
            //         return [
            //             'hadir' => $p,
            //             'terlambat' => $t,
            //             'sakit' => $s,
            //             'izin' => $l,
            //             'alpha' => $a,
            //         ];
            //     });
                // dd($data['datasiswa']);
        }

        return Inertia::render('Absensi/Murid', $data);
    }

    public function absensiOrtu(Request $request)
    {
        if (Auth::user()->usertype_id != 5)
            return Redirect::back()->with('error', 'Only parents can access the page');
        $arrChildrenId = User::where('parent_id', Auth::id())->pluck('id');
        // $arrKelasId = GroupUser::where('user_id', Auth::id())->pluck('group_id');
        $arrKelasId = GroupUser::whereIn('user_id', $arrChildrenId)->pluck('group_id');
// dd($arrKelasId);
        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');
        $data['children'] = User::where('parent_id', Auth::id())
            ->whereIn('id', $access)
            ->get()
            ->transform(function ($child) {
                return [
                    'id' => $child->id,
                    'fullname' => $child->fullname,
                ];
            });
        $data['kelas'] = Group::where('is_active',1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'classes' => $group->classes. " " .$group->huruf." ".$group->kel_kelas.' ('. $periode .' ' .$group->year. ')',
                ];
            });

        $data['selectedChild'] = [];
        $data['presences'] = [];
        $data['tanggal'] = [];

        if (!empty($request->all())) {
            $tgl_mulai = $request->tgl_mulai == null ? date('Y-m-d') : $request->tgl_mulai;
            $tgl_akhir = $request->tgl_akhir == null ? date('Y-m-d 23:59:59') : $request->tgl_akhir . " 23:59:59";
            $kelasanak = Group::find($request->kelas);
            $anakterpilih = User::find($request->id_anak);
            // $arrKelasAnakId = GroupUser::where('user_id', $anakterpilih->id)->pluck('group_id');
            // $arrAnakKelasId = GroupUser::where('group_id', $kelasanak->id)->pluck('user_id');
// dd($arrKelasAnakId);
            $data['selectedChild'] = User::find($request->id_anak);
            $data['tanggal'] = [
                'tgl_mulai' => $tgl_mulai,
                'tgl_akhir' => explode(' ', $tgl_akhir)[0],
            ];

            $data['presences'] = Presences::with(['user', 'courseModule'])
                ->leftJoin('course_modules','course_modules.id','=','presences.coursemodule_id')
                ->leftJoin('courses','courses.id','=','course_modules.course_id')
                ->when($request->id_anak != null, function ($q)  use ($request) {
                    return $q->where('student_user_id', $request->id_anak);
                })
                ->when($request->id_anak == null, function ($q)  use ($arrChildrenId) {
                    return $q->whereIn('student_user_id', $arrChildrenId);
                })
                ->when($request->kelas != null, function ($q)  use ($kelasanak) {
                    return $q->where('courses.group_id',$kelasanak->id);
                })
                ->when($request->kelas == null, function ($q)  use ($arrKelasId) {
                    return $q->whereIn('courses.group_id', $arrKelasId);
                })
               
                // ->whereIn('student_user_id', $arrChildrenId)
                ->whereBetween('date_complete', [$tgl_mulai, $tgl_akhir])
                ->get()
                ->transform(function ($presence) {
                    $jam_course_module = explode(' ', $presence->courseModule->actual_start_at)[1];
                    $jam_absen = explode(' ', $presence->date_complete)[1];
                    $keterangan = '';

                    // P hadir
                    if ($presence->status == 'P' && strtotime($jam_absen) > strtotime($jam_course_module) + 300) {
                        $keterangan = 'TERLAMBAT';
                    } elseif ($presence->status == 'P' && strtotime($jam_absen) <= strtotime($jam_course_module) + 300) {
                        $keterangan = 'HADIR';
                    } elseif ($presence->status == 'S') {
                        // SAKIT
                        $keterangan = 'SAKIT';
                    } elseif ($presence->status == 'L') {
                        // IZIN leave
                        $keterangan = 'IZIN';
                    } elseif ($presence->status == 'A') {
                        // ALFA tidak ada keterangan
                        $keterangan = 'ALFA';
                    }

                    return [
                        'id' => $presence->id,
                        'jam_course_module' => $jam_course_module,
                        'jam_absen' => $jam_absen,
                        'presence_time' => $presence->date_complete,
                        'keterangan' => $keterangan,
                        'course' => $presence->courseModule->course,
                        'course_module' => $presence->courseModule,
                        'user' => $presence->user,
                    ];
                });

            $data['datasiswa'] = array(
                'hadir' => count($data['presences']->where('keterangan', 'HADIR')),
                'terlambat' => count($data['presences']->where('keterangan', 'TERLAMBAT')),
                'sakit' => count($data['presences']->where('keterangan', 'SAKIT')),
                'izin' => count($data['presences']->where('keterangan', 'IZIN')),
                'alpha' => count($data['presences']->where('keterangan', 'ALFA')),
            );
            
        }

        // dd($data['presences']);

        return Inertia::render('Absensi/Ortu', $data);
    }

    public function absensiGuru(Request $request)
    {
        $arrUserType = [2, 3]; // Tipe user 1 = admin, 2 = murid, 3 = guru, 5 = orang tua

        if (!in_array(Auth::user()->usertype_id, [1, 3, 4]))
            return Redirect::back()->with('error', 'Only teachers and admins can access the page');
         
        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');
       
        $data['users'] = User::whereIn('usertype_id', $arrUserType)
            ->whereIn('id', $access)
            ->get()
            ->transform(function ($user) {
                $tipe_user = $user->usertype_id == 2 ? 'Murid' : 'Guru';
                return [
                    'id' => $user->id,
                    'fullname' => $tipe_user . ' - ' . $user->fullname,
                ];
            });
        $data['kelas'] = Group::where('is_active',1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'classes' => $group->classes. " " .$group->huruf." ".$group->kel_kelas.' ('. $periode .' ' .$group->year. ')',
                ];
            });
        $data['subject'] = Subject::where('is_active',1)->get()
            ->transform(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name." ".$subject->subject_code,
                ];
            });
        $data['selectedChild'] = [];
        $data['presences'] = [];
        $data['tanggal'] = [];

        if (!empty($request->all())) {
            $tgl_mulai = $request->tgl_mulai == null ? date('Y-m-d') : $request->tgl_mulai;
            $tgl_akhir = $request->tgl_akhir == null ? date('Y-m-d 23:59:59') : $request->tgl_akhir . " 23:59:59";

            $groupid = Group::find($request->kelas);
            $subjectid = Subject::find($request->subject);
            $data['selectedChild'] = User::find($request->id_anak);
            $data['tanggal'] = [
                'tgl_mulai' => $tgl_mulai,
                'tgl_akhir' => explode(' ', $tgl_akhir)[0],
            ];
            $data['presences'] = Presences::with(['user', 'courseModule'])
                ->leftJoin('course_modules','course_modules.id','=','presences.coursemodule_id')
                ->leftJoin('courses','courses.id','=','course_modules.course_id')
                ->leftJoin('subjects','subjects.id','=','courses.subject_id')
                ->whereHas(
                    'user',
                    function ($q) use ($request) {
                        return $q->when($request->usertype_id != null, function ($query) use ($request) {
                            $query->where('usertype_id', $request->usertype_id);
                        });
                    }
                )
                ->when($request->id_user != null, function ($q)  use ($request) {
                    return $q->where('student_user_id', $request->id_user);
                })
                ->when($request->kelas != null, function ($q)  use ($groupid) {
                    return $q->where('courses.group_id',$groupid->id);
                })
                ->when($request->subject != null, function ($q)  use ($subjectid) {
                    return $q->where('courses.subject_id',$subjectid->id);
                })
                // ->when($request->id_user == null, function ($q)  use ($arrUserType) {
                //     return $q->whereIn('student_user_id', $arrChildrenId);
                // })
                // ->whereIn('student_user_id', $arrChildrenId)
                ->whereBetween('date_complete', [$tgl_mulai, $tgl_akhir])
                ->get()

                ->transform(function ($presence) {

                    $jam_course_module = explode(' ', $presence->courseModule->actual_start_at)[1];
                    $jam_course_module1 = date('Y-m-d', strtotime($presence->courseModule->actual_start_at));
                     $jam_course_module2 = date('H:i:s', strtotime($presence->courseModule->actual_start_at));
                  //if ($presence->courseModule->actual_end_at != null){
                         //$course_module_end = explode(' ', $presence->courseModule->actual_end_at)[1];
                   // }else {
                        //$course_module_end='Absensi berjalan';
                   // }
                    $jam_absen = explode(' ', $presence->date_complete)[1];
                    
                    $keterangan = '';
                    
                  //print_r($course_module_end);
                

                    // P hadir
                    if ($presence->status == 'P' && strtotime($jam_absen) > strtotime($jam_course_module) + 300) {
                        $keterangan = 'TERLAMBAT';
                    } elseif ($presence->status == 'P' && strtotime($jam_absen) <= strtotime($jam_course_module) + 300) {
                        $keterangan = 'HADIR';
                    } elseif ($presence->status == 'S') {
                        // SAKIT
                        $keterangan = 'SAKIT';
                    } elseif ($presence->status == 'L') {
                        // IZIN leave
                        $keterangan = 'IZIN';
                    } elseif ($presence->status == 'A') {
                        // ALFA tidak ada keterangan
                        $keterangan = 'ALFA';
                    } elseif ($presence->status == 'N') {
                        // NON tidak ada jam
                        $keterangan = 'TIDAK ADA JAM';
                    }
                    
                    
                    return [
                        'id' => $presence->id,
                        'jam_course_module1' => $jam_course_module1,
                        'jam_course_module2' => $jam_course_module2,
                        //'course_module_end'=>$course_module_end,
                        'jam_absen' => $jam_absen,
                        'presence_time' => $presence->date_complete,
                        'keterangan' => $keterangan,
                        'course' => $presence->courseModule->course,
                        'course_module' => $presence->courseModule,
                        'subject'=> $presence->courseModule->course->subject,
                        'user' => $presence->user,
                    ];
                });
                //dd($data['presences']);
            // dd($data['tanggal']);
            // dd([$data['presences'], $tgl_mulai, $tgl_akhir]);
        }

       //  print_r($data['presences']);

        return Inertia::render('Absensi/Guru', $data);
    }

    public function exportExcel(Request $request)
    {
        // dd($request->all());
        return Excel::download(new AbsensiExport($request), 'absensi.xlsx');
    }

    public function rekap(\Illuminate\Http\Request $request)
    {
        $data['subjects'] = Subject::select(DB::raw("CONCAT_WS(' - ', name,subject_code) AS namecode"), 'id')->where('is_active', 1)->orderBy('name')->get();
        $data['groups'] = Group::where('is_active', 1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'classes' => $group->classes . " " . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')',
                ];
            });
        if (!empty($request->kelas && $request->matkul)) {
            $data['kelas'] = Group::find($request->kelas);
            $data['matkul'] = Subject::find($request->matkul);


            $tanggalrekap = Presences::select('presences.id', 'course_modules.date')
                            ->join('users','users.id','=','presences.student_user_id')
                            ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                            ->join('courses','courses.id','=','course_modules.course_id')
                            ->where('courses.group_id', $data['kelas']->id)
                            ->where('courses.subject_id', $data['matkul']->id)
                            ->groupBy('course_modules.date')
                            ->get()
                            ->transform(function ($rekap) {
            
                                return [
                                    'id' => $rekap->id,
                                    // 'date' => $rekap->date,
                                    'date' => date('d M', strtotime($rekap->date)),
                                ];
                            });

            $tglrekaps = Presences::select('presences.id', 'users.id as userid', 'users.username as nim', 'users.fullname as nama', 'users.gender')
                    ->join('users','users.id','=','presences.student_user_id')
                    ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                    ->join('courses','courses.id','=','course_modules.course_id')
                    ->where('courses.group_id', $data['kelas']->id)
                    ->where('courses.subject_id', $data['matkul']->id)
                    ->where('users.usertype_id', 2)
                    ->groupBy('users.id')
                    ->orderBy('users.fullname')
                    ->get();

                    $rekaps = [];
                    foreach($tglrekaps as $key => $value){
                        $user = $value->userid;
                        $tglrekap = $value->toArray();

                        $tglrekap['status'] = Presences::select('presences.id', 'course_modules.date', 'users.username', 'users.fullname', 'course_modules.date', 'presences.status')
                            ->join('users','users.id','=','presences.student_user_id')
                            ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                            ->join('courses','courses.id','=','course_modules.course_id')
                            ->where('courses.group_id', $data['kelas']->id)
                            ->where('courses.subject_id', $data['matkul']->id)
                            ->where('users.id', $user)
                            ->groupBy('course_modules.date')
                            ->get()
                            ->transform(function ($rekap){
                                return [
                                    'id' => $rekap->id,
                                    'date' => $rekap->date,
                                    'status' => $rekap->status,
                                ];
                            });
                            $rekaps[] = $tglrekap;
            };
            $data['tanggal'] = $tanggalrekap;
            $data['rekaps'] = $rekaps;
            // dd($data['kelas']);
        }

        return Inertia::render('Absensi/Rekap', $data);

    }

    public function downloadrekap(Request $request)
    {
        // dd($request->all());
        return Excel::download(new RekapAbsensiExport($request), 'REKAP KEHADIRAN.xlsx');
    }

    public function rekapguru(\Illuminate\Http\Request $request){

        $data['tanggaldata'] = [];
        if (!empty($request->all())) {
            $tgl_mulai = $request->tgl_mulai == null ? date('Y-m-d') : $request->tgl_mulai;
            $tgl_akhir = $request->tgl_akhir == null ? date('Y-m-d') : $request->tgl_akhir;
            

            $tanggalrekap = Presences::select('presences.id', 'course_modules.date')
                            ->join('users','users.id','=','presences.student_user_id')
                            ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                            ->join('courses','courses.id','=','course_modules.course_id')
                            ->whereBetween('course_modules.date', [$tgl_mulai, $tgl_akhir])
                            ->groupBy('course_modules.date')
                            ->get()
                            ->transform(function ($rekap) {
            
                                return [
                                    'id' => $rekap->id,
                                    'date' => date('d M', strtotime($rekap->date)),
                                ];
                            });

            $tglrekaps = Presences::select('presences.id', 'users.id as userid', 'users.username as nim', 'users.fullname as nama', 'users.gender')
                        ->join('users','users.id','=','presences.student_user_id')
                        ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                        ->join('courses','courses.id','=','course_modules.course_id')
                        ->where('users.usertype_id', 3)
                        ->whereBetween('course_modules.date', [$tgl_mulai, $tgl_akhir])
                        ->groupBy('users.id')
                        ->orderBy('users.fullname')
                        ->get();
                        $rekaps = [];
                        foreach($tglrekaps as $key => $value){
                            $user = $value->userid;
                            $tglrekap = $value->toArray();

                            $tglrekap['status'] = Presences::
                                // select('presences.id', 'course_modules.date', 'users.username', 'users.fullname', 'course_modules.date', 'presences.status')
                                selectRaw("presences.id, course_modules.date, users.username, users.fullname, course_modules.date, COUNT(CASE WHEN presences.status = 'P' THEN presences.status END) hadir, COUNT(CASE WHEN presences.status = 'L' THEN presences.status END) izin, COUNT(CASE WHEN presences.status = 'S' THEN presences.status END) sakit, COUNT(CASE WHEN presences.status = 'A' THEN presences.status END) alpha")
                                ->join('users','users.id','=','presences.student_user_id')
                                ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                                ->join('courses','courses.id','=','course_modules.course_id')
                                ->where('users.id', $user)
                                ->whereBetween('course_modules.date', [$tgl_mulai, $tgl_akhir])
                                ->groupBy('course_modules.date')
                                ->get()
                                ->transform(function ($rekap){
                                    return [
                                        'id' => $rekap->id,
                                        'date' => $rekap->date,
                                        'status' => $rekap->status,
                                        'hadir' => $rekap->hadir,
                                        'izin' => $rekap->izin,
                                        'sakit' => $rekap->sakit,
                                        'alpha' => $rekap->alpha,
                                    ];
                                });
                                $rekaps[] = $tglrekap;
                };
            $data['tanggaldata'] = [
                'tgl_mulai' => $tgl_mulai,
                'tgl_akhir' => $tgl_akhir,
            ];
            $data['tanggal'] = $tanggalrekap;
            $data['rekaps'] = $rekaps;
        }
        // dd($tanggalrekap);
        
        return Inertia::render('Absensi/RekapGurux', $data);
    }

    public function downloadrekapguru(Request $request)
    {
        $tanggal = array(
            'tgl_mulai' => $request->date_start,
            'tgl_akhir' => $request->date_end,
            // '0' => $request->date_start,
            // '1' => $request->date_end,
        );
        // dd($tanggal);
        return Redirect::route('absensi-downloadrekapguruonly', $tanggal);
    }

    public function downloadrekapguruonly(Request $request)
    {
        // dd($request);
        $data['tanggal'] = array(
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_akhir' => $request->tgl_akhir,
        );

        $tgl_mulai = $request->tgl_mulai;
        $tgl_akhir = $request->tgl_akhir;
        // dd($tanggal['tgl_mulai']);
        
        $base_url = url('/');
        $data['url'] = "$base_url/absensi/downloadrekapguruexcel?tgl_mulai=$tgl_mulai&tgl_akhir=$tgl_akhir" ;
        // $url = "$base_url/absensi/downloadrekapguruexcel?tgl_mulai=$tgl_mulai&tgl_akhir=$tgl_akhir" ;
        // echo "<script>window.open('".$url."', '_blank')</script>";
        // return Redirect::to($url);
        // echo "
        // <script src='//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
        // <script>
        //     $( document ).ready(function() {
        //         window.open(".$url.", '_blank');
        //     });
        // </script>
        // ";
        return Inertia::render('Absensi/RekapGuruDownloadOnly', $data);
        // return view('downloadrekapguru')->with($data);
        // return view('twst');
        // return Redirect::route('absensi-downloadrekapgurexcel', $tanggal);
        // return Redirect::route('absensi.rekapguru');
    }

    public function downloadrekapguruexcel(Request $request)
    {
        // dd($request);
        return Excel::download(new RekapAbsensiGuruExport($request), 'REKAP KEHADIRAN GURU.xlsx');
    }
}
