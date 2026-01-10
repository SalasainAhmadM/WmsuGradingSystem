<?php

namespace App\Livewire\Student\MySchedules;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class MySchedules extends Component
{
    use WithPagination;

    public $title = "My Schedule";

    public $route = "my-schedules";

    public $school_year;
    public $semesters = [];
    public $year_levels = [];

    public $detail = [
        'id' => NULL,
        'curriculum_id' => NULL,
        'year_level_id' => NULL,
        'semester_id' => NULL,
        'subject_id' => NULL,
    ];

    public function mount(){
         $this->semesters = DB::table('semesters as s')
            ->orderBy('s.is_active','desc')
            ->orderBy('s.id', 'asc')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        $this->year_levels = DB::table('year_levels as yl')
            ->orderBy('yl.id', 'asc')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

    }
    public function render()
    {
        $userId = Session::get('user_id');

        $student_id = DB::table('students')
            ->where('user_id','=',$userId)
            ->get()
            ->first()->id;

        $table_data = DB::table('enrolled_students as es')
            ->select(
                'cl.id' ,
                's.college_id' ,
                's.department_id' ,
                'sh.subject_id' ,
                'cl.room_id' ,
                's.subject_code' ,
                's.description',
                's.prerequisite_subject_id' ,
                's.lecture_unit',
                's.laboratory_unit' ,
                'c.name as college',
                'd.name as department',
                'c.code as college_code',
                'd.code as department_code',
                'pr.subject_id as prerequisite_subject_id',
                'pr.subject_code as prerequisite_subject_code',
                DB::raw('CONCAT_WS(" ", u.first_name, u.middle_name, u.last_name, u.suffix) AS faculty_fullname'),
                DB::raw('CONCAT(s.subject_id," - ",s.subject_code) as subject'),
                'r.code as room_code',
                'r.name as room_name',
                's.is_active',
                DB::raw("DATE_FORMAT(cl.schedule_from, '%h:%i %p') as schedule_from"),
                DB::raw("DATE_FORMAT(cl.schedule_to, '%h:%i %p') as schedule_to"),
                'sh.day' ,
                'sh.is_lec' ,
                'cl.faculty_id',
                'sm.semester',
                'yl.year_level',
                DB::raw('CONCAT(sy.year_start," - ",sy.year_end) as school_year')
            )
            ->leftJoin('schedulings as cl','cl.id','es.schedule_id')
            ->leftJoin('schedules as sh','sh.id','cl.schedule_id')
            ->leftJoin('subjects as s','s.id','sh.subject_id')
            ->leftJoin('rooms as r','r.id','cl.room_id')
            ->leftJoin('faculty as f','f.id','cl.faculty_id')
            ->leftJoin('users as u','u.id','f.user_id')
            ->leftJoin('colleges as c','c.id','s.college_id')
            ->leftJoin('departments as d','d.id','s.department_id')
            ->leftjoin('subjects as pr','pr.id','s.prerequisite_subject_id')
            ->leftjoin('year_levels as yl','yl.id','cl.year_level_id')
            ->leftjoin('school_years as sy','sy.id','cl.school_year_id')
            ->leftjoin('semesters as sm','sm.id','cl.semester_id')
            ->where('es.student_id','=',$student_id )
            ;

        if(strlen($this->detail['year_level_id'])){
            $table_data = $table_data->where('cl.year_level_id','=',$this->detail['year_level_id']);
        }
        if(strlen($this->detail['semester_id'])){
            $table_data = $table_data->where('cl.semester_id','=',$this->detail['semester_id']);
        }
        if (!empty($this->filters['search'])) {
            $table_data
            ->where('s.subject_id','like','%'.$this->filters['search'] .'%')
            ->orwhere('s.subject_code','like','%'.$this->filters['search'] .'%');
        }
        $table_data = $table_data
            ->orderBy('sy.id','desc')
            ->orderBy('yl.id','desc')
            ->orderBy('sm.id','desc')
            ->paginate(10)->withPath(url()->current());


        return view('livewire.student.my-schedules.my-schedules',[
            'table_data' => $table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
