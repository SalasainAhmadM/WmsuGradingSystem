<?php

namespace App\Livewire\Admin\Designation;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class ActivateDesignation extends Component
{
    public $title = "Designation";

    public $route = 'designation';
    public $detail = [
        'code'=> NULL,
        'name'=> NULL,
    ];

    public function mount($id){
        if($college = DB::table('designations')
            ->where('id','=',$id)
            ->first()){

        }
        $this->detail = [
            'id' => $college->id,
            'code' => $college->code,
            'name' => $college->name,
            'is_active' => $college->is_active
        ];
    }

    public function save(){

        if(DB::table('designations')
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
        return view('livewire.admin.designation.activate-designation')
         ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
