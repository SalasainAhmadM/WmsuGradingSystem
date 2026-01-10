<?php

namespace App\Livewire\Admin\Rank;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class viewRank extends Component
{
    public $title = "Rank";

    public $route = 'rank';
    public $detail = [
        'code'=> NULL,
        'name'=> NULL,
    ];

    public function mount($id){
        if($college = DB::table('academic_ranks')
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

        if(DB::table('academic_ranks')
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
        return view('livewire.admin.rank.view-rank')
         ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
