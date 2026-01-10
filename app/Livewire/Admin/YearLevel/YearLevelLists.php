<?php

namespace App\Livewire\Admin\YearLevel;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class YearLevelLists extends Component
{
    public $title = "Year Level";

    public $route = 'year-level';

    public $filters = [
        'search'=> NULL,
    ];

    public function render()
    {
        $table_data = DB::table('year_levels as yl')
            ->where('yl.is_active','=',1)
            ->orwhere('yl.year_level','like','%'.$this->filters['search'] .'%')
            ->orderBy('yl.is_active','desc')
            ->orderBy('yl.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.year-level.year-level-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
