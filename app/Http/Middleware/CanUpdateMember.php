<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class CanUpdateMember
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
        $user = $request->user;
        $member = $request->church->members()
        ->where('id', $id)->first();

        
        if($user->id == $member->created_by || 
        ($user->church_username === $request->church->username && (
        $user->account_type === 'diamond' || 
        $user->account_type === 'gold'   
        ))){
            
            $request->member = $member;
            return $next($request);            
        }
        
        return response()->json([
            'errorMessage' => 'Unauthorized'
            ], 401);        
          
    }
}
