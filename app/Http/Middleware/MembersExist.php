<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class MembersExist
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

        $members = $request->church->members;
        
        if(!count($members)){
            return response()->json([
                'errorMessage' => 'Members could not be found'
            ], 404);           
        }
        
        $request->members = $members;
        return $next($request);           
    }
}
