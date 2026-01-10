<?php

namespace App\Livewire\Admin\Faculty;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class DeleteFaculty extends Component
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
        'user_id'=>NULL,
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
    ];

    public function rules(){
        return [
            'detail.college_id' => 'required|exists:colleges,id',
            'detail.department_id' => 'required|exists:departments,id',
            'detail.academic_rank_id' => 'required|exists:academic_ranks,id',
            'detail.designation_id' => 'required|exists:designations,id',
            'detail.faculty_type_id' => 'required|exists:faculty_types,id',

            'detail.code' => 'required|string|unique:faculty,code,'.$this->detail['id'],
            'detail.first_name' => 'required|string',
            'detail.last_name' => 'required|string',
            'detail.email' => 'required|email|unique:users,email,'.$this->detail['user_id'],
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
    ];


    
    public function mount($id){
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

        $detail = DB::table('faculty as f')->select(
                'f.id' ,
                'f.college_id' ,
                'u.id as user_id',
                'f.department_id' ,
                'f.academic_rank_id',
                'f.designation_id',
                'f.faculty_type_id',
                DB::raw('CONCAT_WS(" ", u.first_name, u.middle_name, u.last_name, u.suffix) AS fullname'),
                'f.code' ,
                'u.first_name' ,
                'u.middle_name' ,
                'u.last_name' ,
                'u.suffix' ,
                'u.email' ,
                'u.is_active' ,
                'c.name as college',
                'c.code as college_code',
                'd.name as department',
                'd.code as department_code',
                'ds.name as designation',
                'ds.code as designation_code',
                'ar.name as academic_rank',
                'ar.code as academic_rank_code',
                'ft.name as faculty_type',
                'ft.code as faculty_type_code',
                'release_time',
                'hours_per_week',
            'u.admin_type',
            )
            ->leftJoin('users as u','u.id','f.user_id')
            ->leftJoin('colleges as c','c.id','f.college_id')
            ->leftJoin('departments as d','d.id','f.department_id')
            ->leftJoin('academic_ranks as ar','ar.id','f.academic_rank_id')
            ->leftJoin('designations as ds','ds.id','f.designation_id')
            ->leftJoin('faculty_types as ft','ft.id','f.faculty_type_id')
            ->where('f.id','=',$id)
            ->first();

        $this->detail = [
            'id' => $detail->id,
            'user_id' => $detail->user_id,
            'college_id' => $detail->college_id,
            'department_id' => $detail->department_id,
            'code' => $detail->code,
            'first_name' => $detail->first_name,
            'middle_name' => $detail->middle_name,
            'last_name' => $detail->last_name,
            'suffix' => $detail->suffix,
            'email' => $detail->email,
            'academic_rank_id' => $detail->academic_rank_id,  
            'designation_id' => $detail->designation_id,
            'faculty_type_id' => $detail->faculty_type_id,
            'is_active' => $detail->is_active,
            'release_time' => $detail->release_time,
            'hours_per_week' => $detail->hours_per_week,
            'is_admin'=> ($detail->admin_type == 1 ? true : false),
        ];
    }

    public function save(){

        if(DB::table('users')
            ->where('id','=',$this->detail['user_id'])
            ->update([
            'is_active'=> !$this->detail['is_active'],
        ])){
        }
        $this->dispatch('notifySuccess', 
        'Updated successfully!',
            route($this->route.'-lists'));
        
    }

    public function render()
    {
        return view('livewire.admin.faculty.delete-faculty')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
