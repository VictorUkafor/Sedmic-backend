<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class CheckDiamond
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
        $user = auth()->user();
        
        if($user->account_type !== 'diamond'){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 404);           
        }
        
        return $next($request);           
    }
}
