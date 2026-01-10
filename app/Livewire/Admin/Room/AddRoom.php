<?php

namespace App\Livewire\Admin\Room;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class AddRoom extends Component
{
    public $title = "Room";

    public $route = 'room';

    public $detail = [
        'code'=> NULL,
        'name'=> NULL,
    ];

    protected $rules = [
        'detail.code' => 'required|string|unique:rooms,code',
        'detail.name' => 'required|string|unique:rooms,name',
    ];

    protected $messages = [
        'detail.code.required' => 'The code field is required.',
        'detail.code.unique' => 'The code already exists.',
        'detail.name.required' => 'The name field is required.',
        'detail.name.unique' => 'The name already exists.',
    ];


    public function saveAdd(){
        $this->validate();

        if(DB::table('rooms')->insert([
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
        return view('livewire.admin.room.add-room')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
