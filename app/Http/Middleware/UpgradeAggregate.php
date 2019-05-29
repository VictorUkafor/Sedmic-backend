<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class UpgradeAggregate
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
        $aggregate = $request->aggregate;
        

        if(($aggregate->units()->count() < 2) || 
        ($aggregate->subs()->count() < 2)){
            return response()->json([
                'errorMessage' => 'You are not eligible for an upgrade',
            ], 401);            
        }

        return $next($request);
    }
}
