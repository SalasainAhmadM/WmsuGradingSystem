<?php

namespace App\Livewire\Admin\Department;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class viewDepartment extends Component
{
    public $title = "Department";

    public $route = 'department';

    public $colleges = [];

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
    public function save(): void{
        if(DB::table('departments')
                ->where('id','=',$this->detail['id'])
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
        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        return view('livewire.admin.department.view-department')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
