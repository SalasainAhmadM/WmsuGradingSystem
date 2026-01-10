<?php

namespace App\Livewire\Admin\Faculty;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class FacultyLists extends Component
{

    public $title = "Faculty";
    public $route = "faculty";

    public $colleges = [];
    public $departments = [];

    public $filters = [
        'search'=> NULL,
        'college_id' =>NULL,
        'department_id' =>NULL,
    ];

    public $detail = [
        'user_id'=> NULL,
        'new_password' => NULL,
        'confirm_password' => NULL,
    ];

    public function rules(){
        return [
        'detail.new_password' => [
        'required',
            Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised(),
            ],
        'detail.confirm_password' => 'required|same:detail.new_password',
        ];
    }
    protected $messages = [
        'detail.new_password.required' => 'The new password field is required.',
        'detail.new_password.min' => 'The new password must be at least 8 characters.',
        'detail.new_password.mixed_case' => 'The new password must contain both uppercase and lowercase letters.',
        'detail.new_password.letters' => 'The new password must include at least one letter.',
        'detail.new_password.numbers' => 'The new password must include at least one number.',
        'detail.new_password.symbols' => 'The new password must include at least one special character.',
        'detail.new_password.uncompromised' => 'This new password has appeared in a data leak. Please choose a different password.',
        
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
            
            
    }
    public function render()
    {

        $table_data = DB::table('faculty as f')
            ->select(
                'f.id' ,
                'f.college_id' ,
                'f.user_id',
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
                'admin_type'
            )
            ->leftJoin('users as u','u.id','f.user_id')
            ->leftJoin('colleges as c','c.id','f.college_id')
            ->leftJoin('departments as d','d.id','f.department_id')
            ->leftJoin('academic_ranks as ar','ar.id','f.academic_rank_id')
            ->leftJoin('designations as ds','ds.id','f.designation_id')
            ->leftJoin('faculty_types as ft','ft.id','f.faculty_type_id');
        if($this->filters['college_id']){
            $table_data->where('f.college_id', '=',$this->filters['college_id']);
        }

        if($this->filters['department_id']){
            if($this->filters['department_id']){
            $table_data->where('f.department_id', '=',$this->filters['department_id']);
        }
        }


        if (!empty($this->filters['search'])) {
            $table_data
            ->where('f.code','like','%'.$this->filters['search'] .'%')
            ->orwhere('u.email','like','%'.$this->filters['search'] .'%')
            ->orwhere(DB::raw('CONCAT_WS(" ", u.first_name, u.middle_name, u.last_name, u.suffix)'), 'like','%'.$this->filters['search'] .'%');
        }
        $table_data = $table_data
            ->orderBy('u.is_active','desc')
            ->orderBy('f.id', 'desc')
            ->paginate(10)->withPath(url()->current());

        return view('livewire.admin.faculty.faculty-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }

    public function change_password($id,$modal_id){
        $this->detail = [
            'user_id'=> $id,
            'new_password' => NULL,
            'confirm_password' => NULL,
        ];
        $this->dispatch('openModal',modal_id:$modal_id);
    }

    public function save_password($modal_id){
        $this->validate();

        $res = DB::table('users')
            ->where('id','=',$this->detail['user_id'])
            ->update([
                'password' => Hash::make($this->detail['new_password'])
            ]);

        if($res){
            $this->dispatch('notifySuccess', 
            'Updated successfully!',
                '');
        }
         $this->dispatch('closeModal',modal_id:$modal_id);
    }
}
