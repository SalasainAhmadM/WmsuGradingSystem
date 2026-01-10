<?php

namespace App\Livewire\Admin\Faculty;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class AddFaculty extends Component
{
    public $title = "Faculty";

    public $route = "faculty";

    public $colleges;
    public $departments;
    public $faculty_types;
    public $designations;
    public $academic_ranks;

    public $detail = [
        'id' => NULL,
        'college_id' => NULL,
        'department_id' => NULL,
        'code' => NULL,
        'first_name' => NULL,
        'middle_name' => NULL,
        'last_name' => NULL,
        'suffix' => NULL,
        'email' => NULL,
        'academic_rank_id' => NULL,  
        'designation_id' => NULL,
        'faculty_type_id' => NULL,
        'is_active' => 1,
        'release_time' => 'With Release Time',
        'hours_per_week' => NULL,
        'password'=>NULL,
        'confirm_password'=> NULL,
        'is_admin'=> false,
    ];

    public function rules(){
        return [
            'detail.college_id' => 'required|exists:colleges,id',
            'detail.department_id' => 'required|exists:departments,id',
            'detail.academic_rank_id' => 'required|exists:academic_ranks,id',
            'detail.designation_id' => 'required|exists:designations,id',
            'detail.faculty_type_id' => 'required|exists:faculty_types,id',

            'detail.code' => 'required|string|unique:faculty,code',
            'detail.first_name' => 'required|string',
            'detail.last_name' => 'required|string',
            'detail.email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@wmsu\.edu\.ph$/'
            ],
            'detail.password' => [
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'detail.confirm_password' => 'required|same:detail.password',
        ];
    }

    protected $messages = [
        'detail.college_id.required' => 'The college field is required.',
        'detail.college_id.exists' => 'The selected college does not exist.',

        'detail.department_id.required' => 'The department field is required.',
        'detail.department_id.exists' => 'The selected department does not exist.',

        'detail.academic_rank_id.required' => 'The academic rank field is required.',
        'detail.academic_rank_id.exists' => 'The selected academic rank does not exist.',

        'detail.designation_id.required' => 'The designation field is required.',
        'detail.designation_id.exists' => 'The selected designation does not exist.',

        'detail.faculty_type_id.required' => 'The faculty type field is required.',
        'detail.faculty_type_id.exists' => 'The selected faculty type does not exist.',

        'detail.code.required' => 'The faculty code is required.',
        'detail.code.unique' => 'The faculty code has already been taken.',

        'detail.first_name.required' => 'The first name is required.',
        'detail.last_name.required' => 'The last name is required.',

        'detail.email.email' => 'The email must be a valid email address.',
        'detail.email.required' => 'The email is required.',
        'detail.email.unique' => 'The email has already been taken.',
        'detail.email.regex' => 'The email must be @wmsu.edu.ph domain.',
        
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


    
    public function mount(){
        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        $this->departments = DB::table('departments')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        $this->faculty_types = DB::table('faculty_types')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        $this->designations = DB::table('designations')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        $this->academic_ranks = DB::table('academic_ranks')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
    }

    public function save(){
        $this->validate();
        
        $res = DB::table('users')
            ->insert([
                'first_name'=> $this->detail['first_name'],
                'middle_name'=> $this->detail['middle_name'],
                'last_name'=> $this->detail['last_name'],
                'suffix'=> $this->detail['suffix'],
                'email'=> $this->detail['email'],
                'password'=> Hash::make($this->detail['password']),
                'admin_type'=> ($this->detail['is_admin'] ? 1 : 2),
            ]);
        
        if($res){
            $user = DB::table('users')
                ->where('email','=',$this->detail['email'])
                ->first();
            if(DB::table('faculty')->insert([
                'user_id' => $user->id,
                'college_id' => $this->detail['college_id'],
                'department_id' => $this->detail['department_id'],
                'code' => $this->detail['code'],
                'academic_rank_id' => $this->detail['academic_rank_id'],
                'designation_id' => $this->detail['designation_id'],
                'faculty_type_id' => $this->detail['faculty_type_id'],
                'release_time' => $this->detail['release_time'],
                'hours_per_week' => $this->detail['hours_per_week'],
            ]
            )){
               $this->dispatch('notifySuccess', 
               'Added successfully!',
                   route($this->route.'-lists'));
           }
        }
    }

    public function render()
    {
        return view('livewire.admin.faculty.add-faculty')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
