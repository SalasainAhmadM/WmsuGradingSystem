<?php

namespace App\Livewire\Student\MyGradeComponents;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;


class MyGradeComponents extends Component
{

    public $title = "Grade component";
    public $route = "grade-components";

    public $terms;
    public $student;

    public $school_work_types;
    public $student_scores;

    public $schedule_id;
    public $student_id;

    public function mount($student_id,$schedule_id){

        $this->schedule_id = $schedule_id;
        $this->student_id = $student_id;
        if(!$student_id){
            $userId = Session::get('user_id');
            $student_id = DB::table('students')
                ->where('user_id','=',$userId)
                ->first()->id;
        }
        self::fetch_terms($schedule_id);


        self::school_work_types($schedule_id);

        $this->student = DB::table('enrolled_students as es')
            ->select(
                's.id' ,
                's.college_id' ,
                'department_id' ,
                'year_level' ,
                DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix) AS fullname'),
                's.code' ,
                'first_name' ,
                'middle_name' ,
                'last_name' ,
                'suffix' ,
                'email' ,
                's.is_active' ,
                'c.name as college',
                'd.name as department',
                'c.code as college_code',
                'd.code as department_code',
                'yl.year_level'
            )
            ->leftJoin('students as s','s.id','es.student_id')
            ->leftJoin('colleges as c','c.id','s.college_id')
            ->leftJoin('departments as d','d.id','s.department_id')
            ->leftJoin('year_levels as yl','yl.id','s.year_level_id')
            ->where('es.schedule_id','=',$schedule_id)
            ->where('es.student_id','=',$student_id)
            ->first();

        $student_id = [
            $this->student->id
        ];

        foreach ($this->terms as $key => $value) {
            self::student_scores($student_id,$schedule_id,$value->id);
        }
        // dd($this->school_work_types,$this    ->student_scores);

    }

    public function student_scores($student_ids,$schedule_id,$term_id){
        $this->student_scores[$term_id] = [];
         foreach ($student_ids as $v_key => $v_value) {
            $scores = [];
            foreach ($this->school_work_types as $key => $value) {
                $school_works = DB::table('school_works_types as swt')
                    ->select(
                        'swt.school_work_type',
                        'swt.id as school_work_type_id',
                        'swt.weight',
                        'sw.id',
                        'sw.max_score',
                        'score',
                        'sws.id as score_id',
                        'sw.school_work_name',
                        DB::raw('DATE_FORMAT(sw.schedule_date, "%M %d, %Y") AS schedule_date'),
                    )
                    ->leftjoin('school_works as sw','sw.school_work_type_id','swt.id')
                    ->leftjoin('school_work_scores as sws','sws.school_work_id','sw.id')
                    ->where('swt.schedule_id','=',$schedule_id)
                    ->where('swt.term_id','=',$term_id)
                    ->where('swt.id','=', $value->id)
                    ->where(function ($query) use ($v_value) {
                        $query->whereNull('sws.student_id') // if no score yet
                        ->orWhere('sws.student_id', $v_value);
                    })
                    // ->leftjoin('school_work_scores as sws','sws.school_work_id','sw.id')
                    ->orderBy('sw.number_order','asc')
                    ->get()
                    ->toArray();
                if( $school_works ){
                    foreach ($school_works as $s_key => $s_value) {
                        if($s_value->id){
                            array_push( $scores,[
                                'score_id' => $s_value->score_id,
                                'schedule_id' => $schedule_id,
                                'student_id'=>$v_value,
                                'term_id' => $term_id,
                                'school_work_id' => $s_value->id,
                                'school_work_type_id' => $s_value->school_work_type_id,
                                'school_work_name' => $s_value->school_work_name,
                                'school_work_type' => $s_value->school_work_type,
                                'weight'=> $s_value->weight,
                                'key' => $key ,
                                'score' => $s_value->score,
                                'max_score' =>$s_value->max_score,
                                'schedule_date'=>$s_value->schedule_date
                            ]);
                        }
                    }
                }
            }
            array_push($this->student_scores[$term_id],$scores);
        }
        $school_work_types = DB::table('school_works_types as swt')
            ->where('swt.schedule_id','=',$schedule_id)
            ->where('swt.term_id','=',$term_id)
            ->leftjoin('school_works as sw','sw.school_work_type_id','swt.id')
            ->orderBy('swt.number_order','asc')
            ->get()
            ->toArray();
            
    }

    public function fetch_terms($schedule_id){
        $this->terms = DB::table('terms')
            ->where('schedule_id','=',$schedule_id)
            ->get()
            ->toArray();
    }
    public function school_work_types($schedule_id){
        $this->school_work_types = DB::table('school_works_types')
            ->where('schedule_id','=',$schedule_id)
            ->orderBy('number_order','asc')
            ->get()
            ->toArray();

        $this->school_work_type_value = [];
        foreach ($this->school_work_types as $key => $value) {
            array_push($this->school_work_type_value,['val'=>$value->weight]);
        }
    }
    
    public function render()
    {
        return view('livewire.student.my-grade-components.my-grade-components')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
