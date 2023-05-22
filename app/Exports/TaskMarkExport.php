<?php

namespace App\Exports;

use App\Task;
use App\TaskFileUser;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use DB;

class TaskMarkExport implements FromCollection, WithHeadings, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $id;
    function __construct($id){
        $this->id=$id;
    }

    public function collection()
    {
       // dd($this->id);
        return Task::where('tasks.id', $this->id)
        ->join('task_file_users', 'task_file_users.task_id', '=', 'tasks.id')
        ->join('users', 'users.id','=', 'task_file_users.user_id')
            ->get([
                'task_file_users.id as id', 'tasks.id as task_id', 'tasks.name as task', 'users.id as user_id', 'users.fullname as fullname', 'task_file_users.mark',DB::raw('(CASE WHEN (task_file_users.status IS NULL) THEN "BELUM" ELSE "SUDAH" END) AS status')
                ])
            ->transform(function($taskuser){
                return [
                    'id' => $taskuser->id,
                    'task_id' => $taskuser->task_id,
                    'task' => $taskuser->task,
                    'user_id' => $taskuser->user_id,
                    'nama' => $taskuser->fullname,
                    'status'=>$taskuser->status,
                    'nilai' => $taskuser->mark
                    // 'code' => $group->code,
                ];
            });
    }

    public function headings(): array
    {
        return ["id", "task_id","task", 'user_id', "nama","status","nilai"];
    }
    
    public function title(): string
    {
        return "Groups";
    }
}
