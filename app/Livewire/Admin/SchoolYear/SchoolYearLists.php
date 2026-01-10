<?php

namespace App\Livewire\Admin\SchoolYear;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class SchoolYearLists extends Component
{

    public $title = "School Year";

    public $route = 'school-year';
    public function render()
    {
        $table_data = DB::table('school_years')
            ->paginate(10)->withPath(url()->current());
        
        return view('livewire.admin.school-year.school-year-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
