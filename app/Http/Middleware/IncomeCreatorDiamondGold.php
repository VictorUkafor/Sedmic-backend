<?php

namespace App\Http\Middleware;

use Closure;

class IncomeCreatorDiamondGold
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

        if($user->id == $request->income->created_by || 
        ($user->church_id == $request->church->id && 
        ($user->account_type == 'diamond' || $user->account_type == 'gold'))){
            return $next($request);               
        }
        
        return response()->json([
            'errorMessage' => 'Unauthorized'
        ], 401);        
          
    }
}
