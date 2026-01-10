<?php

namespace App\Livewire\Admin\Student;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class AddStudent extends Component
{
    public $title = "Student";

    public $route = "student";

    public $colleges = [];

    public $departments = [];

    public $year_levels = [];

     public $detail = [
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
            'detail.code' => 'required|string|max:100|unique:students,code',
            'detail.email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@wmsu\.edu\.ph$/'
            ],
            'detail.first_name' => 'required|string|max:255',
            'detail.middle_name' => 'nullable|string|max:255',
            'detail.last_name' => 'required|string|max:255',
            'detail.suffix' => 'nullable|string|max:255',
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
            'detail.confirm_password' => 'required|same:detail.password',
            'detail.password.required' => 'The password field is required.',
            'detail.password.min' => 'The password must be at least 8 characters.',
            'detail.password.mixed_case' => 'The password must contain both uppercase and lowercase letters.',
            'detail.password.letters' => 'The password must include at least one letter.',
            'detail.password.numbers' => 'The password must include at least one number.',
            'detail.password.symbols' => 'The password must include at least one special character.',
            'detail.password.uncompromised' => 'This password has appeared in a data leak. Please choose a different password.',
            
            'detail.confirm_password.required_with' => 'Please confirm your password.',
            'detail.confirm_password.required' => 'The confirm password field is required.',
            'detail.confirm_password.same' => 'The password confirmation does not match.',
        ];
    }


    public function updatedDetailCollegeId($value){
        $this->detail['department_id'] = null;
    }
    public function mount(){
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

        $res = DB::table('users')
            ->insert([
                'first_name'=> $this->detail['first_name'],
                'middle_name'=> $this->detail['middle_name'],
                'last_name'=> $this->detail['last_name'],
                'suffix'=> $this->detail['suffix'],
                'email'=> $this->detail['email'],
                'password'=> Hash::make('Drusha01@2'),
                'admin_type'=> 3,
            ]);
        if($res){
            $user = DB::table('users')
                ->where('email','=',$this->detail['email'])
                ->first();
            if(DB::table('students')->insert([
                'user_id'=> $user->id,
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
                $this->dispatch('notifySuccess', 
                'Added successfully!',
                    route($this->route.'-lists'));
            }
        }
    }
    public function render(){
        return view('livewire.admin.student.add-student')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
