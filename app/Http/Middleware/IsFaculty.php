<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class IsFaculty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = Session::get('user_id');

        if(isset($userId)){

            $admin = DB::table('users')
                ->where('id','=',$userId)
                ->where('is_active','=',1)
                ->where('admin_type','=',1)->first();

            $faculty = DB::table('users')
                ->where('id','=',$userId)
                ->where('is_active','=',1)
                ->where('admin_type','=',2)->first();

            $scheduling = DB::table('users as u')
                ->join('faculty as f','f.user_id','u.id')
                ->join('schedulings as cl','cl.faculty_id','f.id')
                ->where('f.user_id','=',$userId)
                ->get()
                ->toArray();
            $student = DB::table('users')
                ->where('id','=',$userId)
                ->where('is_active','=',1)
                ->where('admin_type','=',3)->first();
            if($admin){
                // return redirect('/admin');
            }
            if($scheduling == 0){
                return redirect('/student');
            }
        }
        return $next($request);
    }
}
