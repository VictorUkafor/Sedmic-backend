<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class UnitMembers
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
        $unitMembers = $request->unit->members;

        if(!count($unitMembers)){
            return response()->json([
                'errorMessage' => 'Unit has no members'
            ], 404); 
        }

        return $next($request);           
    }
}
