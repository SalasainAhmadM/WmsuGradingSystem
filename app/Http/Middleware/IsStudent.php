<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class IsStudent
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
            $student = DB::table('users')
                ->where('id','=',$userId)
                ->where('is_active','=',1)
                ->where('admin_type','=',3)->first();
            if($faculty){
                return redirect('/faculty');
            }
            if($admin){
                return redirect('/admin');
            }
        }
        return $next($request);
    }
}
