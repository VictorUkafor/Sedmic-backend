<?php

namespace App\Http\Middleware;

use App\UnitExecutive;
use App\Member;
use Closure;
use Validator;

class RemoveUnitPosition
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
        $positionId = $request->route('positionId');
        $findPosition = UnitExecutive::find($positionId);
        $exco = $findPosition ? Member::find($findPosition->member_id) : null;

        $members = [];
        
        foreach($unit->members as $member){
            array_push($members, $member->id);
        }

        if (!$findPosition || $exco->church_id != $unit->church_id || !in_array($exco->id, $members)){
            return response()->json([
                'errorMessage' => 'Position can not be found'
            ], 401);  
        }

        $request->findPosition = $findPosition;
        return $next($request);
          
    }
}
