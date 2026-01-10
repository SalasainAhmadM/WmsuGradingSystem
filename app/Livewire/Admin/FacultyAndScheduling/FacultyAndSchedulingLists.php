<?php

namespace App\Livewire\Admin\FacultyAndScheduling;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class FacultyAndSchedulingLists extends Component
{
    use WithPagination;
    public $title = "Faculty and Scheduling";

    public $route = "Faculty and Scheduling";

    public function render()
    {
        $table_data = DB::table('school_years as sy')
            ->orderBy('sy.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        
        return view('livewire.admin.faculty-and-scheduling.faculty-and-scheduling-lists',[
            'table_data' =>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
