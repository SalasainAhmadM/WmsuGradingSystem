<?php

namespace App\Livewire\Admin\Schedule;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class ScheduleLists extends Component
{
    public $title = "Schedule";

    public $route = "schedule";

    public $subjects;

    public $rooms;

    public $filters = [
        'search'=> NULL,
        'room_id' => NULL,
    ];
    
    public $days  = [
        ['day'=>'M','day_full'=>'Monday'],
        ['day'=>'T','day_full'=>'Tuesday'],
        ['day'=>'W','day_full'=>'Wednesday'],
        ['day'=>'TH','day_full'=>'Thursday'],
        ['day'=>'F','day_full'=>'Friday'],
        ['day'=>'S','day_full'=>'Saturday'],
        ['day'=>'Sun','day_full'=>'Sunday'],
    ];

    public function mount(){
        $this->subjects = DB::table('subjects')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        $this->rooms = DB::table('rooms')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
    }

    public function render()
    {

        $table_data = DB::table('schedules as s')
            ->select(
                's.id' ,
                's.subject_id',
                's.faculty_id' ,
                's.room_id',
                's.code' ,
                DB::raw("DATE_FORMAT(schedule_from, '%h:%i %p') as schedule_from"),
                DB::raw("DATE_FORMAT(schedule_to, '%h:%i %p') as schedule_to"),
                's.day' ,
                's.is_lec' ,
                DB::raw('CONCAT_WS(" ", u.first_name, u.middle_name, u.last_name, u.suffix) AS fullname'),
                'f.code as faculty_code' ,
                'u.first_name' ,
                'u.middle_name' ,
                'u.last_name' ,
                'u.suffix' ,
                'u.email' ,
                'r.code as room_code',
                'r.name as room_name',
                'sj.subject_id as subject_subject_id' ,
                'sj.subject_code' ,
                'sj.lecture_unit',
                'sj.laboratory_unit',
                's.is_active',
            )
            ->leftjoin('faculty as f','f.id','s.faculty_id')
            ->leftJoin('users as u','u.id','f.user_id')
            ->leftjoin('rooms as r','r.id','s.room_id')
            ->leftjoin('subjects as sj','sj.id','s.subject_id');
        if($this->filters['room_id']){
            $table_data->where('s.room_id', '=',$this->filters['room_id']);
        }

        if (!empty($this->filters['search'])) {
            $table_data->where(function ($query) {
                $query->where('sj.subject_id', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('sj.subject_code', 'like', '%' . $this->filters['search'] . '%');
            });
        }
            $table_data = $table_data
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.schedule.schedule-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
