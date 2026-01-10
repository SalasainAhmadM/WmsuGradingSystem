<?php

namespace App\Livewire\Admin\FacultyType;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class AddFacultyType extends Component
{
    public $title = "FacultyType";

    public $route = 'faculty-type';

    public $detail = [
        'code'=> NULL,
        'name'=> NULL,
    ];

    protected $rules = [
        'detail.code' => 'required|string|unique:faculty_types,code',
        'detail.name' => 'required|string|unique:faculty_types,name',
    ];

    protected $messages = [
        'detail.code.required' => 'The code field is required.',
        'detail.code.unique' => 'The code already exists.',
        'detail.name.required' => 'The name field is required.',
        'detail.name.unique' => 'The name already exists.',
    ];


    public function saveAdd(){
        $this->validate();

        if(DB::table('faculty_types')->insert([
            'code'=>$this->detail['code'],
            'name'=>$this->detail['name'],
        ])){
            $this->dispatch('notifySuccess', 
            'Added successfully!',
                route($this->route.'-lists'));
        }
    }

    public function render()
    {
        return view('livewire.admin.faculty-type.add-faculty-type')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
