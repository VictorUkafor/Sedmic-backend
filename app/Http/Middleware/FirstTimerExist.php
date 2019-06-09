<?php

namespace App\Http\Middleware;

use App\FirstTimer;
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
        $church = $request->church;
        $firstTimer = FirstTimer::find($id);
        

        if(!$firstTimer){
            return response()->json([
                'errorMessage' => 'First timer can not be found'
            ], 404); 
        }

        if($firstTimer->church_id !== $church->id){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401); 
        }
        
        $request->firstTimer = $firstTimer;
        return $next($request);
        
    }
}
