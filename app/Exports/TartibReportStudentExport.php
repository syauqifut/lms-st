<?php

namespace App\Exports;

use App\User;
use App\TartibPosSiswa;
use App\TartibNegSiswa;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TartibReportStudentExport implements FromView
{

    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;

        $user = User::find($request->id_user);
        $tartibPositif = TartibPosSiswa::select('tartibpossiswa.id', 'groups.classes', 'tartibs.nama_pelanggaran', 'tartibpossiswa.poin')
            ->join('groups', 'groups.id', '=', 'tartibpossiswa.group_id')
            ->join('tartibs', 'tartibs.id', '=', 'tartibpossiswa.tartib_id')
            ->where('tartibpossiswa.user_id', $user->id)
            ->get();
        $tartibNegatif = TartibNegSiswa::select('tartibnegsiswa.id', 'groups.classes', 'tartibs.nama_pelanggaran', 'tartibnegsiswa.poin')
            ->join('groups', 'groups.id', '=', 'tartibnegsiswa.group_id')
            ->join('tartibs', 'tartibs.id', '=', 'tartibnegsiswa.tartib_id')
            ->where('tartibnegsiswa.user_id', $user->id)
            ->get();
        $sumPoinPositif = $tartibPositif->sum('poin');
        $sumPoinNegatif = $tartibNegatif->sum('poin');
        // dd($tartibPositif);
        return view('tartibsiswa', [
            'user' => $user,
            'tartibpositif' => $tartibPositif,
            'tartibnegatif' => $tartibNegatif,
            'sumpoinpositif' => $sumPoinPositif,
            'sumpoinnegatif' => $sumPoinNegatif,
        ]);
    }
}
