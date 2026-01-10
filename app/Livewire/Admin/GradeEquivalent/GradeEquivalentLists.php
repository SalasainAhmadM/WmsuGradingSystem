<?php

namespace App\Livewire\Admin\GradeEquivalent;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class GradeEquivalentLists extends Component
{
    use WithPagination;

    public $title = "Grade Equivalent";

    public $route ='grade-equivalent';
     public $filters = [
        'search'=> NULL,

    ];
    public function render()
    {
        $table_data = DB::table('point_grade_equivalent as pge')
            ->orwhere('pge.grade','like','%'.$this->filters['search'] .'%')
            ->orderBy('pge.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.grade-equivalent.grade-equivalent-lists',[
         'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
