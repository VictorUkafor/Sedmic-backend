<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class FreeSub
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

        $unit = $request->church->units()->where('id', $id)->first();
        $aggregate = $request->church->aggregates()->where('id', $id)->first();

        $sub = $level == 1 ? $unit : $aggregate;
        
        if(!$sub){
            return response()->json([
                'errorMessage' => 'Sub can not be found'
            ], 404);
        }

        if($sub->aggregate_id){
            return response()->json([
                    'errorMessage' => 'Sub has already been aggregated'
            ], 401);   
        }

        if($sub->aggregate_id == null && $level > 1 &&
        (!$sub->units()->count() && !$sub->subs()->count())){
            return response()->json([
                    'errorMessage' => 'Aggregate has no sub'
            ], 401);   
        }

        if($sub->aggregate_id == null && $level == 1 && !$sub->members()->count()){
            return response()->json([
                    'errorMessage' => 'Unit has no member'
            ], 401);   
        }

        $request->subId = $subId;
        return $next($request);
        
    }
}
