<?php

namespace App\Livewire\Admin\College;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class DeleteCollege extends Component
{
    public $title = "College";
    public $route = 'college';

    public $detail = [
        'code'=> NULL,
        'name'=> NULL,
    ];

    public function mount($id){
        if($college = DB::table('colleges')
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

        if(DB::table('colleges')
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
        return view('livewire.admin.college.delete-college')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
