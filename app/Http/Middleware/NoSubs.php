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

        if($request->level == 1){
            
            $units = Unit::where([
                'church_id' => $church->id,
                'type' => $sub_unit_type,
                'aggregate_id' => NULL
            ])->get();

            if(count($units) > 0 && count($units) < 2){
                return response()->json([
                    'errorMessage' => $sub_unit_type.' not enough to create an aggregate'
                ], 401);
            }

            if(count($units) == 0){
                return response()->json([
                    'errorMessage' => $sub_unit_type.' can not be found'
                ], 404);
            }
            
        }


        if($request->level > 1){
            
            $aggregates = Aggregate::where([
                'church_id' => $church->id,
                'sub_unit_type' => $sub_unit_type,
                'level' => ($request->level - 1),
                'aggregate_id' => NULL
            ])->get();

            if(count($aggregates) > 0 && count($aggregates) < 2){
                return response()->json([
                    'errorMessage' => $sub_unit_type.' not enough to create an Aggregate'
                ], 401);
            }

            if(count($aggregates) == 0){
                return response()->json([
                    'errorMessage' => $sub_unit_type.' can not be found'
                ], 404);
            }
        
        }
        
        
        return $next($request);
    }
}
