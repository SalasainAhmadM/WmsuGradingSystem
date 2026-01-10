<?php

namespace App\Livewire\Admin\Schedule;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Carbon\Carbon;



class ActivateSchedule extends Component
{
    public $title = "Schedule";

    public $route = "schedule";

    public $subjects;
    public $rooms;
    public $faculty;

    public $detail = [
        'id' => NULL,
        'subject_id' => NULL,
        'faculty_id' => NULL,
        'room_id' => NULL,
        'code' => NULL,
        'schedule_from' => NULL,
        'schedule_to' => NULL,
        'day' => [],
        'is_lec' => "1",
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

    public function rules(){
        return [
            'detail.subject_id'      => 'required|exists:subjects,id',
            'detail.room_id'         => 'required|exists:rooms,id',
            'detail.faculty_id'      => 'nullable|exists:faculties,id',
            'detail.code'            => 'nullable|string',
            'detail.schedule_from'   => 'required|date_format:H:i',
            'detail.schedule_to'     => 'required|date_format:H:i|after:detail.schedule_from',
            'detail.day'             => 'required|array|min:1',
            'detail.is_lec'          => 'nullable|boolean',
        ];
    }


    public function messages(){
        return [
            'detail.subject_id.required'    => 'The subject is required.',
            'detail.subject_id.exists'      => 'The selected subject does not exist.',
            'detail.room_id.required'       => 'The room is required.',
            'detail.room_id.exists'         => 'The selected room does not exist.',
            'detail.schedule_from.required' => 'The start time is required.',
            'detail.schedule_from.date_format' => 'The start time must be in the format HH:MM.',
            'detail.schedule_to.required'   => 'The end time is required.',
            'detail.schedule_to.date_format' => 'The end time must be in the format HH:MM.',
            'detail.schedule_to.after'      => 'The end time must be after the start time.',
            'detail.day.required'           => 'At least one day must be selected.',
            'detail.day.array'              => 'The day must be a valid array.',
            'detail.day.min'                => 'Please select at least one day.',
        ];
    }


    public function mount($id){
        $this->subjects = DB::table('subjects')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        $this->rooms = DB::table('rooms')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

        $this->rooms = DB::table('rooms')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        $detail = DB::table('schedules')
        ->where('id','=',$id)
        ->first();

        $this->detail = [
            'id' => $detail->id,
            'subject_id' => $detail->subject_id,
            'faculty_id' => $detail->faculty_id,
            'room_id' => $detail->room_id,
            'code' => $detail->code,
            'schedule_from' =>  (Carbon::createFromFormat('Y-m-d H:i:s', $detail->schedule_from))->format('H:i'),
            'schedule_to' => (Carbon::createFromFormat('Y-m-d H:i:s', $detail->schedule_to))->format('H:i'),
            'day' =>  json_decode($detail->day),
            'is_lec' => "1",
        ];
    }


    public function save(){

        if(DB::table('schedules')
            ->where('id','=',$this->detail['id'])
            ->update([
                'is_active'=>1
            ])){
        }
        $this->dispatch('notifySuccess', 
        'Activated successfully!',
            route($this->route.'-lists'));
    }

    public function render()
    {
        return view('livewire.admin.schedule.activate-schedule')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
