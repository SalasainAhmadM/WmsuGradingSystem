<?php

namespace App\Livewire\Admin\Department;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class EditDepartment extends Component
{
    public $title = "Department";

    public $route = 'department';

    public $colleges = [];
    protected function rules(){
         return [
            'detail.college_id' => 'required|exists:colleges,id',
            'detail.code' => 'required|string|unique:departments,code,'. ($this->detail['id'] ?? 'NULL'),
            'detail.name' => 'required|string|unique:departments,name,'.  ($this->detail['id'] ?? 'NULL'),
        ];
    }

    protected $messages = [
        'detail.college_id.required' => 'The college field is required.',
        'detail.college_id.exists' => 'The selected college is invalid.',

        'detail.code.required' => 'The code field is required.',
        'detail.code.unique' => 'This code has already been taken.',

        'detail.name.required' => 'The name field is required.',
        'detail.name.unique' => 'This name has already been taken.',
    ];

    public $detail = [
        'id' => NULL,
        'college_id'=> NULL,
        'code' => NULL,
        'name' => NULL,
        'is_active' => NULL
    ];

    public function mount($id){
        $detail = DB::table('departments')
            ->where('id','=',$id)
            ->first();
            $this->detail = [
            'id' => $detail->id,
            'college_id'=> $detail->college_id,
            'code' => $detail->code,
            'name' => $detail->name,
            'is_active' => $detail->is_active
        ];
    }

    public function save(){
        $this->validate();

        if ($this->detail['id']) {
            DB::table('departments')
                ->where('id', $this->detail['id'])
                ->update([
                    'college_id' => $this->detail['college_id'],
                    'code' => $this->detail['code'],
                    'name' => $this->detail['name'],
                    'is_active' => $this->detail['is_active'] ?? 1,
                ]);
        
        } 
        $this->dispatch('notifySuccess', 
        'Updated successfully!',
            route($this->route.'-lists'));
            
    }

    public function render()
    {
        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        return view('livewire.admin.department.edit-department')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
