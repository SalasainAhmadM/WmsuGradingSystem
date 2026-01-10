<?php

namespace App\Livewire\Admin\Subjects;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class EditSubject extends Component
{
    public $title = "Subject";

    public $route = 'subject';
    public $colleges = [];

    public $departments = [];

    public $subjects = [];
    public $detail = [
        'id' => NULL,
        'college_id' => NULL,
        'department_id' => NULL,
        'subject_id' => NULL,
        'subject_code' => NULL,
        'description'=> NULL,
        'prerequisite_subject_id' => [],
        'lecture_unit'=> 3,
        'laboratory_unit' => 0,
    ];

    public $is_major_or_minor = true;

    public function rules(){
        return [
            'detail.college_id' => 'nullable|exists:colleges,id',
            'detail.department_id' => 'nullable|exists:departments,id',
            'detail.prerequisite_subject_id' => 'nullable|array',
            'detail.subject_id' => 'required|unique:subjects,subject_id,'.$this->detail['id'],
            'detail.subject_code' => 'required|unique:subjects,subject_code,'.$this->detail['id'],
            'detail.lecture_unit' => 'integer|min:1',
            'detail.laboratory_unit' => 'integer|min:0',
        ];
    }

    public function updatedIsMajorOrMinor($value){
        $this->detail['college_id'] = null;
        $this->detail['department_id'] = null;
    }

    public function updatedDetailCollegeId($value){
        $this->detail['department_id'] = null;
    }
    
    public function messages(){
        return [
            'detail.college_id.required' => 'The college is required.',
            'detail.college_id.exists' => 'The selected college does not exist.',
            
            'detail.department_id.required' => 'The department is required.',
            'detail.department_id.exists' => 'The selected department does not exist.',
            
            'detail.prerequisite_subject_id.exists' => 'The selected prerequisite subject is invalid.',
            
            'detail.subject_id.required' => 'The subject ID is required.',
            'detail.subject_id.unique' => 'The subject ID has already been taken.',
            
            'detail.subject_code.required' => 'The subject code is required.',
            'detail.subject_code.unique' => 'The subject code has already been taken.',
            
            'detail.lecture_unit.required' => 'Lecture unit is required.',
            'detail.lecture_unit.integer' => 'Lecture unit must be an integer.',
            
            'detail.laboratory_unit.integer' => 'Laboratory unit must be an integer.',
            'detail.lecture.integer' => 'Laboratory unit must be an integer.',
            'detail.lecture_unit.min' => 'Laboratory unit must greater than 0.',
            'detail.prerequisite_subject_id.array' => 'The day must be a valid array.',
        ];
    }


    public function save(){
        $this->validate();

        if (
            intval( $this->detail['lecture_unit']) < 1 &&
            intval( $this->detail['laboratory_unit']) < 1
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'detail.lecture_unit' => 'Either Lecture Unit or Laboratory Unit must be at least 1.',
                'detail.laboratory_unit' => 'Either Lecture Unit or Laboratory Unit must be at least 1.',
            ]);
        }
        foreach ($this->detail['prerequisite_subject_id'] as $key => $value) {
            if($value == $this->detail['id']){
                throw \Illuminate\Validation\ValidationException::withMessages([
                   'detail.prerequisite_subject_id' => 'A subject cannot be its own prerequisite.',
               ]);
            }
        }
       
        if(DB::table('subjects')
            ->where('id','=',$this->detail['id'])
            ->update([
            'college_id'=> $this->detail['college_id'],
            'department_id'=> $this->detail['department_id'],
            'subject_id' => $this->detail['subject_id'],
            'subject_code' => $this->detail['subject_code'],
            'description'=> $this->detail['description'],
            'prerequisite_subject_id' => json_encode($this->detail['prerequisite_subject_id']),
            'lecture_unit'=> intval($this->detail['lecture_unit']),
            'laboratory_unit' => intval($this->detail['laboratory_unit']),
        ])){
        }
        $this->dispatch('notifySuccess', 
        'Updated successfully!',
            route($this->route.'-lists'));
    }


    public function mount($id){
        $detail = DB::table('subjects as s')
            ->where('id','=',$id)
            ->select(
                's.id' ,
                's.college_id' ,
                's.department_id' ,
                's.subject_id' ,
                's.subject_code' ,
                's.description',
                's.prerequisite_subject_id' ,
                's.lecture_unit',
                's.laboratory_unit' ,
            )
            ->first();

        $this->detail = [
            'id' => $detail->id,
            'college_id' => $detail->college_id,
            'department_id' => $detail->department_id,
            'subject_id' => $detail->subject_id,
            'subject_code' => $detail->subject_code,
            'description'=> $detail->description,
            'prerequisite_subject_id' => json_decode($detail->prerequisite_subject_id),
            'lecture_unit'=> $detail->lecture_unit,
            'laboratory_unit' => $detail->laboratory_unit,
        ];

        if(intval($detail->college_id)){
            $this->is_major_or_minor = true;
        }else{
            $this->is_major_or_minor = false;
        }

        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        $this->departments = DB::table('departments')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        $this->subjects = DB::table('subjects')
            ->where('is_active','=',1)
            ->where('id','<>',$id)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.subjects.edit-subject')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
