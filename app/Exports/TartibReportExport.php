<?php

namespace App\Exports;

use DB;
use App\Group;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;

class TartibReportExport implements FromCollection, WithHeadings, WithTitle, WithDrawings, WithColumnWidths, WithEvents
{

    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function title(): string
    {
        return "Laporan Tata Tertib";
    }

    public function headings(): array
    {
        $request = $this->request;

        $group = Group::where('id', $request->id_group)->pluck('classes')->first();

        return [
            [''],
            [''],
            [''],
            [''],
            [''],
            [''],
            ['','Kelas', ': '.$group],
            [''],
            ['No', 'No. Induk', 'Nama Santri', 'Poin Positif', 'Poin Negatif', 'Poin Akhir']
        ];
    }
    
    public function drawings()
    {

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Report logo');
        $drawing->setPath(public_path('images/tartiblogo.png'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');
        return $drawing;
    }

    public function collection()
    {
        $request = $this->request;

        // DB::statement(DB::raw('set @row:=0'));
        // return DB::table(DB::raw("(select *, 'N' AS tartibjenis from tartibnegsiswa union select *, 'P' AS tartibjenis from tartibpossiswa) as tartibsiswa"))
        // ->selectRaw("*, @row:=@row+1 as row, sum(case when tartibjenis = 'P' then poin end) totalpositif, sum(case when tartibjenis = 'N' then poin end) totalnegatif")
        // ->where('group_id', $request->id_group)
        // ->join('users', 'users.id', '=', 'tartibsiswa.user_id')
        // ->groupBy('tartibsiswa.user_id')
        // ->orderBy('row')
        // ->get()
        // ->transform(function ($tartib) {
        //     return [
        //         'nomor' => $tartib->row,
        //         'username' => $tartib->username,
        //         'fullname' => $tartib->fullname,
        //         'positif' => $tartib->totalpositif,
        //         'negatif' => $tartib->totalnegatif,
        //         'nilaiakhir' => $tartib->totalpositif - $tartib->totalnegatif,
        //     ];
        // });
        return DB::table(DB::raw("(SELECT *, 'N' AS tartibjenis FROM tartibnegsiswa UNION SELECT *, 'P' AS tartibjenis FROM tartibpossiswa) AS tartibsiswa"))
                    // ->selectRaw("tartibsiswa.id, users.username, users.fullname, tartibsiswa.group_id, @row:=@row+1 AS row, SUM(CASE WHEN tartibjenis = 'P' THEN poin END) totalpositif, SUM(CASE WHEN tartibjenis = 'N' THEN poin END) totalnegatif")
                    ->selectRaw("tartibsiswa.id, users.username, users.fullname, tartibsiswa.group_id, SUM(CASE WHEN tartibjenis = 'P' THEN poin END) totalpositif, SUM(CASE WHEN tartibjenis = 'N' THEN poin END) totalnegatif")
                    ->where('group_id', $request->id_group)
                    ->join('users', 'users.id', '=', 'tartibsiswa.user_id')
                    ->groupBy('tartibsiswa.user_id')
                    ->get()
                    ->transform(function ($tartib) {
                        return [
                            'nomor' => $tartib->id,
                            'username' => $tartib->username,
                            'fullname' => $tartib->fullname,
                            'positif' => $tartib->totalpositif,
                            'negatif' => $tartib->totalnegatif,
                            'nilaiakhir' => $tartib->totalpositif - $tartib->totalnegatif,
                        ];
                    });
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,            
            'C' => 23,            
            'D' => 12,            
            'E' => 12,            
            'F' => 12,           
        ];
    }

    public function registerEvents(): array
    {
        return [
            
            AfterSheet::class=> function(AfterSheet $event) 
            {
                $event->sheet->getStyle('A9:F9')->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]
                ]);
            },
        ];
    }
}
