<?php

namespace App\Livewire\Admin\Department;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class DepartmentLists extends Component
{
    use WithPagination;

    public $title = "Department";

    public $route ='department';
    public $college_ids = [];
    public $college_id = NULL;

    public $department = [
        'id' => NULL,
        'college_id'=> NULL,
        'code' => NULL,
        'name' => NULL,
        'is_active' => NULL
    ];

    public $colleges = [];
    public $filters = [
        'search'=> NULL,
        'college_id'=> NULL,

    ];

    public function mount($id= NULL){
        if($id){
            array_push($this->college_ids,intval($id));
            $this->filters['college_id'] = $id;
        }

        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
    }

    public function updateCollege(){
        // $this->college_ids =[];
        // if($this->college_id){
        //     array_push($this->college_ids,intval($this->college_id));
        // }
        redirect( route('department-lists-college',$this->filters['college_id']));
    }
    public function render()
    {
        $table_data = DB::table('departments as d')
            ->select('d.id','d.code','d.name','c.code as college_code','d.is_active','d.college_id')
            ->leftJoin('colleges as c','c.id','d.college_id');

        if($this->filters['college_id']){
            $table_data->where('d.college_id', '=',$this->filters['college_id']);
        }

        if (!empty($this->filters['search'])) {
            $table_data->where(function ($query) {
                $query->where('d.code', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('d.name', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        $table_data = $table_data
            ->orderBy('d.is_active', 'desc')
            ->orderBy('d.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.department.department-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
