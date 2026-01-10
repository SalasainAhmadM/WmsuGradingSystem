<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class IsValid
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

            $user = DB::table('users')
                ->where('id','=',$userId)
                ->where('is_active','=',1)->first();
            if(!$user){
                return redirect(route('deactivated'));
            }
        }
        
        return $next($request);
    }
}
