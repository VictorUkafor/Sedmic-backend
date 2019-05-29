<?php

namespace App\Http\Middleware;

use App\Aggregate;
use App\Unit;
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
        $subId = (int)$request->route('subId');
        $level = $request->aggregate->level;

        $sub = $level == 1 ? Unit::find($subId) : 
        Aggregate::find($subId);

        
        if(!$sub){
            return response()->json([
                'errorMessage' => 'Sub can not be found'
            ], 404);
        }

        if($sub->aggregate_id != $request->aggregate->id){
            return response()->json([
                    'errorMessage' => 'Sub does not belong to you'
            ], 401);   
        }


        $request->subId = $subId;
        return $next($request);
        
    }
}
