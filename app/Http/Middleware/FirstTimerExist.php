<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class FirstTimerExist
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
        $id = $request->route('firstTimerId');
        $firstTimer = $request->church->firstTimers()
        ->where('id', $id)->first();
        

        if(!$firstTimer){
            return response()->json([
                'errorMessage' => 'First timer could not be found'
            ], 404); 
        }
        
        $request->firstTimer = $firstTimer;
        return $next($request);
        
    }
}
