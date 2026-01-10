<?php

namespace App\Livewire\Admin\SideNav;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SideNav extends Component
{
    public function render()
    {

        $userId = Session::get('user_id');


        $user = DB::table('users')
            ->where('id','=',$userId)
            ->first();

        $schedules = DB::table('users as u')
            ->join('faculty as f','f.user_id','u.id')
            ->join('schedulings as cl','cl.faculty_id','f.id')
            ->where('f.user_id','=',$userId)
            ->get()
            ->toArray();

        $admin = DB::table('users')
            ->where('id','=',$userId)
            ->where('is_active','=',1)
            ->where('admin_type','=',1)->first();

        $student = DB::table('users')
                ->where('id','=',$userId)
                ->where('is_active','=',1)
                ->where('admin_type','=',3)->first();

        return view('livewire.admin.side-nav.side-nav',[
            'user' => $user,
            'schedules' => $schedules,
            'admin' => $admin,
            'student'=> $student
        ]);
    }
}
