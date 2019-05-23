<?php

namespace App\Http\Middleware;


use App\Unit;
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
        $church = $request->church;
        $unit = Unit::find($id);
        

        if(!$unit){
            return response()->json([
                'errorMessage' => 'Unit can not be found'
            ], 404); 
        }

        if($unit->church_id !== $church->id){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401); 
        }
        
        $request->unit = $unit;
        return $next($request);
        
    }
}
