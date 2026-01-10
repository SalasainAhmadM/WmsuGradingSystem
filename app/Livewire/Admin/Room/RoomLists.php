<?php

namespace App\Livewire\Admin\Room;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class RoomLists extends Component
{
    use WithPagination;

    public $title = "Room";
    public $route = 'room';

    public $filters = [
        'search'=> NULL,
    ];

    public function updatedFiltersSearch (){
        $this->resetPage();
    }
    public function render()
    {
        $table_data = DB::table('rooms as r')
            ->orwhere('r.code','like','%'.$this->filters['search'] .'%')
            ->orwhere('r.name','like','%'.$this->filters['search'] .'%')
            ->orderBy('r.is_active','desc')
            ->orderBy('r.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.room.room-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
