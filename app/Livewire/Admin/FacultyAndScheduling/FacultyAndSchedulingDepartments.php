<?php

namespace App\Livewire\Admin\FacultyAndScheduling;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class FacultyAndSchedulingDepartments extends Component
{
    use WithPagination;
    public $school_year;
    public $college;

    public $title = "Faculty and Scheduling";
    public $route = 'Faculty and Scheduling';

    public $filters = [
        'search'=> NULL,
    ];
    public function mount($school_year,$college){
        $this->school_year = $school_year;
        $this->college = $college;
    }
    public function render()
    {
        $table_data = DB::table('departments as d')
            ->select('d.id','d.code','d.name','c.code as college_code','d.is_active','d.college_id')
            ->leftJoin('colleges as c','c.id','d.college_id');

        if($this->college){
            $table_data->where('c.code', '=',$this->college);
        }

        if (!empty($this->filters['search'])) {
            $table_data->where(function ($query) {
                $query->where('d.code', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('d.name', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('c.code', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        $table_data = $table_data
            ->orderBy('d.is_active', 'desc')
            ->orderBy('d.id', 'desc')
            ->paginate(10)->withPath(url()->current());

        return view('livewire.admin.faculty-and-scheduling.faculty-and-scheduling-departments',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
