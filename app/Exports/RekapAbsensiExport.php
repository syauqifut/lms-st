<?php

namespace App\Exports;

use App\Presences;
use App\Group;
use App\Subject;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RekapAbsensiExport implements FromView
{

    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
        $group_id = $request[0];
        $subject_id = $request[1];
        
        $group = Group::find($group_id);
        $subject = Subject::find($subject_id);

        // $group = Group::select('id', 'classes', 'academicterms', 'year')
        //                 ->where('id', $group_id)
        //                 ->get();
                        // ->transform(function ($group) {
                        //     $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                        //     return [
                        //         'id' => $group->id,
                        //         'classes' => $group->classes,
                        //         'semester' => $periode,
                        //         'year' => $group->year,
                        //     ];
                        // });

        // dd($group, $subject);
        $tglrekaps = Presences::select('presences.id', 'users.id as userid', 'users.username as nim', 'users.fullname as nama', 'users.gender')
                ->join('users','users.id','=','presences.student_user_id')
                ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                ->join('courses','courses.id','=','course_modules.course_id')
                ->where('courses.group_id', $group_id)
                ->where('courses.subject_id', $subject_id)
                ->where('users.usertype_id', 2)
                ->groupBy('users.id')
                ->orderBy('users.fullname')
                ->get();
        // dd($tglrekaps);
                $rekaps = [];
                foreach($tglrekaps as $key => $value){
                    $user = $value->userid;
                    $tglrekap = $value->toArray();

                    $tglrekap['siswarekap'] = Presences::select('presences.id', 'users.username', 'users.fullname', 'course_modules.date', 'presences.status')
                        ->join('users','users.id','=','presences.student_user_id')
                        ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                        ->join('courses','courses.id','=','course_modules.course_id')
                        ->where('courses.group_id', $group_id)
                        ->where('courses.subject_id', $subject_id)
                        ->where('users.id', $user)
                        ->groupBy('course_modules.date')
                        ->get()
                        ->transform(function ($rekap) {
        
                            return [
                                'id' => $rekap->id,
                                'date' => $rekap->date,
                                'status' => $rekap->status,
                            ];
                        });
                        $rekaps[] = $tglrekap;
        };

        $tanggalrekap = Presences::select('presences.id', 'course_modules.date')
                        ->join('users','users.id','=','presences.student_user_id')
                        ->join('course_modules','course_modules.id','=','presences.coursemodule_id')
                        ->join('courses','courses.id','=','course_modules.course_id')
                        ->where('courses.group_id', $group_id)
                        ->where('courses.subject_id', $subject_id)
                        ->groupBy('course_modules.date')
                        ->get()
                        ->transform(function ($rekap) {
        
                            return [
                                'id' => $rekap->id,
                                'date' => $rekap->date,
                                'status' => $rekap->status,
                            ];
                        });

        // dd($rekaps);
        return view('rekapabsensi', [
            'rekaps' => $rekaps,
            'tanggal' => $tanggalrekap,
            'group' => $group,
            'subject' => $subject,
        ]);
    }
}
