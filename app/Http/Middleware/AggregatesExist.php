<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class AggregatesExist
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

        $aggregates = $request->church->aggregates;
        
        if(count($aggregates)){    
            $request->aggregates = $aggregates;
            return $next($request);
        }
        
        return response()->json([
            'errorMessage' => 'Aggregates can not be found'
        ], 404);           
    }
}
