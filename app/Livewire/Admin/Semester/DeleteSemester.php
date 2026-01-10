<?php

namespace App\Livewire\Admin\Semester;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class DeleteSemester extends Component
{
    public $title = "Semester";

    public $route = "semester";

    public $detail = [
        'semester' => NULL,
        'date_start_date' => 1,
        'date_start_month' => NULL,
        'date_end_date' => 1,
        'date_end_month' => NULL,
        'is_active'=> NULL,
    ];

    public $months = [
        0=>['month_name'=> 'January','month_number'=>1,'max_date'=>31],
        1=>['month_name'=> 'February','month_number'=>2,'max_date'=>28],
        2=>['month_name'=> 'March','month_number'=>3,'max_date'=>31],
        3=>['month_name'=> 'April','month_number'=>4,'max_date'=>30],
        4=>['month_name'=> 'May','month_number'=>5,'max_date'=>31],
        5=>['month_name'=> 'June','month_number'=>6,'max_date'=>30],
        6=>['month_name'=> 'July','month_number'=>7,'max_date'=>31],
        7=>['month_name'=> 'August','month_number'=>8,'max_date'=>31],
        8=>['month_name'=> 'September','month_number'=>9,'max_date'=>30],
        9=>['month_name'=> 'October','month_number'=>10,'max_date'=>31],
        10=>['month_name'=> 'Novermber','month_number'=>11,'max_date'=>30],
        11=>['month_name'=> 'December','month_number'=>12,'max_date'=>31],
    ];

    public function mount($id){

        $detail = DB::table('semesters')
            ->where(
                    'id','=', $id,
                )
            ->first();
        $this->detail = [
            'id'=>$detail->id,
            'semester' => $detail->semester,
            'date_start_date' => $detail->date_start_date,
            'date_start_month' => $detail->date_start_month,
            'date_end_date' => $detail->date_end_date,
            'date_end_month' => $detail->date_end_month,
            'is_active'=> $detail->is_active
        ];
    }

    public function save(){
        $updated = DB::table('semesters')
            ->where(
                'id','=', $this->detail['id'],
            )
            ->update([
                'is_active' => !$this->detail['is_active'],
            ]);

       
        $this->dispatch('notifySuccess', 
        'Updated successfully!',
            route($this->route.'-lists'));
    }
    public function render()
    {
        return view('livewire.admin.semester.delete-semester')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
