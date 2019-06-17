<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class UnitPositionExist
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
        $memberId = $request->member;
        $position = $request->position;

        $exco = $request->unit->members()
        ->where('id', $memberId)->first();

        $findPosition = $request->unit->executives()
        ->where('position', $position)->first();

        $members = [];
        
        foreach($unit->members as $member){
            array_push($members, $member->id);
        }


        if (!$exco || !in_array($exco->id, $members)){
            return response()->json([
                'errorMessage' => 'Member can not be found'
            ], 404);  
        }

        if (in_array($exco->id, $members) && $findPosition){
            return response()->json([
                'errorMessage' => 'Position has been taken'
            ], 401);  
        }


        $request->excoId = $exco->id;
        return $next($request);
          
    }
}
