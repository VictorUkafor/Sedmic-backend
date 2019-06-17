<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class UniqueUnitName
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
        $unit = $request->church->units()
        ->where('name', $request->name)->first();

        if(!$unit){
            return $next($request);
        }

        return response()->json([
            'errorMessage' => 'Name has already been taken'
        ], 400);
        
    }
}
