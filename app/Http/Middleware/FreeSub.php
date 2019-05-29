<?php

namespace App\Http\Middleware;

use App\Aggregate;
use App\Unit;
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
        $subId = (int)$request->route('subId');
        $level = $request->aggregate->level;

        $sub = $level == 1 ? Unit::find($subId) : 
        Aggregate::find($subId);

        
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

        if($sub->aggregate_id == NULL && $level > 1 &&
        (!$sub->units()->count() && !$sub->subs()->count())){
            return response()->json([
                    'errorMessage' => 'Aggregate has no sub'
            ], 401);   
        }

        if($sub->aggregate_id == NULL && $level == 1 && !$sub->members()->count()){
            return response()->json([
                    'errorMessage' => 'Unit has no member'
            ], 401);   
        }

        $request->subId = $subId;
        return $next($request);
        
    }
}
