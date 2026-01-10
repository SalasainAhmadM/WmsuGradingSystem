<?php

namespace App\Livewire\Admin\FacultyType;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class FacultyTypeLists extends Component
{
    use WithPagination;

    public $title = "FacultyType";
    public $route = 'faculty-type';

    public $filters = [
        'search'=> NULL,
    ];

    public function updatedFiltersSearch (){
        $this->resetPage();
    }
    public function render()
    {
        $table_data = DB::table('faculty_types as r')
            ->orwhere('r.code','like','%'.$this->filters['search'] .'%')
            ->orwhere('r.name','like','%'.$this->filters['search'] .'%')
            ->orderBy('r.is_active','desc')
            ->orderBy('r.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.faculty-type.faculty-type-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
