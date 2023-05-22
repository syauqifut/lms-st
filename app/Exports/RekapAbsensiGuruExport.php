<?php

namespace App\Exports;

use App\Presences;
use App\Group;
use App\Subject;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RekapAbsensiGuruExport implements FromView
{

    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
        
        $tgl_mulai = $request['0'];
        $tgl_akhir = $request['1'];
        
        $tanggalrekap = Presences::select('presences.id', 'course_modules.date')
                            ->join('users','users.id','=','presences.student_user_id')
                            ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                            ->join('courses','courses.id','=','course_modules.course_id')
                            ->whereBetween('course_modules.date', [$tgl_mulai, $tgl_akhir])
                            ->groupBy('course_modules.date')
                            ->orderBy('course_modules.date')
                            ->get()
                            ->transform(function ($rekap) {
            
                                return [
                                    'id' => $rekap->id,
                                    'datereal' => $rekap->date,
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

                        $tglrekap['siswarekap'] = Presences::
                            selectRaw("presences.id, course_modules.date, users.username, users.fullname, course_modules.date, 
                                COUNT(CASE WHEN presences.status = 'P' THEN presences.status END) hadir, 
                                COUNT(CASE WHEN presences.status = 'L' THEN presences.status END) izin, 
                                COUNT(CASE WHEN presences.status = 'S' THEN presences.status END) sakit, 
                                COUNT(CASE WHEN presences.status = 'A' THEN presences.status END) alpha,
                                COUNT(CASE WHEN presences.status = 'N' THEN presences.status END) none")
                            ->join('users','users.id','=','presences.student_user_id')
                            ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                            ->join('courses','courses.id','=','course_modules.course_id')
                            ->where('users.id', $user)
                            ->whereBetween('course_modules.date', [$tgl_mulai, $tgl_akhir])
                            ->groupBy('course_modules.date')
                            ->orderBy('course_modules.date')
                            ->get()
                            ->transform(function ($rekap){
                                return [
                                    'id' => $rekap->id,
                                    'date' => $rekap->date,
                                    'hadir' => $rekap->hadir,
                                    'izin' => $rekap->izin,
                                    'sakit' => $rekap->sakit,
                                    'alpha' => $rekap->alpha,
                                    'none' => $rekap->none,
                                ];
                            });
                            $rekaps[] = $tglrekap;
            };
        // dd($rekaps);
        return view('rekapabsensiguru', [
            'rekaps' => $rekaps,
            'tanggal' => $tanggalrekap,
            'tanggalsiswa' => $tanggalrekap,
            'tglmulai' => date('d-M-Y', strtotime($tgl_mulai)),
            'tglakhir' => date('d-M-Y', strtotime($tgl_akhir)),
        ]);
    }
}
