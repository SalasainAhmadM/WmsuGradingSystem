<?php

namespace App\Livewire\Admin\Room;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class ActivateRoom extends Component
{
    public $title = "Room";

    public $route = 'room';
    public $detail = [
        'code'=> NULL,
        'name'=> NULL,
    ];

    public function mount($id){
        if($college = DB::table('rooms')
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

        if(DB::table('rooms')
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
        return view('livewire.admin.room.activate-room')
         ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
