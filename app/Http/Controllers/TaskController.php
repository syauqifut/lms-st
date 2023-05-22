<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseModule;
use App\CourseUser;
use App\Task;
use App\TaskFileUser;
use App\User;
use App\UserAccess;
// use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use ZipArchive;
use Excel;
use App\Exports\TaskMarkExport;
use App\Imports\TaskMarkImport;
use App\ImportLog;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index($course_module_id)
    {
        $data['courseModule'] = CourseModule::find($course_module_id);
        $data['filters'] = Request::all('search');
        $data['tasks'] =  Task::filter(Request::only('search'))
            ->where('course_module_id', $course_module_id)
            ->orderBy('name')
            ->paginate()
            ->transform(function ($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'course_module' => $task->course_module,
                    'teacher' => $task->teacher,
                    'group' => $task->group,
                    'date' => $task->date,
                    'due_date' => $task->due_date,
                    'task_type' => $task->task_type,
                    'is_file' => $task->task_type,
                    'file' => $task->file,
                    'soal'=>$task->soal,
                    'auto_mark' => $task->auto_mark,
                    'random_order' => $task->random_order,
                    'link' => $task->link,
                    'user_upload' => $task->user_upload($task->id),
                ];
            });
        // dd($data['tasks']);

        return Inertia::render('Tasks/Index', $data);
    }

    public function showAllUploadedTask($task_id)
    {
        $data['task'] = Task::findOrFail($task_id);
        $data['filters'] = Request::all('search');
        $data['taskFiles'] =  TaskFileUser::filter(Request::only('search'))
            ->with(['user'])
            ->join('users', 'users.id', '=', 'task_file_users.user_id')
            ->where('task_id', $task_id)
            ->select('task_file_users.*', 'task_file_users.id as task_file_id', 'task_file_users.tanggal_kumpul as tanggal_kumpul_tugas','users.fullname')
            // ->select('task_file_users.*', 'task_file_users.id as task_file_id', 'task_file_users.updated_at as file_updated_at')
            ->orderBy('users.fullname')
            // dd($data['taskFiles']);
            ->paginate()
            ->transform(function ($taskFile) {
                return [
                    'id' => $taskFile->task_file_id,
                    'description' => $taskFile->description,
                    'soal'=>$taskFile->soal,
                    'file' => $taskFile->file,
                    'status' => $taskFile->status,
                    'mark' => $taskFile->mark,
                    'user' => $taskFile->user,
                    'fullname'=>$taskFile->fullname,
                    'tanggal_kumpul' => $taskFile->tanggal_kumpul,
                    'tgl_pengumpulan' => Carbon::parse($taskFile->tanggal_kumpul_tugas)->format('Y-m-d h:i:s'),
                ];
            });
          
            // dd($data['taskFiles']);

            if ($data['task']->file) {
                // echo "A";
                return Inertia::render('Tasks/ListTaskFile', $data);
            }elseif ($data['task']->link){
                // echo "B";
                return Inertia::render('Tasks/ListTaskLink', $data);
            } 
            else {
                // echo "C";
                return Inertia::render('Tasks/ListTaskNotFile', $data);
            }
    }

    public function finishAssignment($task_id)
    {
        // dd($task_id);
        $current_date_time = Carbon::now()->toDateTimeString();

        $taskUser = TaskFileUser::where('user_id','=',Auth::id())
        ->where('task_id','=', $task_id);

        $taskUser->update([
            'description' => 'FINISH ASSIGNMENT LINK',
            'status' => 0,
            'tanggal_kumpul' => $current_date_time
        ]);

        return Redirect::back()->with('success', 'Berhasil menyelesaikan tugas');
    }

    public function markStudentTask(\Illuminate\Http\Request $request)
    {
        // dd($request->all());
        if ($request->task_id != null) {
            // NILAI SEMUA TUGAS DI TASK ITU
            $task = Task::findOrFail($request->task_id);
            $taskFiles = TaskFileUser::where('task_id', $task->id)
                ->update([
                    'status' => 1,
                    'mark' => $request->mark
                ]);
        } else {
            // NILAI 1 TUGAS
            $taskFile = TaskFileUser::findOrFail($request->task_file_id);
            $taskFile->update([
                'status' => 1,
                'mark' => $request->mark
            ]);
        }

        return Redirect::back()->with('success', 'Berhasil update nilai');
    }

    public function create($course_module_id)
    {
        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $data['courseModule'] = CourseModule::findOrFail($course_module_id);
        $data['userLogin'] = Auth::user();
        $data['teachers'] = User::where('usertype_id', 3)->whereIn('id', $access)->get();
        $data['tasks'] = Task::all()
            ->transform(function ($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name . ' - ' . $task->task_type,
                ];
            });

        return Inertia::render('Tasks/Create', $data);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        // dd($request->all());
        $course = Course::join('course_modules', 'course_modules.course_id', '=', 'courses.id')
            ->where('course_modules.id', $request->course_module_id)
            ->first();
        //  dd($course);

        if ($request->create_task == 'new') {
            $rules = [
                'teacher_id' => [auth()->user()->usertype_id != 3 ? 'required' : ''],
                'name' => ['required'],
                'task_type' => ['required'],
                'date' => ['required'],
                'due_date' => ['required'],
                'is_file' => ['required'],
                'file' => ['required_if:is_file,1'],
                // 'auto_mark' => ['required_if:is_file,0'],
                // 'random_order' => ['required_if:is_file,0'],
                'soal'=>['required_if:is_file,2'],
                'link' => ['required_if:is_file,0'],
            ];
            $messages = [
                'teacher_id.required' => 'Guru tidak boleh kosong',
                'name.required' => 'Nama task tidak boleh kosong',
                'task_type.required' => 'Tipe task tidak boleh kosong',
                'date.required' => 'Tanggal tidak boleh kosong',
                'due_date.required' => 'Tanggal batas pengumpulan tidak boleh kosong',
            ];
            $request->validate($rules, $messages);

            if ($request->is_file == 0) {
                $task = Task::create([
                    'course_module_id' => $request->course_module_id,
                    'teacher_id' => $request->teacher_id ? $request->teacher_id : Auth::id(),
                    'group_id' => $course->group_id,
                    'date' => $request->date,
                    'name' => $request->name,
                    'task_type' => $request->task_type,
                    'due_date' => $request->due_date . " 23:59:59",
                    'is_file' => $request->is_file,
                    // 'auto_mark' => $request->auto_mark,
                    // 'random_order' => $request->random_order,
                    //'keterangan'=>$request->keterangan,
                    //'link' => $request->link,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            } elseif($request->is_file == 1) {
                $task = Task::create([
                    'course_module_id' => $request->course_module_id,
                    'teacher_id' => $request->teacher_id ? $request->teacher_id : Auth::id(),
                    'group_id' => $course->group_id,
                    'date' => $request->date,
                    'name' => $request->name,
                    'task_type' => $request->task_type,
                    'due_date' => $request->due_date . " 23:59:59",
                    'is_file' => $request->is_file,
                    'file' => Task::setFile($request->file),
                    // 'auto_mark' => 0,
                    // 'random_order' => 0,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            } else{
                 $task = Task::create([
                    'course_module_id' => $request->course_module_id,
                    'teacher_id' => $request->teacher_id ? $request->teacher_id : Auth::id(),
                    'group_id' => $course->group_id,
                    'date' => $request->date,
                    'name' => $request->name,
                    'task_type' => $request->task_type,
                    'due_date' => $request->due_date . " 23:59:59",
                    'is_file' => $request->is_file,
                    //'file' => Task::setFile($request->file),
                    // 'auto_mark' => 0,
                    // 'random_order' => 0,
                    'soal' => $request->soal,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            $newTask = Task::where('name', $request->name)
                ->where('course_module_id', $request->course_module_id)
                ->orderBy('id', 'DESC')
                ->first();

            $users = CourseUser::where('course_id', $course->course_id)
                ->join('users', 'course_users.user_id', 'users.id')
                ->where('users.usertype_id', '2')
                ->where('course_users.is_active',1)
                ->get();


            foreach ($users as $user) {
                $taskUser = TaskFileUser::create([
                    'task_id' => $newTask->id,
                    'user_id' => $user->user_id,
                    'file' =>'',
                    'status' => NULL
                ]);
            }

            return Redirect::route('tasks.index', [$task->course_module_id])->with('success', 'Task berhasil dibuat.');
        } elseif ($request->create_task == 'old') {
            $rules = [
                'teacher_id' => [auth()->user()->usertype_id != 3 ? 'required' : ''],
                'name' => ['required'],
                'task_type' => ['required'],
                'date' => ['required'],
                'due_date' => ['required'],
            ];
            $messages = [
                'teacher_id.required' => 'Guru tidak boleh kosong',
                'name.required' => 'Nama task tidak boleh kosong',
                'task_type.required' => 'Tipe task tidak boleh kosong',
                'date.required' => 'Tanggal tidak boleh kosong',
                'due_date.required' => 'Tanggal batas pengumpulan tidak boleh kosong',
            ];
            $request->validate($rules, $messages);

            $oldTask = Task::find($request->task_id);

            $newTask = Task::create([
                'course_module_id' => $request->course_module_id,
                'teacher_id' => $request->teacher_id ? $request->teacher_id : Auth::id(),
                'group_id' => $course->group_id,
                'date' => $request->date,
                'name' => $request->name,
                'task_type' => $request->task_type,
                'due_date' => $request->due_date . " 23:59:59",
                'is_file' => $oldTask->is_file,
                'link' => $oldTask->link,
                'file' => $oldTask->file,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $users = CourseUser::where('course_id', $course->course_id)
                ->join('users', 'course_users.user_id', 'users.id')
                ->where('users.usertype_id', '2')
                ->where('course_users.is_active',1)
                ->get();

            foreach ($users as $user) {
                $taskUser = TaskFileUser::create([
                    'task_id' => $newTask->id,
                    'user_id' => $user->user_id,
                    'status' => NULL
                ]);
            }

            return Redirect::route('tasks.index', [$task->course_module_id])->with('success', 'Task berhasil dibuat.');
        }
    }

    public function downloadAsZip($task_id)
    {
        $task = Task::findOrFail($task_id);
        $taskFiles = TaskFileUser::where('task_id', $task->id)
            ->whereNotNull('file')
            ->get();

        if ($taskFiles) {
            $zip = new ZipArchive();
            $zipName = $task->name . '_' .  time() . '.zip';
            $zip->open($zipName, ZipArchive::CREATE);

            foreach ($taskFiles as $taskFile) {
                $path = public_path('files/student_tasks/' . $taskFile->file);
                if (file_exists($path)) {
                    $zip->addFromString(basename($path),  file_get_contents($path));
                }
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $zipName);
            header('Content-Length: ' . filesize($zipName));
            readfile($zipName);

            unlink(public_path($zipName));
        }
    }

    public function preview($task_id)
    {
        $data['task'] = Task::find($task_id);
        $path = public_path('/files/tasks/' . $data['task']->file);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext == 'pdf') {
            $data['file'] = 'pdf';
            $contentPathPdf = url('/') . "/files/tasks/" . $data['task']->file; //server online
            // $contentPathPdf = "http://infolab.stanford.edu/pub/papers/google.pdf"; //server offline
            $data['showGoogle'] = "";
            $data['showPdf'] = " https://docs.google.com/viewer?url=" . $contentPathPdf . "&embedded=true";
        } else if ($ext == 'png' || $ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'tiff' || $ext == 'svg') {
            $data['file'] = 'jpg';
            $data['showPdf'] = "";
            $data['showGoogle'] = "";
        } else if ($ext == 'zip') {
            $data['file'] = 'zip';
            $data['showPdf'] = "";
            $data['showGoogle'] = "";
        } else {
            $data['file'] = 'doc';
            $contentPathDoc = url('/') . "/files/tasks/" . $data['task']->file; //server online
            // $contentPathDoc = "http://schoolmap.dindikptk.net/sipptendik/modul/verval/fileEkspor/RekapSIPP106.xlsx"; //server offline
            $data['showPdf'] = "";
            $data['showGoogle'] = "https://view.officeapps.live.com/op/embed.aspx?src=" . $contentPathDoc;
        }
        // dd($contentPath);

        return Inertia::render('Tasks/Preview', $data);
    }

    public function downloadFile($task_id)
    {
        $task = Task::find($task_id);
        // dd($discussion);

        $path = public_path('/files/tasks/' . $task->file);
        $header = [
            'Content-Type' => 'application/*',
        ];
        return response()->download($path, $task->file, $header);
    }

    public function downloadUploadedTask($task_file_id)
    {
        $taskFile = TaskFileUser::find($task_file_id);
        // dd($task_file_id);
        $path = public_path('/files/student_tasks/' . $taskFile->file);
        $header = [
            'Content-Type: application/*',
        ];
        // return redirect('/files/student_tasks/' . $taskFile->file);
        return response()->download($path, $taskFile->file, $header);
    }
    public function lihatTugas($task_file_id)
    {
        $data['task'] = TaskFileUser::find($task_file_id);
        $path = public_path('/files/student_tasks/' . $data['task']->file);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext == 'pdf') {
            $data['file'] = 'pdf';
            $contentPathPdf = url('/') . "/files/student_tasks/" . $data['task']->file; //server online
            // $contentPathPdf = "http://infolab.stanford.edu/pub/papers/google.pdf"; //server offline
            $data['showGoogle'] = "";
            $data['showPdf'] = " https://docs.google.com/viewer?url=" . $contentPathPdf . "&embedded=true";
        } else if ($ext == 'png' || $ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'tiff' || $ext == 'svg') {
            $data['file'] = 'jpg';
            $data['showPdf'] = "";
            $data['showGoogle'] = "";
        } else if ($ext == 'zip') {
            $data['file'] = 'zip';
            $data['showPdf'] = "";
            $data['showGoogle'] = "";
        } else {
            $data['file'] = 'doc';
            $contentPathDoc = url('/') . "/files/student_tasks/" . $data['task']->file; //server online
            // $contentPathDoc = "http://schoolmap.dindikptk.net/sipptendik/modul/verval/fileEkspor/RekapSIPP106.xlsx"; //server offline
            $data['showPdf'] = "";
            $data['showGoogle'] = "https://view.officeapps.live.com/op/embed.aspx?src=" . $contentPathDoc;
        }
        // dd($contentPath);

        return Inertia::render('Tasks/LihatTugas',  $data);
    }

    public function edit($task_id)
    {
        $access = UserAccess::join('accesses', 'accesses.id', '=', 'user_accesses.access_id')
            ->where('accesses.name', '=', 'Stai')
            ->pluck('user_accesses.user_id');

        $data['teachers'] = User::where('usertype_id', 3)->whereIn('id', $access)->get();
        $data['task'] = Task::find($task_id);
        $data['userLogin'] = Auth::user();

        return Inertia::render('Tasks/Edit', $data);
    }


    public function update(\Illuminate\Http\Request $request, $task_id)
    {
        $task = Task::find($task_id);

        $rules = [
            'teacher_id' => [auth()->user()->usertype_id != 3 ? 'required' : ''],
            'name' => ['required'],
            'task_type' => ['required'],
            'date' => ['required'],
            'due_date' => ['required'],
            'is_file' => ['required'],
            // 'file' => ['required_if:is_file,1'],
            // 'auto_mark' => ['required_if:is_file,0'],
            // 'random_order' => ['required_if:is_file,0'],
            'soal'=>['required_if:is_file,2'],
            'link' => ['required_if:is_file,0'],
        ];
        $messages = [
            'teacher_id.required' => 'Guru tidak boleh kosong',
            'name.required' => 'Nama task tidak boleh kosong',
            'task_type.required' => 'Tipe task tidak boleh kosong',
            'date.required' => 'Tanggal tidak boleh kosong',
            'due_date.required' => 'Tanggal batas pengumpulan tidak boleh kosong',
        ];

        $request->validate($rules, $messages);

        if ($request->is_file == 0) {
            $task->update([
                'teacher_id' => $request->teacher_id,
                'date' => $request->date,
                'name' => $request->name,
                'task_type' => $request->task_type,
                'due_date' => $request->due_date,
                'is_file' => $request->is_file,
                // 'auto_mark' => $request->auto_mark,
                // 'random_order' => $request->random_order,
                //'keterangan'=>$request->keterangan,
                'link' => $request->link,
                'updated_by' => Auth::id(),
            ]);
        } elseif($request->is_file == 1) {

            if ($request->hasFile('file')) {
                if (!empty($task->file)) {
                    $path = public_path('files/tasks/' . $task->file);
                    unlink($path);
                }
                $newFile = $task->setFile($request->file);
            }

            $task->update([
                'teacher_id' => $request->teacher_id,
                'date' => $request->date,
                'name' => $request->name,
                'task_type' => $request->task_type,
                'due_date' => $request->due_date,
                'is_file' => $request->is_file,
                'file' => $request->hasFile('file') ? $newFile : $task->file,
                // 'auto_mark' => 0,
                // 'random_order' => 0,
                //'keterangan'=>$request->keterangan,
                'link' => $request->link,
                'updated_by' => Auth::id(),
            ]);
        }else {

            //if ($request->hasFile('file')) {
                //if (!empty($task->file)) {
                    //$path = public_path('files/tasks/' . $task->file);
                    //unlink($path);
                //}
                //$newFile = $task->setFile($request->file);
            //}//

            $task->update([
                'teacher_id' => $request->teacher_id,
                'date' => $request->date,
                'name' => $request->name,
                'task_type' => $request->task_type,
                'due_date' => $request->due_date,
                'is_file' => $request->is_file,
                //'file' => $request->hasFile('file') ? $newFile : $task->file,
                // 'auto_mark' => 0,
                // 'random_order' => 0,
                'soal'=>$request->soal,
                //'link' => $request->link,
                'updated_by' => Auth::id(),
            ]);
        }

        return Redirect::route('tasks.index', [$task->course_module_id])->with('success', 'Task berhasil diperbarui.');
    }


    public function destroy($task_id)
    {
        // dd($task_id);
        $task = Task::find($task_id);

        if (!empty($task->file)) {
            $files = Task::where('file', $task->file)->count();
            if ($files > 1) {
                return Redirect::back()->with('error', 'Tidak bisa menghapus task. File ini juga digunakan di task lain');
            } else {
                $path = public_path('files/tasks/' . $task->file);
                unlink($path);
            }
        }

        $task->delete();

        return Redirect::route('tasks.index', [$task->course_module_id])->with('success', 'Task berhasil dihapus.');
    }

    public function studetMarkTaskLink($task_id)
    {
        $data['task'] = Task::findOrFail($task_id);
        $data['taskFile'] = TaskFileUser::where('task_id', $data['task']->id)
            ->where('user_id', Auth::id())
            ->first();

        return Inertia::render('Tasks/NilaiTaskLink', $data);
    }

    public function studentUploadForm($task_id)
    {
        $data['task'] = Task::find($task_id);
        $data['taskFile'] = TaskFileUser::where('task_id', $data['task']->id)
            ->where('user_id', Auth::id())
            ->first();

        return Inertia::render('Tasks/Upload', $data);
    }

    public function studentUploadFile(\Illuminate\Http\Request $request)
    {
        $current_date_time = Carbon::now()->toDateTimeString();
        $rules = [
            //'file' => ['required'],
        ];
        $messages = [
           // 'file.required' => 'File tidak boleh kosong',
        ];

        $request->validate($rules, $messages);

        if (empty($request->task_file_id)) {
            $taskFile = TaskFileUser::create([
                'description' => $request->description,
                'file' => TaskFileUser::setFile($request->file),
                'status' => 0,
                'task_id' => $request->task_id,
                'user_id' => Auth::id(),
                'tanggal_kumpul' => $current_date_time
            ]);

        } else {
            $taskFile = TaskFileUser::find($request->task_file_id);

            if (!empty($taskFile->file)) {
                $path = public_path('files/student_tasks/' . $taskFile->file);

                if (file_exists($path)) {
                    unlink($path);
                }
                // $newFile = $taskFile->setFile($request->file);

            }

            $newFile = $taskFile->setFile($request->file);

            $taskFile->update([
                'description' => $request->description,
                'file' => $newFile,
                'status' => 0,
                'tanggal_kumpul' => $current_date_time
            ]);
        }

        return Redirect::back()->with('success', 'Berhasil upload task');
    }

    public function export($id)
    {
        return Excel::download(new TaskMarkExport($id), 'task.xlsx');
    }

    public function import(\Illuminate\Http\Request $request)
    {
        $data['task'] = Task::find($request->task_id);
        return Inertia::render('Tasks/Import', $data);
    }

    public function processImport()
    {
        Request::validate([
            'data' => ['required', 'file'],
        ]);
        $path1 = Request::file('data')->store('temp');
        $path = storage_path('app') . '/' . $path1;

        //$result = Excel::import(new TaskMarkImport, Request::file('data')->path());
        $result = Excel::import(new TaskMarkImport, Request::file('data'));
        // \Excel::import(new UsersImport, $request->file('mcafile'))

        ImportLog::create([
            'name' => 'Import Groups',
            'description' => Request::input('description'),
            'createdBy' => Auth::id(),
        ]);

        return Redirect::back()->with('success', 'Berhasil import Nilai ');
    }
}
