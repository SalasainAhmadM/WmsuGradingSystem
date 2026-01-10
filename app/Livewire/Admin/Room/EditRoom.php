<?php

namespace App\Livewire\Admin\Room;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class EditRoom extends Component
{
    public $title = "Room";

    public $route = 'room';

     public function rules(){
        return [
            'detail.code' => 'required|string|unique:rooms,code,' . $this->detail['id'],
            'detail.name' => 'required|string|unique:rooms,name,' . $this->detail['id'],
        ];
    }
    protected $messages = [
        'detail.code.required' => 'The code field is required.',
        'detail.code.unique' => 'The code already exists.',
        'detail.name.required' => 'The name field is required.',
        'detail.name.unique' => 'The name already exists.',
    ];

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

    public function saveEdit(){
        $this->validate();

        if(DB::table('rooms')
            ->where('id','=',$this->detail['id'])
            ->update([
            'code'=>$this->detail['code'],
            'name'=>$this->detail['name'],
        ])){
        }
        $this->dispatch('notifySuccess', 
        'Updated successfully!',
            route($this->route.'-lists'));
        
    }

    public function render()
    {
        return view('livewire.admin.room.edit-room')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
