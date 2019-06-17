<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class UniqueAggregateName
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
        $aggregate = $request->church->aggregates()
        ->where('name', $request->name)->first();

        if(!$aggregate){
            return $next($request);
        }

        return response()->json([
            'errorMessage' => 'Name has already been taken'
        ], 400);
        
    }
}
