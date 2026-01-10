<?php

namespace App\Livewire\Admin\Department;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class AddDepartment extends Component
{
    public $title = "Department";

    public $route = 'department';

    public $colleges = [];
    protected $rules = [
        'detail.college_id' => 'required|exists:colleges,id',
        'detail.code' => 'required|string|unique:departments,code',
        'detail.name' => 'required|string|unique:departments,name',
    ];

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

    public function save(){
        $this->validate();

        $inserted = DB::table('departments')->insert([
            'college_id' => $this->detail['college_id'],
            'code' => $this->detail['code'],
            'name' => $this->detail['name'],
        ]);

        if ($inserted) {
            // You can dispatch success notification or redirect here
            $this->dispatch('notifySuccess', 
            'Added successfully!',
                route($this->route.'-lists'));
            
        }
    }

    public function render()
    {
        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        return view('livewire.admin.department.add-department')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
