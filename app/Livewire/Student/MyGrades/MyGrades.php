<?php

namespace App\Livewire\Student\MyGrades;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class MyGrades extends Component
{
    use WithPagination;
    public $title = "My Grade";
    public $route = "my-grades";

    public $equivalent_grade;

    public $student_id;
    public function render()
    {

        $userId = Session::get('user_id');

        $student_id = DB::table('students')
            ->where('user_id','=',$userId)
            ->get()
            ->first()->id;

        $this->student_id = $student_id;        
        $table_data = $this->grades_v2 = DB::table('lab_lec_grades as llg')
            ->selectRaw('
                s.id as subject_row_id,
                s.subject_id,
                s.subject_code,
                sm.semester,
                cl.school_year_id,
                s.lecture_unit,
                s.laboratory_unit,
                MAX(cl.date_created) as date_created,
                SUM(CASE WHEN cl.is_lec = 1 THEN llg.grade ELSE 0 END) as lec_total_grade,
                SUM(CASE WHEN cl.is_lec = 0 THEN llg.grade ELSE 0 END) as lab_total_grade,
                SUM(CASE WHEN cl.is_lec = 1 THEN llg.grade / llg.sub_weight ELSE 0 END) as lec_normalized_total,
                SUM(CASE WHEN cl.is_lec = 0 THEN llg.grade / llg.sub_weight ELSE 0 END) as lab_normalized_total,
                SUM(CASE WHEN cl.is_lec = 1 THEN (llg.grade / llg.sub_weight) * 100 ELSE 0 END) as lec_calculated_grade,
                SUM(CASE WHEN cl.is_lec = 0 THEN (llg.grade / llg.sub_weight) * 100 ELSE 0 END) as lab_calculated_grade,
                CONCAT(sy.year_start," - ",sy.year_end) as school_year,
                cl.id as schedule_id
            ')
            ->join('schedulings as cl', 'cl.id', '=', 'llg.schedule_id')
            ->join('school_years as sy','sy.id','cl.school_year_id')
            ->join('schedules as sh', 'sh.id', '=', 'cl.schedule_id')
            ->join('subjects as s', 's.id', '=', 'sh.subject_id')
            ->join('semesters as sm', 'sm.id', '=', 'cl.semester_id')
            ->where('llg.student_id', $student_id)
            ->groupBy(
                's.id',
                's.subject_id',
                's.subject_code',
                's.lecture_unit',
                's.laboratory_unit',
                'sm.semester',
                'cl.school_year_id',
                'sy.year_start',
                'sy.year_end',
                'cl.id'
            );
            if (!empty($this->filters['search'])) {
                $table_data
                ->where('s.subject_id','like','%'.$this->filters['search'] .'%')
                ->orwhere('s.subject_code','like','%'.$this->filters['search'] .'%');
            }
            $table_data = $table_data
            ->orderBy('date_created', 'asc')
            ->paginate(10)->withPath(url()->current());

        $this->equivalent_grade = DB::table('point_grade_equivalent')
            ->get()
            ->toArray();
        return view('livewire.student.my-grades.my-grades',[
            'table_data' =>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
