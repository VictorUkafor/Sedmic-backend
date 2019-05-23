<?php

namespace App\Http\Middleware;

use App\Unit;
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
        $church = $request->church;
        $unit = Unit::where([
            'church_id' => $church->id,
            'name' => $request->name
        ])->first();

        if(!$unit){
            return $next($request);
        }

        return response()->json([
            'errorMessage' => 'Name has already been taken'
        ], 400);
        
    }
}
