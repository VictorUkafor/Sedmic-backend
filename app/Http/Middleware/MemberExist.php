<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class MemberExist
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
        $id = $request->route('member_id');
        $member = $request->church->members()
        ->where('id', $id)->first();

        
        if(!$member){
            return response()->json([
                'errorMessage' => 'Member can not found'
            ], 404);           
        }
        
        $request->member = $member;
        return $next($request);           
    }
}
