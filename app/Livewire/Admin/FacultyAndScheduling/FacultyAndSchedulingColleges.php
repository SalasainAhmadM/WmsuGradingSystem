<?php

namespace App\Livewire\Admin\FacultyAndScheduling;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class FacultyAndSchedulingColleges extends Component
{

    use WithPagination;
    public $school_year;

    public $title = "Faculty and Scheduling";
    public $route = 'Faculty and Scheduling';

    public $filters = [
        'search'=> NULL,
    ];
    public function mount($school_year){
        $this->school_year = $school_year;
    }

    public function render()
    {
        $table_data = DB::table('colleges as c')
            ->orwhere('c.code','like','%'.$this->filters['search'] .'%')
            ->orwhere('c.name','like','%'.$this->filters['search'] .'%')
            ->orderBy('c.is_active','desc')
            ->orderBy('c.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.faculty-and-scheduling.faculty-and-scheduling-colleges',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
