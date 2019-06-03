<?php

namespace App\Http\Middleware;

use App\UnitExecutive;
use App\Member;
use Closure;
use Validator;

class UnitPositions
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
        $positions = $unit->executives;


        if(!count($positions)){
            return response()->json([
                'errorMessage' => 'Position can not be found'
            ], 404);           
        }


        $exco = Member::find($findPosition->member_id);

        if ($exco->church_id != $unit->church_id || !in_array($exco->id, $members)){
            return response()->json([
                'errorMessage' => 'This fellow is not your member'
            ], 401);  
        }

        $request->findPosition = $findPosition;
        return $next($request);
          
    }
}
