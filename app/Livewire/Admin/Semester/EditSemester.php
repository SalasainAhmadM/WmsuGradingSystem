<?php

namespace App\Livewire\Admin\Semester;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class EditSemester extends Component
{
   public $title = "Semester";

    public $route = "semester";

    protected function rules(){
        return [
            'detail.semester' => [
                'required',
                'string',
                Rule::unique('semesters', 'semester')->ignore(($this->detail['id'] ?? 'NULL')),
            ],
            'detail.date_start_date' => 'required|integer|min:1|max:31',
            'detail.date_start_month' => 'required|integer|min:1|max:12',
            'detail.date_end_date' => 'required|integer|min:1|max:31',
            'detail.date_end_month' => 'required|integer|min:1|max:12',
        ];
    }

    public $messages = [
        'detail.semester.required' => 'The semester name is required.',
        'detail.semester.unique' => 'This semester already exists.',
        'detail.date_start_date.required' => 'Start day is required.',
        'detail.date_start_date.integer' => 'Start day must be a number.',
        'detail.date_start_date.min' => 'Start day must be at least 1.',
        'detail.date_start_date.max' => 'Start day cannot exceed 31.',
        'detail.date_start_month.required' => 'Start month is required.',
        'detail.date_start_month.integer' => 'Start month must be numeric.',
        'detail.date_start_month.min' => 'Start month must be at least 1.',
        'detail.date_start_month.max' => 'Start month cannot exceed 12.',
        'detail.date_end_date.required' => 'End day is required.',
        'detail.date_end_date.integer' => 'End day must be a number.',
        'detail.date_end_date.min' => 'End day must be at least 1.',
        'detail.date_end_date.max' => 'End day cannot exceed 31.',
        'detail.date_end_month.required' => 'End month is required.',
        'detail.date_end_month.integer' => 'End month must be numeric.',
        'detail.date_end_month.min' => 'End month must be at least 1.',
        'detail.date_end_month.max' => 'End month cannot exceed 12.',
    ];



    public $detail = [
        'semester' => NULL,
        'date_start_date' => 1,
        'date_start_month' => NULL,
        'date_end_date' => 1,
        'date_end_month' => NULL,
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
        ];
    }

    public function save(){

        $this->validate($this->rules());

        // $start = intval(str_pad($this->detail['date_start_month'], 2, '0', STR_PAD_LEFT) . str_pad($this->detail['date_start_date'], 2, '0', STR_PAD_LEFT));
        // $end = intval(str_pad($this->detail['date_end_month'], 2, '0', STR_PAD_LEFT) . str_pad($this->detail['date_end_date'], 2, '0', STR_PAD_LEFT));

        // $overlap = DB::table('semesters')
        //     ->where(function ($query) use ($start, $end) {
        //         $query->whereRaw("LPAD(date_start_month, 2, '0') || LPAD(date_start_date, 2, '0') <= ?", [$end])
        //             ->whereRaw("LPAD(date_end_month, 2, '0') || LPAD(date_end_date, 2, '0') >= ?", [$start]);
        //     })
        //     ->exists();

        // if ($overlap) {
        //     $this->addError('detail.semester', 'This semester\'s date range overlaps with another.');
        //     return;
        // }

        $updated = DB::table('semesters')
            ->where(
                'id','=', $this->detail['id'],
            )
            ->update([
                'semester' => $this->detail['semester'],
                'date_start_date' => $this->detail['date_start_date'],
                'date_start_month' => $this->detail['date_start_month'],
                'date_end_date' => $this->detail['date_end_date'],
                'date_end_month' => $this->detail['date_end_month'],
            ]);

       
        // You can dispatch success notification or redirect here
        $this->dispatch('notifySuccess', 
        'Updated successfully!',
            route($this->route.'-lists'));
    }

    public function render()
    {
        return view('livewire.admin.semester.edit-semester')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
