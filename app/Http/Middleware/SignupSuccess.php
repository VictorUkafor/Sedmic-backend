<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Validator;

class SignupSuccess
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
        
        if($user->activation_token){
            return response()->json([
                'errorMessage' => 'Unauthorized action'
            ], 401);           
        }
        
        return $next($request);           
    }
}
