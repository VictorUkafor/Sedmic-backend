<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class SubNotYours
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
        $subId = $request->route('subId');
        $level = $request->aggregate->level;

        $unit = $request->church->units()
        ->where('id', $subId)->first();

        $aggregate = $request->church->aggregates()
        ->where('id', $subId)->first();

        $sub = $level == 1 ? $unit : $aggregate;

        
        if(!$sub){
            return response()->json([
                'errorMessage' => 'Sub can not be found'
            ], 404);
        }

        if($sub->aggregate_id != $request->aggregate->id){
            return response()->json([
                    'errorMessage' => 'Unauthorized'
            ], 401);   
        }


        $request->subId = $subId;
        return $next($request);
        
    }
}
