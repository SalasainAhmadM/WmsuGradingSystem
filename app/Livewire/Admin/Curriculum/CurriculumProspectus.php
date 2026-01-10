<?php

namespace App\Livewire\Admin\Curriculum;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class CurriculumProspectus extends Component
{
    use WithPagination;

    public $title = "Prospectu";

    public $route = "prospectus";

    public $subjects = [];

    public $year_levels = [];

    public $semesters = [];

    public $edit = false;

    public $is_table = false;

    public $permanent = false;
    public $curriculum = [
        'id'=> NULL,
        'school_year_id' => NULL,
        'college_id' => NULL,
        'department_id' => NULL,
        'prospectus' => NULL,
        'is_editable' => true,
    ];
     public $detail = [
        'id' => NULL,
        'curriculum_id' => NULL,
        'year_level_id' => NULL,
        'semester_id' => NULL,
        'subject_id' => NULL,
    ];

     public $filters = [
        'search'=> NULL,
        'college_id' =>NULL,
        'department_id' =>NULL,
    ];

    
    public function mount($curriculum_id){

            $this->curriculum['id'] = $curriculum_id;
        // $this->curriculum['school_year_id'] = DB::table('school_years')->where(DB::raw('concat(year_start,"-",year_end)'),'=',$school_year)->first()->id;
        // $this->curriculum['college_id'] = DB::table('colleges')->where('code','=',$college)->first()->id;
        // $this->curriculum['department_id'] = DB::table('departments')->where('code','=',$department)->first()->id;

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

        self::getCurriculum();
    }

    public function render()
    {
        $table_data = DB::table('curriculum_subjects as cs')
            ->select(
                'cs.id',
                's.subject_id' ,
                's.subject_code' ,
                's.description',
                's.prerequisite_subject_id' ,
                'sm.semester',
                'sm.id as semester_id',
                'yl.year_level',
                'yl.id as year_level_id',
                's.lecture_unit',
                's.laboratory_unit' ,
            )
            ->where('curriculum_id','=',$this->curriculum['id'])
            ->leftjoin('subjects as s','s.id','cs.subject_id')
            ->leftjoin('year_levels as yl','yl.id','cs.year_level_id')
            ->leftjoin('semesters as sm','sm.id','cs.semester_id');

            // if(strlen($this->detail['year_level_id'])){
            //     $table_data = $table_data->where('cs.year_level_id','=',$this->detail['year_level_id']);
            // }
            // if(strlen($this->detail['semester_id'])){
            //     $table_data = $table_data->where('cs.semester_id','=',$this->detail['semester_id']);
            // }
            $table_data = $table_data->orderBy('yl.year_level')   // Order first by year level
            ->orderBy('yl.year_level') 
            ->orderBy('sm.semester');
             if (!empty($this->filters['search'])) {
                $table_data
                ->where('s.subject_id','like','%'.$this->filters['search'] .'%');
                // ->orwhere('s.subject_code','like','%'.$this->filters['search'] .'%');
            }
            if($this->is_table){
                $table_data = $table_data
                ->paginate(10)->withPath(url()->current());
            }else{
                $table_data = $table_data
                ->get()
                ->toArray();
            }
        return view('livewire.admin.curriculum.curriculum-prospectus',[
            'table_data' =>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }

    public function add($modal_id){
        $this->detail = [
            'id' => NULL,
            'curriculum_id' => $this->curriculum['id'],
            'year_level_id' => NULL,
            'semester_id' => NULL,
            'subject_id' => NULL,
        ];
        self::subjectLists();
        self::yearLevels();
        $this->dispatch('openModal',modal_id:$modal_id);
    }

    public function saveAdd($modal_id){
        if(
           $res = DB::table('schedulings')
                ->where('school_year_id' ,'=', DB::table('school_years')->where(DB::raw('concat(year_start,"-",year_end)'),'=',$this->detail['school_year'])->first()->id)
                ->where('college_id' ,'=', DB::table('colleges')->where('code','=',$this->detail['college'])->first()->id)
                ->where('department_id' ,'=', DB::table('departments')->where('code','=',$this->detail['department'])->first()->id)
                ->where('year_level_id' ,'=', DB::table('year_levels')->where('year_level','=',$this->detail['year_level'])->first()->id)
                ->where('semester_id' ,'=', DB::table('semesters')->where('semester','=',$this->detail['semester'])->first()->id)
                ->where('subject_id' ,'=', $this->detail['subject_id'])
                ->first()
        ){
            throw \Illuminate\Validation\ValidationException::withMessages([
                'detail.subject_id' => 'Schedule already exists.',
            ]);
        }
    }

    public function getCurriculum(){
        $curriculum = DB::table('curriculums')
            ->where('id','=',$this->curriculum['id'])
            ->first();

        if($curriculum){
            $curriculum = (array)$curriculum;
            $this->curriculum = [
                'id'=> $curriculum['id'],
                'school_year_id' => $curriculum['school_year_id'],
                'college_id' => $curriculum['college_id'],
                'department_id' => $curriculum['department_id'],
                'prospectus' => $curriculum['prospectus'],
                'is_editable' => $curriculum['is_editable'],
            ];
            $this->permanent = (!$curriculum['is_editable']) ;
            $this->detail['curriculum_id'] = $curriculum['id']; 
        }else{
            $this->edit = true;
        }
    }

    public function view_prospectus(){
        $this->edit = true;
    }

    public function save_prospectus(){
        $rules = [
            'curriculum.id' => 'nullable|integer',
            'curriculum.school_year_id' => 'nullable|integer',
            'curriculum.college_id' => 'nullable|integer',
            'curriculum.department_id' => 'nullable|integer',
            'curriculum.prospectus' => 'required|string', // REQUIRED field
        ];

        $messages = [
            'curriculum.prospectus.required' => 'The prospectus field is required.',
        ];
        
        $this->validate($rules, $messages);

        if(intval($this->curriculum['id'])){
            if(DB::table('curriculums')
                ->where('id','=',$this->curriculum['id'])
                ->update($this->curriculum)){
            }
        }else{
            if(DB::table('curriculums')
                ->insert($this->curriculum)){
            }
        }
        $this->dispatch('notifySuccess', 
            'Updated successfully!',
                '');
        if($this->permanent){
            DB::table('curriculums')
                ->where('id','=',$this->curriculum['id'])
                ->update(['is_editable'=>false]);
        }
        self::getCurriculum();
        $this->edit = false;
    }

    public function subjectLists(){
        $this->subjects = DB::table('subjects')
        ->where('is_active','=',1)
        ->get()
        ->toArray();
    }

    public function yearLevels(){
        $this->year_levels = DB::table('year_levels')
        ->where('is_active','=',1)
        ->get()
        ->toArray();
    }

    public function addSubject($modal_id){
        
        $year_level = DB::table('year_levels')
        ->orderBy('is_active','desc')
        ->where('is_active','=',1)
        ->get()
        ->first();
        
        $semester = DB::table('semesters as s')
        ->orderBy('s.is_active','desc')
        ->orderBy('s.id', 'asc')
        ->where('is_active','=',1)
        ->get()
        ->first();

        $subject = DB::table('subjects')
            ->where('id','=',$this->detail['subject_id'])
            ->first();
        if( 
            $semester->id == $this->detail['semester_id'] && 
            $this->detail['year_level_id'] == $year_level->id &&
            count(json_decode($subject->prerequisite_subject_id))>0
            ){
            throw \Illuminate\Validation\ValidationException::withMessages([
                'detail.subject_id' => 'Subject cannot add at '.$year_level->year_level.' '.$semester->semester .' that has prerequisites.',
            ]);
        }
        
        $rules = [
            'detail.curriculum_id' => 'required|integer',
            'detail.year_level_id' => 'required|integer',
            'detail.semester_id' => 'required|integer',
            'detail.subject_id' => [
                'required',
                'integer',
                Rule::unique('curriculum_subjects','subject_id')
                    ->where(function ($query) {
                        return $query->where('curriculum_id', $this->detail['curriculum_id'])
                            ->where('subject_id', $this->detail['subject_id']);
                    })
                    ->ignore($this->detail['id']), // Exclude current row if updating
            ],
        ];

        $messages = [
            'detail.subject_id.unique' => 'This subject already exists for the curriculum.',
            'detail.curriculum_id.required' => 'Curriculum is required.',
            'detail.year_level_id.required' => 'Year level is required.',
            'detail.semester_id.required' => 'Semester is required.',
            'detail.subject_id.required' => 'Subject is required.',
        ];

        $this->validate($rules, $messages);

       if( DB::table('curriculum_subjects')
            ->insert($this->detail)){
            $this->dispatch('notifySuccess', 
            'Added successfully!',
                '');

                $this->detail['subject_id'] = NULL;
            }

        // $this->dispatch('closeModal',modal_id:$modal_id);
    }

    public function view($id,$modal_id){
     
        $detail = DB::table('curriculum_subjects as cs')
            ->where('id','=',$id)
            ->first();

        $this->detail = [
            'id'=> $detail->id,
            'curriculum_id'=> $detail->curriculum_id,
            'year_level'=> $detail->year_level_id,
            'semester_id'=> $detail->semester_id,
            'subject_id'=> $detail->subject_id,
        ];
        $this->dispatch('openModal',modal_id:$modal_id);
        
    }

    public function saveDelete($id,$modal_id){
        if(DB::table('curriculum_subjects')
            ->where('id','=',$id)
            ->delete()){

            $this->dispatch('notifySuccess', 
            'Deleted successfully!',
                '');
            $this->dispatch('closeModal',modal_id:$modal_id);
        }

    }
}
