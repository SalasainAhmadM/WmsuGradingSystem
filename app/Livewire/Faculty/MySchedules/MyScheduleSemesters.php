<?php

namespace App\Livewire\Faculty\MySchedules;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;


class MyScheduleSemesters extends Component
{

    use WithPagination;
    public $school_year;
    public $school_year_id;

    public $title = "My Schedule";

    public $route = "My Schedule";

    public $filters = [
        'search'=> NULL,
    ];
    public function mount($school_year){
        $this->school_year = $school_year;
    }
    
    public function render()
    {
        $userId = Session::get('user_id');

        $this->school_year_id = DB::table('school_years')->where(DB::raw('concat(year_start,"-",year_end)'),'=',$this->school_year)->first()->id;

        $table_data = DB::table('users as u')
            ->select(
                  'sm.id',
                'sm.semester',
            )
            ->join('faculty as f', 'f.user_id', 'u.id')
            ->join('schedulings as cl', 'cl.faculty_id', 'f.id')
            ->where('f.user_id', '=', $userId)
            ->Join('schedules as sh', 'sh.id', 'cl.schedule_id')
            ->leftJoin('school_years as sy', 'sy.id', 'cl.school_year_id')
            ->where('sy.id', '=', $this->school_year_id)
            ->leftJoin('semesters as sm', 'sm.id', 'cl.semester_id')
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
                'sm.id',
                'sm.semester',
            )
            ->orderBy('sm.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.faculty.my-schedules.my-schedule-semesters',[
            'table_data' =>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
