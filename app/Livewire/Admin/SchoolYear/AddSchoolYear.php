<?php

namespace App\Livewire\Admin\SchoolYear;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Carbon\Carbon;  


class AddSchoolYear extends Component
{
    public $title = "School Year";
    public $route = 'school-year';

    public $detail = [
        'id' => NULL,
        'semester' => NULL,
        'date_start_date' => NULL,
        'date_start_month' => NULL,
        'date_end_date' => NULL,
        'date_end_month' => NULL,
    ];

    protected $messages;
    public $school_year = [
        'id' => NULL,
        'year_start'=> NULL,
        'year_end' => NULL,
        'date_start' => NULL,
        'date_end' => NULL,
        'date_start_date' => NULL,
        'date_start_month' => NULL,
        'date_end_date'=> NULL,
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
    

    public function mount(){
        $latest_school_year = DB::table('school_years')
            ->orderBy('id','desc')
            ->first();
        $first_semester = DB::table('semesters')
            ->where('semester','=','1st semester')
            ->first();
        $second_semester = DB::table('semesters')
            ->where('semester','=','2nd semester')
            ->first();
        if( $latest_school_year){
            $this->school_year = [
                'id' => NULL,
                'year_start'=> $latest_school_year->year_start+1,
                'year_end' => $latest_school_year->year_end+1,
                'date_start' => ($latest_school_year->year_start+1).'-'.$first_semester->date_start_month.'-'.$first_semester->date_start_date,
                'date_end' => ($latest_school_year->year_end+1).'-'.$second_semester->date_end_month.'-'.$second_semester->date_end_date,
                'date_start_date' => $first_semester->date_start_date,
                'date_start_month' => $first_semester->date_start_month,
                'date_end_date'=> $second_semester->date_end_date,
                'date_end_month' => $second_semester->date_end_month,
            ];
        }else{
            $this->school_year = [
                'id' => NULL,
                'year_start'=> Carbon::now()->year,
                'year_end' => Carbon::now()->year+1,
                'date_start' => (Carbon::now()->year).'-'.$first_semester->date_start_month.'-'.$first_semester->date_start_date,
                'date_end' => (Carbon::now()->year+1).'-'.$second_semester->date_end_month.'-'.$second_semester->date_end_date,
                'date_start_date' => $first_semester->date_start_date,
                'date_start_month' => $first_semester->date_start_month,
                'date_end_date'=> $second_semester->date_end_date,
                'date_end_month' => $second_semester->date_end_month,
            ];
        }
        
        $this->detail = [
            'id' => NULL,
            // 'detail' => $detail->semester,
            'date_start_date' => $first_semester->date_start_date,
            'date_start_month' => $first_semester->date_start_month,
            'date_end_date'=> $second_semester->date_end_date,
            'date_end_month' => $second_semester->date_end_month,
        ];

    }

    public function saveAdd(){
        $this->validate(
            [
                'school_year.date_start_month' => 'required|integer|min:1|max:12',
                'school_year.date_start_date' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) {
                        $month = $this->school_year['date_start_month'];
                        $year = $this->school_year['year_start'];
                        if (!checkdate($month, $value, $year)) {
                            $fail('The start date is not a valid calendar date.');
                        }
                    },
                ],
                'school_year.date_end_month' => 'required|integer|min:1|max:12',
                'school_year.date_end_date' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) {
                        $month = $this->school_year['date_end_month'];
                        $year = $this->school_year['year_end'];
                        if (!checkdate($month, $value, $year)) {
                            $fail('The end date is not a valid calendar date.');
                        }
                    },
                ],
                'school_year.year_start' => 'required|integer|min:1900',
                'school_year.year_end' => 'required|integer|min:1900',
            ],
            [
                'school_year.date_start_month.*' => 'Start month must be between 1 and 12.',
                'school_year.date_end_month.*' => 'End month must be between 1 and 12.',
                'school_year.date_start_date.required' => 'The start day is required.',
                'school_year.date_end_date.required' => 'The end day is required.',
            ]
        );

        // Additional logic to ensure start date is before end date
        $start = strtotime("{$this->school_year['year_start']}-{$this->school_year['date_start_month']}-{$this->school_year['date_start_date']}");
        $end   = strtotime("{$this->school_year['year_end']}-{$this->school_year['date_end_month']}-{$this->school_year['date_end_date']}");

        if ($start >= $end) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'school_year.date_start_date' => 'Start date must be earlier than end date.',
            ]);
        }

        if (DB::table('school_years')
            ->where('year_start', '=',$this->school_year['year_start'])
            ->where('year_end', '=', $this->school_year['year_end'])
            ->first()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'school_year.date_start_date' => 'School year added.',
            ]);
        }

        if(DB::table('school_years')
            ->insert([
                'year_start'=>$this->school_year['year_start'],
                'year_end' => $this->school_year['year_end'],
                'date_start' => $this->school_year['date_start'],
                'date_end' => $this->school_year['date_end'],
        ])){
            
        }
        $this->dispatch('notifySuccess', 
            'Added successfully!',
            route($this->route.'-lists'));

    }
    public function render()
    {
        return view('livewire.admin.school-year.add-school-year')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
