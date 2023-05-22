<?php

namespace App\Exports;

use App\Presences;
use App\Subject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AbsensiExport implements FromCollection, WithHeadings, WithTitle
{

    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $request = $this->request;
        //$subjectid = Subject::find($request->subject);
        $tgl_mulai = $request->tgl_mulai == null ? date('Y-m-d') : $request->tgl_mulai;
        $tgl_akhir = $request->tgl_akhir == null ? date('Y-m-d 23:59:59') : $request->tgl_akhir . " 23:59:59";

        return Presences::with(['user', 'courseModule'])
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
            ->whereBetween('date_complete', [$tgl_mulai, $tgl_akhir])
            ->get()
            ->transform(function ($presence) {
                $jam_course_module = explode(' ', $presence->courseModule->actual_start_at)[1];
                $jam_absen = explode(' ', $presence->date_complete)[1];
                 $jam_absen1 = date('Y-m-d', strtotime($presence->date_complete));
                 
                $keterangan = '';
                $userType = $presence->user->usertype_id == 3 ? 'GURU' : 'MURID';

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
                    'tanggal' => $presence->courseModule->date,
                    'subject' => $presence->courseModule->course->subject->name,
                     'jam_absen1'=>$jam_absen1,
                    'course' => $presence->courseModule->course->title . ' - ' . $presence->courseModule->title,
                    'jam_course_module' => $jam_course_module,
                    'jam_absen' => $jam_absen,
                    'keterangan' => $keterangan,
                    'NIK' => $presence->user->username,
                    'nama_user' => $presence->user->fullname,
                    'jenis_user' => $userType,
                ];
            });
    }

    public function headings(): array
    {
        return [
            "Tanggal",
            "Subject",
            "Tanggal Absen",
            "Course",
            "Jam Course Module",
            "Jam Absen",
            "Keterangan",
            "NIK",
            "Nama",
            "Jenis User",
        ];
    }

    public function title(): string
    {
        return "Absensi";
    }
}
