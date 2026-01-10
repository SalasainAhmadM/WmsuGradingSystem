<?php

namespace App\Livewire\Admin\Rank;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class RankLists extends Component
{
    use WithPagination;

    public $title = "Rank";
    public $route = 'rank';

    public $filters = [
        'search'=> NULL,
    ];

    public function updatedFiltersSearch (){
        $this->resetPage();
    }
    public function render()
    {
        $table_data = DB::table('academic_ranks as r')
            ->orwhere('r.code','like','%'.$this->filters['search'] .'%')
            ->orwhere('r.name','like','%'.$this->filters['search'] .'%')
            ->orderBy('r.is_active','desc')
            ->orderBy('r.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.rank.rank-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
