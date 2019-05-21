<?php

namespace App\Http\Middleware;


use App\Member;
use App\Church;
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
        $memberId = $request->route('member_id');
        $user = auth()->user();
        $church = Church::where('username', $user->church_username)
        ->first();

        $member = Member::where([
            'id' => $memberId,
            'church_id' => $church->id
            ])->first();

        
        if(!$member){
            return response()->json([
                'errorMessage' => 'Member can not found'
            ], 404);           
        }
        
        $request->member = $member;
        return $next($request);           
    }
}
