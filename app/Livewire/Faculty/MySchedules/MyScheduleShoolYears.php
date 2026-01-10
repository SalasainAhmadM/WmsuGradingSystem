<?php

namespace App\Livewire\Faculty\MySchedules;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class MyScheduleShoolYears extends Component
{
     use WithPagination;
    public $title = "My Schedule";

    public $route = "My Schedule";
    public function render()
    {

        $userId = Session::get('user_id');

        $table_data = DB::table('users as u')
            ->select(
                'sy.id as school_year_id',
                'sy.year_start',
                'sy.year_end',
                'sy.date_start',
                'sy.date_end'
            )
            ->join('faculty as f', 'f.user_id', 'u.id')
            ->join('schedulings as cl', 'cl.faculty_id', 'f.id')
            ->where('f.user_id', '=', $userId)
            ->Join('schedules as sh', 'sh.id', 'cl.schedule_id')
            ->leftJoin('school_years as sy', 'sy.id', 'cl.school_year_id')
            ->leftJoin('subjects as s', 's.id', 'sh.subject_id')
            ->leftJoin('rooms as r', 'r.id', 'cl.room_id')
            ->leftJoin('colleges as c', 'c.id', 's.college_id')
            ->leftJoin('departments as d', 'd.id', 's.department_id')
            ->leftJoin('subjects as pr', 'pr.id', 's.prerequisite_subject_id');

        if (!empty($this->filters['search'])) {
            $table_data->where(function($query) {
                $query->where('s.subject_id', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('s.subject_code', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        $table_data = $table_data
            ->groupBy(
                'sy.id',
                'sy.year_start',
                'sy.year_end',
                'sy.date_start',
                'sy.date_end',
            )
            ->orderBy('sy.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.faculty.my-schedules.my-schedule-shool-years',[
            'table_data' =>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
