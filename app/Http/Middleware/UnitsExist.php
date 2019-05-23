<?php

namespace App\Http\Middleware;


use App\Unit;
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
        $church = $request->church;

        $units = Unit::where('church_id', $church->id)
        ->get();

        
        if(count($units)){    
            $request->units = $units;
            return $next($request);
        }
        
        return response()->json([
            'errorMessage' => 'Units can not be found'
        ], 404);           
    }
}
