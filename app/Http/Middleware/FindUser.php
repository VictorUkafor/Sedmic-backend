<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Validator;

class FindUser
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
        $user_id = $request->route('userId');
        $user = User::find($user_id);
        
        if(!$user){
            return response()->json([
                'errorMessage' => 'User does not exist'
            ], 404);           
        }
        
        return $next($request);           
    }
}
