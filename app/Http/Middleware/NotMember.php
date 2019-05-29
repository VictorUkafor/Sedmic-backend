<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class NotMember
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
        $unit = $request->unit;
        $member = $request->member;

        if($unit->members()->whereId($member->id)->exists()){
            return $next($request);  
        }

        return response()->json([
            'errorMessage' => 
            $member->first_name.' '.$member->last_name.' is not a member of '.$unit->name,
        ], 401); 
          
    }
}
