<?php

namespace App\Http\Controllers;

use App\TartibNegSiswa;
use App\TartibPosSiswa;
use App\User;
use App\Group;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Exports\TartibReportExport;
use App\Exports\TartibReportStudentExport;
use Excel;

class TartibReportController extends Controller
{
    public function laporanTartibMurid()
    {
        if (Auth::user()->usertype_id != 2)
            return Redirect::back()->with('error', 'Halaman ini hanya bisa diakses oleh murid.');

        $data['user'] = Auth::user();
        $data['tartibPositif'] = TartibPosSiswa::with(['group', 'pelapor', 'tartib'])
            ->whereHas('activeGroup')
            ->where('user_id', Auth::id())
            ->get();
        $data['tartibNegatif'] = TartibNegSiswa::with(['group', 'pelapor', 'tartib'])
            ->whereHas('activeGroup')
            ->where('user_id', Auth::id())
            ->get();
        $data['sumPoinPositif'] = $data['tartibPositif']->sum('poin');
        $data['sumPoinNegatif'] = $data['tartibNegatif']->sum('poin');
        // dd($data['tartibPositif']);

        return Inertia::render('TartibReports/Murid', $data);
    }

    public function laporanTartibOrtu(Request $request)
    {
        if (Auth::user()->usertype_id != 5)
            return Redirect::back()->with('error', 'Halaman ini hanya bisa diakses oleh orang tua.');

        $data['children'] = User::with(['group_users.group'])
            ->whereHas('group_users.activeGroup')
            ->where('parent_id', Auth::id())
            ->get();

        if (!empty($request->id_anak)) {
            $data['anak'] = User::find($request->id_anak);

            $data['tartibPositif'] = TartibPosSiswa::with(['group', 'pelapor', 'tartib'])
                ->whereHas('activeGroup')
                ->where('user_id', $data['anak']->id)
                ->get();
            $data['tartibNegatif'] = TartibNegSiswa::with(['group', 'pelapor', 'tartib'])
                ->whereHas('activeGroup')
                ->where('user_id', $data['anak']->id)
                ->get();
            $data['sumPoinPositif'] = $data['tartibPositif']->sum('poin');
            $data['sumPoinNegatif'] = $data['tartibNegatif']->sum('poin');
        }
        // dd($data['children']);

        return Inertia::render('TartibReports/Ortu', $data);
    }
    public function laporanTartibGuru(Request $request)
    {
        $arrayRole = [1, 3, 4]; // 1 = admin, 3 = guru, 4 = guru
        if (!in_array(Auth::user()->usertype_id, $arrayRole))
            return Redirect::back()->with('error', 'Halaman ini hanya bisa diakses oleh Admin dan Guru.');

        $data['students'] = User::with(['group_users.group'])
            ->whereHas('group_users.activeGroup')
            ->where('usertype_id', 2)
            ->get();

        if (!empty($request->id_user)) {
            $data['user'] = User::find($request->id_user);

            $data['tartibPositif'] = TartibPosSiswa::with(['group', 'pelapor', 'tartib'])
                ->whereHas('activeGroup')
                ->where('user_id', $data['user']->id)
                ->get();
            $data['tartibNegatif'] = TartibNegSiswa::with(['group', 'pelapor', 'tartib'])
                ->whereHas('activeGroup')
                ->where('user_id', $data['user']->id)
                ->get();
            $data['sumPoinPositif'] = $data['tartibPositif']->sum('poin');
            $data['sumPoinNegatif'] = $data['tartibNegatif']->sum('poin');
        }
        // dd($data['children']);

        return Inertia::render('TartibReports/Guru', $data);
    }

    public function laporanTartibKelas(Request $request)
    {
        $data['groups'] = Group::where('is_active', 1)->get()
            ->transform(function ($group) {
                $periode = $group->academicterms == 1 ? 'GANJIL' : 'GENAP';
                return [
                    'id' => $group->id,
                    'classes' => $group->classes . " " . $group->huruf . " " . $group->kel_kelas . ' (' . $periode . ' ' . $group->year . ')',
                ];
            });
            // dd($request);
        if (!empty($request->id_group)) {
            $data['group'] = Group::find($request->id_group);
            // DB::statement(DB::raw('SET @row:=0'));
            $data['tartibs'] = DB::table(DB::raw("(SELECT *, 'N' AS tartibjenis FROM tartibnegsiswa UNION SELECT *, 'P' AS tartibjenis FROM tartibpossiswa) AS tartibsiswa"))
                    // ->selectRaw("tartibsiswa.id, users.username, users.fullname, tartibsiswa.group_id, @row:=@row+1 AS row, SUM(CASE WHEN tartibjenis = 'P' THEN poin END) totalpositif, SUM(CASE WHEN tartibjenis = 'N' THEN poin END) totalnegatif")
                    ->selectRaw("tartibsiswa.id, users.username, users.fullname, tartibsiswa.group_id, SUM(CASE WHEN tartibjenis = 'P' THEN poin END) totalpositif, SUM(CASE WHEN tartibjenis = 'N' THEN poin END) totalnegatif")
                    ->where('group_id', $data['group']->id)
                    ->join('users', 'users.id', '=', 'tartibsiswa.user_id')
                    ->groupBy('tartibsiswa.user_id')
                    ->get()
                    ->transform(function ($tartib) {
                        // $nomor = 1;
                        // foreach($tartib as $tid){
                        //     $nomor++;
                        // };
                        return [
                            // 'nomor' => $tartib->row,
                            'id' => $tartib->id,
                            'username' => $tartib->username,
                            'fullname' => $tartib->fullname,
                            'positif' => $tartib->totalpositif,
                            'negatif' => $tartib->totalnegatif,
                            'nilaiakhir' => $tartib->totalpositif - $tartib->totalnegatif,
                        ];
                    });
        }
        // dd($data['tartibs']);

        return Inertia::render('TartibReports/Kelas', $data);
    }

    public function export(Request $request)
    {
        // dd($request->all());
        return Excel::download(new TartibReportExport($request), 'Laporan Tata Terib.xlsx');
    }

    public function exportsiswa(Request $request)
    {
        // dd($request->all());
        return Excel::download(new TartibReportStudentExport($request), 'Laporan Tata Tertib - Siswa.xlsx');
    }
}
