<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class AttendanceController extends Controller
{
    public function attendance_dates(Request $request){

        $schedule_id =  $request->input('schedule_id');
        $term_id =  $request->input('term_id');

        $current_school_work_type = DB::table('school_works_types')  
            ->where('school_work_type','=','Attendance')  
            ->where('schedule_id','=',$schedule_id)
            ->where('term_id','=',$term_id)
            ->first();
        $attendance_dates = DB::table('school_works')  
            ->select(
                'id',
                DB::raw('DATE_FORMAT(schedule_date, "%Y-%m-%d") as schedule_date')
            )
            ->where('school_work_type_id', '=', $current_school_work_type->id)  
            ->where('schedule_id', '=', $schedule_id)
            ->where('term_id', '=', $term_id)
            ->get(); // No need for ->toArray()

        $mapped = $attendance_dates->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => 'Attedance',
                'start' => $item->schedule_date,
                'display' => 'background',
                'color' => 'green'
            ];
        });
        return response()->json($mapped);
    }

    public function remove_attendace_date(Request $request){
        $id =  $request->input('id');
        $res = DB::table('school_works')  
                ->where('id','=',$id)
                ->delete();
        return  $res ;
    }

     public function add_attendace_date(Request $request){
        $date =  $request->input('date');
        $schedule_id =  $request->input('schedule_id');
        $term_id =  $request->input('term_id');

        $current_school_work_type = DB::table('school_works_types')  
            ->where('school_work_type','=','Attendance')  
            ->where('schedule_id','=',$schedule_id)
            ->where('term_id','=',$term_id)
            ->first();

        $attendance_name = 'Attendance for '.Carbon::parse($date)->format('F, d Y');
        if(!DB::table('school_works')  
            ->where('school_work_name','=',$attendance_name)  
            ->where('schedule_id', '=', $schedule_id)
            ->where('term_id', '=', $term_id)
            ->first()){
            $res = DB::table('school_works')
                ->insert([
                    'id' => NULL,
                    'schedule_id' => $schedule_id,
                    'term_id' => $term_id,
                    'school_work_name' => $attendance_name,
                    'school_work_type_id' => $current_school_work_type->id,
                    'max_score' => 1,
                    'schedule_date' => $date,
                    'number_order' => NULL,
                ]);
            return $res;
        }
        return 0;
    }
}
