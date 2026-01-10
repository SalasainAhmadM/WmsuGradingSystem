<?php

namespace App\Livewire\Admin\Student;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class EditStudent extends Component
{
    public $title = "Student";
    
    public $route = 'student';

    public $colleges = [];

    public $departments = [];

    public $year_levels = [];

     public $detail = [
        'id'=> NULL,
        'college_id'=> NULL,
        'department_id'=> NULL,
        'year_level_id'=> NULL,
        'code'=> NULL,
        'email'=> NULL,
        'first_name'=> NULL,
        'middle_name'=> NULL,
        'last_name'=> NULL,
        'suffix'=> NULL,
    ];
    

    public function rules(){
        return [
            'detail.college_id' => 'required|exists:colleges,id',
            'detail.department_id' => 'required|exists:departments,id',
            'detail.year_level_id' => 'required|exists:year_levels,id',
            'detail.code' => 'required|string|max:100|unique:students,code,' . $this->detail['id'],
            'detail.first_name' => 'required|string|max:255',
            'detail.middle_name' => 'nullable|string|max:255',
            'detail.last_name' => 'required|string|max:255',
            'detail.suffix' => 'nullable|string|max:255',
            'detail.email' => [
                'required',
                'email',
                'unique:users,email,'.$this->detail['user_id'],
                'regex:/^[a-zA-Z0-9._%+-]+@wmsu\.edu\.ph$/'
            ],
        ];
    }


    public function messages(){
        return [
            'detail.college_id.required' => 'The college is required.',
            'detail.college_id.exists' => 'The selected college does not exist.',
            'detail.department_id.required' => 'The department is required.',
            'detail.department_id.exists' => 'The selected department does not exist.',
            'detail.year_level_id.required' => 'The year level is required.',
            'detail.year_level_id.exists' => 'The selected year level does not exist.',
            'detail.code.required' => 'The student code is required.',
            'detail.code.unique' => 'This code is already taken.',
            'detail.first_name.required' => 'First name is required.',
            'detail.last_name.required' => 'Last name is required.',
            'detail.email.email' => 'The email must be a valid email address.',
            'detail.email.required' => 'The email is required.',
            'detail.email.unique' => 'The email has already been taken.',
            'detail.email.regex' => 'The email must be @wmsu.edu.ph domain.',
        ];
    }

    public function updatedDetailCollegeId($value){
        $this->detail['department_id'] = null;
    }
    public function mount($id){
        $detail = DB::table('students as s')
            ->select(
                's.id' ,
                's.college_id' ,
                'department_id' ,
                'year_level' ,
                DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix) AS fullname'),
                's.code' ,
                's.first_name' ,
                's.middle_name' ,
                's.last_name' ,
                's.suffix' ,
                's.email' ,
                's.is_active' ,
                'c.name as college',
                'd.name as department',
                'yl.year_level',
                's.year_level_id',
                's.user_id'
            )
            ->leftJoin('colleges as c','c.id','s.college_id')
            ->leftJoin('departments as d','d.id','s.department_id')
            ->leftJoin('year_levels as yl','yl.id','s.year_level_id')
            ->leftJoin('users as u','u.id','s.user_id')
            ->where('s.id','=',$id)
            ->first();

        $this->detail = [
            'id'=>$detail->id,
            'college_id'=> $detail->college_id,
            'department_id'=> $detail->department_id,
            'year_level_id'=> $detail->year_level_id,
            'code'=> $detail->code,
            'email'=> $detail->email,
            'first_name'=> $detail->first_name,
            'middle_name'=> $detail->middle_name,
            'last_name'=> $detail->last_name,
            'suffix'=> $detail->suffix,
            'user_id'=> $detail->user_id,
        ];

        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        $this->departments = DB::table('departments')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        $this->year_levels = DB::table('year_levels')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
    }

    public function save(){
        $this->validate($this->rules(), $this->messages());

        DB::table('users as u')
            ->where('u.id','=',$this->detail['user_id'])
            ->update([
                'first_name'=> $this->detail['first_name'],
                'middle_name'=> $this->detail['middle_name'],
                'last_name'=> $this->detail['last_name'],
                'suffix'=> $this->detail['suffix'],
                'email'=> $this->detail['email'],
            ]);
         if(DB::table('students')
            ->where('id','=',$this->detail['id'])
            ->update([
            'college_id'=> $this->detail['college_id'],
            'department_id'=> $this->detail['department_id'],
            'year_level_id'=> $this->detail['year_level_id'],
            'code'=> $this->detail['code'],
            'email'=> $this->detail['email'],
            'first_name'=> $this->detail['first_name'],
            'middle_name'=> $this->detail['middle_name'],
            'last_name'=> $this->detail['last_name'],
            'suffix'=> $this->detail['suffix'],
        ])){
        }
        $this->dispatch('notifySuccess', 
        'Updated successfully!',
            route($this->route.'-lists'));
    }
    public function render()
    {
        return view('livewire.admin.student.edit-student')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
