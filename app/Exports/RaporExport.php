<?php

namespace App\Exports;

use App\Rapor;
use App\Group;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RaporExport implements FromCollection, WithHeadings, WithTitle, WithColumnWidths, WithEvents, WithDrawings
{
    protected $subject;
    protected $kelas;

    function __construct($subject, $kelas) {
        $this->subject = $subject;
        $this->kelas = $kelas;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Rapor::where('subject','=',$this->subject)
                      ->where('kelas','=',$this->kelas)
                      ->get()
            ->transform(function($rapor){
                return [
                    'id' => $rapor->id,
                    'nim' => $rapor->nim,
                    'nama' => $rapor->nama,
                    'tugas' => $rapor->tugas,
                    'uts' => $rapor->uts,
                    'uas' => $rapor->uas,
                    'perform' => $rapor->perform,
                    'sakit' => $rapor->sakit,
                    'izin' => $rapor->izin,
                    'alpha' => $rapor->alpha,
                    'nilai' => $rapor->nilai,
                    'huruf' => $rapor->huruf,
                ];
            });
        // dd($this->subject);
    }

    public function headings(): array
    {
        $rapor = Rapor::select('subject', 'gurupengajar', 'kelas', 'walikelas')
                        ->where('subject','=',$this->subject)
                        ->where('kelas','=',$this->kelas)->get();
        $tahun = Group::where('classes',$this->kelas)->get('year');
        $sem = Group::where('classes',$this->kelas)->pluck('academicterms')->first();
        if($sem == 1){
            $semester = 'GASAL';
        }else{
            $semester = 'GENAP';
        }
        // dd($tahun);
        return [
            [''],
            [''],
            [''],
            [''],
            [''],
            [''],
            [''],
            [''],
            ['DAFTAR ABSENSI UJIAN/NILAI UAS'],
            ['SEMESTER '.$semester.' TAHUN AKADEMIK '.$tahun[0]['year']],
            [''],
            ['Mata Kuliah','', ':'.$rapor[0]['subject'], '', '', '', '', 'Pengampu', '', '',': '.$rapor[0]['gurupengajar'], ''],
            ['Jurusan','', ':'.$rapor[0]['kelas'], '', '', '', '', 'Wali Kelas', '', '', ': '.$rapor[0]['walikelas'], ''],
            [''],
            ['Id', 'NIM', 'Nama Mahasiswa', 'TUGAS', 'UTS', 'UAS', 'PERFORM', 'S', 'I', 'A', 'ANGKA', 'HURUF', 'Tanda Tangan']
        ];
    }
    
    public function title(): string
    {
        return "Rapor";
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 16,            
            'C' => 25,            
            'D' => 9,            
            'E' => 9,            
            'F' => 9,            
            'G' => 11,            
            'H' => 4,            
            'I' => 4,            
            'J' => 4,            
            'K' => 9,            
            'L' => 9,            
            'M' => 14,            
        ];
    }

    public function drawings()
    {

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Report logo');
        $drawing->setPath(public_path('images/reportlogo.png'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');
        return $drawing;
    }

    public function registerEvents(): array
        {
        return [
            
            AfterSheet::class=> function(AfterSheet $event) 
            {
                $event->sheet->mergeCells('A1:M7');
                $event->sheet->mergeCells('A8:M8');
                $event->sheet->mergeCells('A9:M9');
                $event->sheet->mergeCells('A10:M10');
                $event->sheet->mergeCells('A11:M11');
                $event->sheet->mergeCells('A14:M14');

                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');
                $event->sheet->getStyle('A9')->applyFromArray([
                    'font' => ['bold' => true],  
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]
                ]);
                $event->sheet->getStyle('A10')->applyFromArray([
                    'font' => ['bold' => true],  
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]
                ]);
                $event->sheet->getRowDimension(15)->setRowHeight(20);
                $event->sheet->getStyle('A15:M15')->applyFromArray([
                    'font' => ['bold' => true],  
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ]
                ]);
                $event->sheet->getStyle('A16:B70')->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ]
                ]);
                $event->sheet->getStyle('C16:C70')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ]
                ]);
                $event->sheet->getStyle('D16:M70')->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ]
                ]);
                
                
                // $event->sheet->getColumnDimension('D')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            },
              ];
       }
}
