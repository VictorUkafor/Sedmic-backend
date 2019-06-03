<?php

namespace App\Http\Middleware;

use App\UnitExecutive;
use App\Member;
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
        $unit = $request->unit;
        $memberId = $request->member;
        $position = $request->position;
        $exco = Member::find($memberId);

        $findPosition = UnitExecutive::where([
            'unit_id' => $unit->id,
            'position' => $position
        ])->first();

        $members = [];
        
        foreach($unit->members as $member){
            array_push($members, $member->id);
        }


        if (!$exco || $exco->church_id != $unit->church_id || !in_array($exco->id, $members)){
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
