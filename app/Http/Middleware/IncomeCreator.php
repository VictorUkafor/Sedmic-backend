<?php

namespace App\Http\Middleware;

use Closure;

class IncomeCreator
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
        if($request->user->id != $request->income->created_by){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401);                       
        }
        
        return $next($request);        
          
    }
}
