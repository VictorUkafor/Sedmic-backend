<?php

namespace App\Http\Middleware;


use App\Member;
use App\Church;
use Closure;
use Validator;

class CanDeleteMember
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
        $memberId = $request->route('member_id');
        $user = auth()->user();
        $member = Member::find($memberId);
        $church = Church::find($member->church_id);

        
        if(($user->id == $member->created_by && 
        ($user->account_type === 'gold' || $user->account_type === 'silver')) || 
        ($user->church_username === $church->username && 
        ($user->account_type === 'diamond' || $user->account_type === 'gold'))){
            
            $request->member = $member;
            return $next($request);            
        }
        
        return response()->json([
            'errorMessage' => 'Unauthorized'
            ], 401);        
          
    }
}
