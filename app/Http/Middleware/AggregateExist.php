<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class AggregateExist
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
        $id = $request->route('aggregate_id');
        $aggregate = $request->church->aggregates()
        ->where('id', $id)->first();
        

        if(!$aggregate){
            return response()->json([
                'errorMessage' => 'Aggregate can not be found'
            ], 404); 
        }

        
        $request->aggregate = $aggregate;
        return $next($request);
        
    }
}
