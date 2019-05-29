<?php

namespace App\Http\Middleware;

use App\Unit;
use App\Aggregate;
use Closure;
use Validator;

class NoSubs
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
        $church = $request->church;
        $sub_unit_type = strtolower(preg_replace('/\s+/', '_', $request->sub_unit_type));

        $unitAgg = $request->level == 1 ? 
        Unit::where([
            'church_id' => $church->id,
            'type' => $sub_unit_type,
            'aggregate_id' => NULL
        ])->get() :   
        Aggregate::where([
            'church_id' => $church->id,
            'type' => $sub_unit_type,
            'level' => ((int)$request->level - 1),
            'aggregate_id' => NULL
        ])->get();


        if(count($unitAgg) > 0 && count($unitAgg) < 2){
            return response()->json([
                'errorMessage' => 'You did not have sufficient '.$sub_unit_type.' for this action'
            ], 401);
        }

        if(count($unitAgg) == 0){
            return response()->json([
                'errorMessage' => $sub_unit_type.' can not be found'
            ], 404);
        }
        
        return $next($request);
    }
}
