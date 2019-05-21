<?php

namespace App\Http\Middleware;


use App\Member;
use App\Church;
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
        $user = auth()->user();
        $church = Church::where('username', $user->church_username)
        ->first();

        $members = Member::where('church_id', $church->id)
        ->first();

        
        if(!$members){
            return response()->json([
                'errorMessage' => 'Members can not found'
            ], 404);           
        }
        
        return $next($request);           
    }
}
