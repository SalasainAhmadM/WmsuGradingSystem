<?php

namespace App\Livewire\Admin\Curriculum;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class CurriculumColleges extends Component
{
    use WithPagination;

    public $school_year;

    public $title = "Curriculum";
    public $route = 'Curriculum';

    public $filters = [
        'search'=> NULL,
    ];
    public function mount(){
        // $this->school_year = $school_year;
    }
    public function render()
    {
        $table_data = DB::table('colleges as c')
            ->orwhere('c.code','like','%'.$this->filters['search'] .'%')
            ->orwhere('c.name','like','%'.$this->filters['search'] .'%')
            ->orderBy('c.is_active','desc')
            ->orderBy('c.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.curriculum.curriculum-colleges',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
