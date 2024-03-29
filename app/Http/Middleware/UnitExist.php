<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class UnitExist
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
        $id = $request->route('unit_id');
        
        $unit = $request->church->units()
        ->where('id', $id)->first();
        

        if(!$unit){
            return response()->json([
                'errorMessage' => 'Unit can not be found'
            ], 404); 
        }

        
        $request->unit = $unit;
        return $next($request);
        
    }
}
