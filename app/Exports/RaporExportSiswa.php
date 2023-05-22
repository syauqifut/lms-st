<?php

namespace App\Exports;

use App\DB;
use App\Rapor;
use App\Group;
use App\Course;
use App\Category;
use Carbon\Carbon;
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

class RaporExportSiswa implements FromCollection, WithHeadings, WithTitle, WithColumnWidths,  WithEvents, WithDrawings
{
    protected $siswa;
    protected $kelas;

    function __construct($siswa, $kelas) {
        $this->siswa = $siswa;
        $this->kelas = $kelas;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Rapor::
                    // join('subjects', 'subjects.name', '=','rapor.subject')->
                    where('rapor.nama','=',$this->siswa)
                    ->where('rapor.kelas','=',$this->kelas)
                    ->get()
            ->transform(function($rapor){
                return [
                    'id' => $rapor->id,
                    'subject' => $rapor->subject,
                    'gurupengajar' => $rapor->gurupengajar,
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
        $rapor = Rapor::select('nama', 'nim', 'kelas', 'walikelas')
                        ->where('nama','=',$this->siswa)
                        ->where('kelas','=',$this->kelas)->get();
        $tahun = Group::where('classes',$this->kelas)->pluck('year')->first();
        $sem = Group::where('classes',$this->kelas)->pluck('academicterms')->first();
        if($sem == 1){
            $semester = 'Ganjil';
        }else{
            $semester = 'Genap';
        }
        
        return [
            [''],
            [''],
            [''],
            [''],
            [''],
            [''],
            [''],
            ['KARTU HASIL STUDI'],
            [''],
            ['Nama', ': '.$rapor[0]['nama'], '', '', 'Prodi','','', ': '.$rapor[0]['kelas']],
            ['NIM', ': '.$rapor[0]['nim'], '',  '', 'Tahun Pelajaran/Semester','','', ': '.$tahun.'/'.$semester],
            [''],
            ['KODE', 'MATA KULIAH', 'DOSEN', 'TUGAS', 'UTS', 'UAS', 'PERFORM', 'S', 'I', 'A', 'ANGKA', 'HURUF']
        ];
    }
    
    public function title(): string
    {
        return "Rapor";
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 38,            
            'C' => 28,            
            'D' => 9,            
            'E' => 8,            
            'F' => 8,            
            'G' => 12,            
            'H' => 4,            
            'I' => 4,            
            'J' => 4,            
            'K' => 9,            
            'L' => 9,             
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
        // dd($tanggal);
        return [
            
            AfterSheet::class=> function(AfterSheet $event) 
            {
                $prodi = Rapor::select(\DB::raw('SUBSTRING(kelas, 4) as kelas'))->where('kelas','=',$this->kelas)->pluck('kelas')->first();
                // dd($prodi);
                $tanggal = Carbon::now()->format('d F Y');

                $event->sheet->mergeCells('A1:L6');
                $event->sheet->mergeCells('A8:L8');
                $event->sheet->mergeCells('A9:L9');
                $event->sheet->mergeCells('A12:L12');

                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');
                $event->sheet->getStyle('A8')->applyFromArray([
                    'font' => ['bold' => true],  
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]
                ]);
                // $event->sheet->getStyle('A10')->applyFromArray([
                //     'font' => ['bold' => true],  
                //     'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]
                // ]);
                $event->sheet->getRowDimension(13)->setRowHeight(20);
                $event->sheet->getStyle('A13:L13')->applyFromArray([
                    'font' => ['bold' => true],  
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ]
                ]);
                $event->sheet->getStyle('A14:A33')->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ]
                ]);
                $event->sheet->getStyle('B14:C33')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ]
                ]);
                $event->sheet->getStyle('D14:L33')->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ]
                ]);
                $event->sheet->mergeCells('A35:F35');
                $event->sheet->mergeCells('A36:F36');
                $event->sheet->mergeCells('A37:F37');
                $event->sheet->mergeCells('G35:L35');
                $event->sheet->mergeCells('G36:L36');
                $event->sheet->mergeCells('G37:L37');
                $event->sheet->getStyle('G35')->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]
                ]);
                $event->sheet->getStyle('G36')->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]
                ]);
                $event->sheet->getStyle('G37')->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]
                ]);
                $event->sheet->setCellValue('G35','Surabaya, '.$tanggal);
                $event->sheet->setCellValue('G36','A.n. Ketua,');
                $event->sheet->setCellValue('G37','Kaprodi '.$prodi);
            
            },
              ];
       }
}
