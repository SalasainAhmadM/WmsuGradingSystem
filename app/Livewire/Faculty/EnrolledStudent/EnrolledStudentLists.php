<?php

namespace App\Livewire\Faculty\EnrolledStudent;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class EnrolledStudentLists extends Component
{
    use WithPagination;
    public $title = "Enrolled-student";

    public $route = "enrolled-student";

    public $filters = [
        'search'=> NULL,
        'college_id' =>NULL,
        'department_id' =>NULL,
    ];
    
    public $colleges = [];

    public $students = [];

    public $departments = [];

    public $school_years = [];

    public $semesters = [];

    public $subjects = [];

    public $year_levels = []; 

    public $schedule = NULL;
    public $studentFilter = [
        'college_id'=> NULL,
        'studentFilter'=> NULL
    ];

    public $detail = [
        'student_id'=> NULL,
        'schedule_id'=> NULL,
    ];

    protected $rules = [
        'detail.student_id' => 'required|exists:students,id',
        'detail.schedule_id' => 'required|exists:curriculums,id',
    ];

    protected $messages = [
        'detail.student_id.required' => 'Student is required.',
        'detail.student_id.exists' => 'The selected student does not exist.',
        'detail.schedule_id.required' => 'Curriculum is required.',
        'detail.schedule_id.exists' => 'The selected schedule does not exist.',
    ];



    public $school_year;
    public $semester;
    public $school_year_id;
    public $semester_id;
    public function mount($school_year, $semester, $schedule_id){
        $this->school_year = $school_year;
        $this->semester = $semester;
        $this->school_year_id = DB::table('school_years')->where(DB::raw('concat(year_start,"-",year_end)'),'=',$school_year)->first()->id;
        $this->semester_id = DB::table('semesters')->where(DB::raw('semester'),'=',$semester)->first()->id;


        $this->detail['schedule_id'] = $schedule_id;
        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        $this->departments = DB::table('departments')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
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
        return view('livewire.faculty.enrolled-student.enrolled-student-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }

    public function add($modal_id){
        $this->studentFilter = [
            'college_id'=> NULL,
            'studentFilter'=> NULL
        ];

        self::studentList();
        $this->dispatch('openModal',modal_id:$modal_id);
    }

    public function studentList(){
        $this->detail['student_id'] = NULL;
         $this->students = DB::table('students as s')
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
            ->leftJoin('colleges as c','c.id','s.college_id')
            ->leftJoin('departments as d','d.id','s.department_id')
            ->leftJoin('year_levels as yl','yl.id','s.year_level_id');
        
        if($this->studentFilter['college_id']){
            $this->students->where('s.college_id', '=',$this->studentFilter['college_id']);
        }

        if (!empty($this->studentFilter['search'])) {
            $this->students
            ->where('s.code','like','%'.$this->studentFilter['search'] .'%')
            ->orwhere('s.email','like','%'.$this->studentFilter['search'] .'%')
            ->orwhere(DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix)'), 'like','%'.$this->studentFilter['search'] .'%');
        }
        $this->students = $this->students
            ->where('s.is_active','=',1)
            ->orderBy('s.is_active','desc')
            ->orderBy('s.id', 'desc')
            ->get()
            ->toArray();
    }
    
    public function saveAdd($modal_id){
        $this->validate();

        if(DB::table('enrolled_students')
            ->where('student_id','=',$this->detail['student_id'])
            ->where('schedule_id','=',$this->detail['schedule_id'])
            ->first()
            ){
            throw \Illuminate\Validation\ValidationException::withMessages([
                'detail.student_id' => 'Student is already added.',
            ]);
        }

        $inserted = DB::table('enrolled_students')
            ->insert($this->detail)
            ;
        if ($inserted) {
            // You can dispatch success notification or redirect here
            $this->dispatch('notifySuccess', 
            'Added successfully!',
                '');
            $this->dispatch('closeModal',modal_id : $modal_id);
        }
    }

    public function deleteStudent($id,$modal_id){
        $this->detail['student_id'] = $id;
        $this->dispatch('openModal',modal_id:$modal_id);
    }

    public function saveDelete($modal_id){
        if(DB::table('enrolled_students')
            ->where('student_id','=',$this->detail['student_id'])
            ->where('schedule_id','=',$this->detail['schedule_id'])
            ->delete()
            ){
                $this->dispatch('notifySuccess', 
        'Successfully deleted!',
            '');
            $this->dispatch('closeModal',modal_id : $modal_id);
        }
    }

    public function viewDetails($modal_id){
        self::getDetails();
        $this->dispatch('openModal',modal_id:$modal_id);
    }
    
    public function getDetails(){
        $this->schedule = DB::table('curriculums as cl')
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

    public function viewAttendance($modal_id){
        $this->dispatch('openModal',modal_id:$modal_id);
        $this->dispatch('openAttendanceModal', [
            'obj' => [
                'schedule_id' => $this->detail['schedule_id'],
                'term_id' => $this->detail['term_id'],
            ]
        ]);
    }
}
