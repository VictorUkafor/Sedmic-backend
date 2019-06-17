<?php

namespace App\Http\Middleware;

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
        $sub_unit_type = strtolower(preg_replace('/\s+/', '_', $request->sub_unit_type));

        $units = $request->church->units()->where([
            'type' => $sub_unit_type,
            'aggregate_id' => null
        ])->get();

        $aggregates = $request->church->aggregates()->where([
            'type' => $sub_unit_type,
            'level' => ((int)$request->level - 1),
            'aggregate_id' => null
        ])->get();

        $unitAgg = $request->level == 1 ? $units : $aggregates;


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
