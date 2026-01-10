<?php

namespace App\Livewire\Admin\Evaluation;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceLists extends Component
{

    public $title = "Attendance";

    public $route = "attendance";
    
    public $colleges = [];

    public $students = [];

    public $departments = [];

    public $curriculum = NULL;

    public $date;

    public $school_work_types = [];
    public $student_scores = [];
    public $detail = [
        'id'=>NULL,
        'student_id'=> NULL,
        'schedule_id'=> NULL,
        'term_id'=> NULL,
        'date'=> NULL,
    ];
    public function mount($schedule_id,$id){
        $this->detail['id'] = $id;
        $school_work_attendance = DB::table('school_works')  
        ->where('id','=',$id)
        ->first();
         $this->detail['schedule_id'] = $schedule_id;
        $this->detail['term_id'] = $school_work_attendance->term_id;
        $this->detail['date'] = Carbon::create($school_work_attendance->schedule_date)->format('F d, Y');
        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        $this->departments = DB::table('departments')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        self::school_work_types($this->detail['schedule_id']);

    }
    public function render()
    {

        $table_data =  DB::table('enrolled_students as es')
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
            ->where('es.schedule_id','=',$this->detail['schedule_id']);
        

        if (!empty($this->filters['search'])) {
            $table_data
            ->where('s.code','like','%'.$this->filters['search'] .'%')
            ->orwhere('s.email','like','%'.$this->filters['search'] .'%')
            ->orwhere(DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix)'), 'like','%'.$this->filters['search'] .'%');
        }
      
        $table_data = $table_data
            ->orderBy('s.is_active','desc')
            ->orderBy('s.id', 'desc')
            ->paginate(10)->withPath(url()->current());

        $student_id = $table_data->pluck('id');

        // foreach ($student_id as $v_key => $v_value) {
        //     foreach ($this->school_work_types as $key => $value) {

        //         $student_school_works = DB::table('school_works as sw')
        //             ->select(
        //                 'sw.id',
        //                 'sw.schedule_id',
        //                 'sw.term_id',
        //                 'school_work_name',
        //                 'school_work_type_id',
        //                 'sw.max_score',
        //                 'schedule_date',
        //                 'student_id',
        //                 'score',
        //                 'school_work_id'

        //             )
        //             ->leftjoin('school_work_scores as sws','sws.school_work_id','sw.id')
        //             ->where('sw.schedule_id','=',$this->detail['schedule_id'])
        //             ->where('sw.term_id','=',$this->detail['term_id'])
        //             ->where('sw.school_work_type_id','=',$value->id)
        //             ->get()
        //             ->toArray();

        //         foreach ($student_school_works as $ssw_key => $ssw_value) {
        //             if(
        //                 !DB::table('school_work_scores as sws')
        //                     ->where('sws.schedule_id','=',$this->detail['schedule_id'])
        //                     ->where('sws.term_id','=',$this->detail['term_id'])
        //                     ->where('sws.student_id','=',$v_value)
        //                     ->where('sws.school_work_id','=',$ssw_value->id)
        //                     ->first()
        //             ){
        //                  DB::table('school_work_scores')
        //                     ->insert([
        //                     'id' => NULL,
        //                     'schedule_id' => $this->detail['schedule_id'],
        //                     'student_id' => $v_value,
        //                     'term_id' => $this->detail['term_id'],
        //                     'school_work_id' => $ssw_value->id,
        //                     'score' => NULL,
        //                     'max_score' => $ssw_value->max_score,
        //                 ]);
        //             }
        //         }
        //     }
        // }
        self::student_scores($student_id);


        return view('livewire.admin.evaluation.attendance-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }

    public function school_work_types($schedule_id){
        $this->school_work_types = DB::table('school_works_types')
            ->where('schedule_id','=',$schedule_id)
            ->where('term_id','=',$this->detail['term_id'])
            ->orderBy('number_order','asc')
            ->get()
            ->toArray();

        $this->school_work_type_value = [];
        foreach ($this->school_work_types as $key => $value) {
            array_push($this->school_work_type_value,['val'=>$value->weight]);
        }
    }

    public function student_scores($student_ids){
        $this->student_scores = [];
         foreach ($student_ids as $v_key => $v_value) {
            $scores = [];
            foreach ($this->school_work_types as $key => $value) {
                $school_works = DB::table('school_works_types as swt')
                    ->select(
                        'swt.id as school_work_type_id',
                        'swt.weight',
                        'sw.id',
                        'sw.max_score',
                        'score',
                        'sws.id as score_id',
                    )
                    ->leftjoin('school_works as sw','sw.school_work_type_id','swt.id')
                    ->leftjoin('school_work_scores as sws','sws.school_work_id','sw.id')
                    ->where('swt.schedule_id','=',$this->detail['schedule_id'])
                    ->where('swt.term_id','=',$this->detail['term_id'])
                    ->where('swt.id','=', $value->id)
                    ->where('sw.id','=', $this->detail['id'])
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
                                'schedule_id' => $this->detail['schedule_id'],
                                'student_id'=>$v_value,
                                'term_id' => $this->detail['term_id'],
                                'school_work_id' => $s_value->id,
                                'school_work_type_id' => $s_value->school_work_type_id,
                                'weight'=> $s_value->weight,
                                'key' => $key ,
                                'score' => $s_value->score,
                                'max_score' =>$s_value->max_score,
                            ]);
                        }
                    }
                }
            }
            array_push($this->student_scores,$scores);
        }
            
    }

    public function updateScore(
        $score_id,
        $schedule_id,
        $student_id,
        $term_id,
        $school_work_id,
        $score,
        $max_score,){
            $score = (strlen($score) ? $score : NULL);
            if($score > $max_score){
            $this->dispatch('notifyWarning', 
            'Score exceeds '.$max_score.' !',
                '');
            return;
        }   
        if($score_id){
            DB::table('school_work_scores')
                ->where('id','=',$score_id)
                ->update([
                'schedule_id' => $schedule_id,
                'student_id' => $student_id,
                'term_id' => $term_id,
                'school_work_id' => $school_work_id,
                'score' => $score,
                'max_score' => $max_score,
            ]);
        }else{
            DB::table('school_work_scores')
                ->insert([
                'id' => NULL,
                'schedule_id' => $schedule_id,
                'student_id' => $student_id,
                'term_id' => $term_id,
                'school_work_id' => $school_work_id,
                'score' => $score,
                'max_score' => $max_score,
            ]);
        }
        $this->dispatch('notifySuccess', 
            'Updated successfully!',
                '');
    }
    public function viewAttendance($modal_id){
        $this->dispatch('openModal',modal_id:$modal_id);
        $this->dispatch('openAttendanceModal', [
            'obj' => [
                'schedule_id' => $this->detail['schedule_id'],
                'term_id' => $this->detail['term_id'],
            ]
        ]);
    }

    public function viewDetails($modal_id){
        self::getDetails();
        $this->dispatch('openModal',modal_id:$modal_id);
    }

      public function getDetails(){
        $this->curriculum = DB::table('curriculums as cl')
            ->select(
                'cl.id',
                's.college_id' ,
                's.department_id' ,
                's.description',
                's.prerequisite_subject_id' ,
                'c.name as college_name',
                'd.name as department_name',
                'c.code as college_code',
                'd.code as department_code',
                'pr.subject_id as prerequisite_subject_id',
                'pr.subject_code as prerequisite_subject_code',
                'r.code as room_code',
                'r.name as room_name',
                's.is_active',
                'sh.schedule_from',
                'sh.schedule_to',
                'sh.day' ,
                'sh.is_lec' ,
                'sh.subject_id',
                'cl.room_id',
                'cl.schedule_id',
                'cl.faculty_id',
                DB::raw('CONCAT(sy.year_start," - ",sy.year_end) as school_year'),
                DB::raw('CONCAT(c.code," ",c.name) as college'),
                DB::raw('CONCAT(d.code," ",d.name) as department'),
                DB::raw('CONCAT_WS(" ", u.first_name, u.middle_name, u.last_name, u.suffix) AS faculty_fullname'),
                DB::raw('sm.semester'), 
                DB::raw('yl.year_level'),
                DB::raw('CONCAT(s.subject_id," - ",s.subject_code) as subject'),
                DB::raw("CONCAT(DATE_FORMAT(sh.schedule_from, '%h:%i %p'), ' ', DATE_FORMAT(sh.schedule_to, '%h:%i %p')) as schedule"),
                's.lecture_unit',
                's.laboratory_unit' ,
                DB::raw('CONCAT(r.code," ",r.name) as room'),

            )
            ->leftJoin('school_years as sy','sy.id','cl.school_year_id')
            ->leftJoin('schedules as sh','sh.id','cl.schedule_id')
            ->leftJoin('subjects as s','s.id','sh.subject_id')
            ->leftJoin('rooms as r','r.id','cl.room_id')
            ->leftJoin('faculty as f','f.id','cl.faculty_id')
            ->leftJoin('users as u','u.id','f.user_id')
            ->leftJoin('colleges as c','c.id','s.college_id')
            ->leftJoin('departments as d','d.id','s.department_id')
            ->leftjoin('subjects as pr','pr.id','s.prerequisite_subject_id')
            ->leftjoin('semesters as sm','sm.id','cl.semester_id')
            ->leftjoin('year_levels as yl','yl.id','cl.year_level_id')
            ->where('cl.id','=',$this->detail['schedule_id'])
            ->first();
    }
}
