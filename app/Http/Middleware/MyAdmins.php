<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Validator;

class MyAdmins
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_id = (int)$request->route('userId');
        $super_user = auth()->user();
        $user = User::find($user_id);
        
        if($super_user->id === $user_id || 
        $super_user->church_username !== $user->church_username){
            return response()->json([
                'errorMessage' => 'Unauthorized action'
            ], 401);           
        } 
        
        return $next($request);           
    }
}
