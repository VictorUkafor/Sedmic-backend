<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class DiamondOrGoldUser
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
        $user = $request->user;
        
        if($user->account_type === 'diamond' ||
        $user->account_type === 'gold'){
            return $next($request);            
        }
        
        return response()->json([
            'errorMessage' => 'Unauthorized'
            ], 401);        
          
    }
}
