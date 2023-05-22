<?php

namespace App\Exports;

use App\GroupUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class GroupUsersExport implements FromCollection, WithDrawings, WithTitle, WithHeadings
{
    protected $groupId;
    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return GroupUser::with('user')
            ->where('group_id', $this->groupId)
            ->where('is_active', 1)
            ->whereHas('user', function ($query) {
                $query->where('is_active', 1);
            })
            ->get()
            ->transform(function ($groupUser) {
                return [
                    // 'id' => $groupUser->id,
                    'name' => $groupUser->user->fullname,
                    'username' => $groupUser->user->username,
                    'status' => $groupUser->user->usertype_id == 2 ? 'Murid' : 'Guru',
                ];
            });
        // dd($return);
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

    public function headings(): array
    {
        return [
            [''],
            [''],
            [''],
            [''],
            [''],
            ['Nama', 'Username', 'Status'],
        ];
    }

    public function title(): string
    {
        return "Group User";
    }
}
