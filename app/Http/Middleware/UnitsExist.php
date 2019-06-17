<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class UnitsExist
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
        $units = $request->church->units;

        if(count($units)){    
            $request->units = $units;
            return $next($request);
        }
        
        return response()->json([
            'errorMessage' => 'Units can not be found'
        ], 404);           
    }
}
