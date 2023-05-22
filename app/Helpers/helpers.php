<?php


use App\CourseUser;


function user_teacher()
{
    return Auth::user()->role == 3;
}

function user_student()
{
    return Auth::user()->role == 2;
}

    
function type_presences($type){
    switch ($type) {
    case "A":
        return "Alpha";
        break;
    case "P":
        return "Hadir";
        break;
    case "S":
        return "Sakit";
        break;
    case "L":
        return "Ijin Tidak Hadir";
        break;
    case "N":
        return "Tidak Ada Jam";
        break;
    default:
        return null;
    }
}

function course_unit_show_width_google($type){
    $extensions = array('xls','xlsx','doc','docx','ppt','pptx');
    if(in_array($type, $extensions )){
        return true;
    }
    else{
        return false;
    }
}

function checkAccessCourse($idCourse, $idUser){
   

    $canAccess = array('1','3','4');

    if(in_array( Auth::guard()->user()->usertype_id, $canAccess)){
        return true;
    }

    /// if learner
    else{
        

        $checkUserIsActive = CourseUser::where('course_id',$idCourse )->where('user_id',  Auth::id())->where('is_active',1)->first();
        if( $checkUserIsActive ){
            return true;
        }else{
            return false;
        }
    }

    
}

function getKode($n) { 
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 

    for ($i = 0; $i < $n; $i++) { 
        $index = rand(0, strlen($characters) - 1); 
        $randomString .= $characters[$index]; 
    } 

    return $randomString; 
} 

function export_excel(string $jenis, string $view, array $data, int $numrow, $tambahan ='')
{
    /// Tujuan ini untuk melakukan export excel dengan tujuan blade dan Data Model Query Builder yang dinamis
    /// Penjelasan Parameter:
    // Jenis: Menjelaskan Jenis dari Laporan yang di export
    // View: Halaman Blade.php yang jadi halaman untuk export excel
    // data: Data yang diperlukan untuk ditampilkan pada halaman excel isinya:
        // !!! Semua Parameter ini perlu di dalam arraynya !!!
        // - start_date: range tanggal awal report
        // - end_date: range tanggal akhir report, bisa waktunya sama dengan start_date
        // - {{Model}}: Ini data yang akan di parsing pada halaman blade pada parameter "view"

    $ucode= uniqid();
    $judul = $data['start_date']->format('Y-m-d'). '_'.$jenis . $tambahan.'_'. Auth::id() . '_'.$ucode;
    $kode = $ucode;
    
    $tanggal = ''. $data['start_date']->format('d-m-Y').' - '. $data['end_date']->format('d-m-Y');
    $data['kode'] = $kode;   
    $data['tanggal'] = $tanggal; 

    // $report = new Report;
    // $report->user_id = Auth::id();
    // $report->tanggal_cetak = Carbon::now();
    // $report->kode_unik = $ucode;
    // $report->nama_file = $judul.'.xlsx';
    // $report->jenis_laporan = $jenis;
    // $report->save();

    $sprtSheet = Excel::create($judul, function ($excel) use ($jenis, $data,$view, $numrow, $tanggal, $kode) {
        $excel->setTitle($jenis);
        $excel->sheet('Laporan', function ($sheet) use ($data,$view,$numrow, $tanggal, $kode)  {
            $sheet->setOrientation('landscape');
            $sheet->loadView($view, $data);
            $arAl =[];
            for ($i=0; $i < $numrow; $i++) { 
                $arAl[] = chr(65+$i);
            }
            $sheet->setAutoSize($arAl);
        });
    });
    $sprtSheet->store('xlsx',public_path('pdf/'))->export('xlsx');
}

?>