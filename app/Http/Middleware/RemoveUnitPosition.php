<?php

namespace App\Http\Middleware;

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
        $id = $request->route('positionId');

        $findPosition = $request->unit->executives()
        ->where('id', $id)->first();

        $member = $request->unit->members()
        ->where('id', $findPosition->member_id)->first();

        $exco = $findPosition ? $member : null;

        $members = [];
        
        foreach($unit->members as $member){
            array_push($members, $member->id);
        }


        if (!$findPosition || !in_array($exco->id, $members)){
            return response()->json([
                'errorMessage' => 'Position could not be found'
            ], 401);  
        }

        $request->findPosition = $findPosition;
        return $next($request);
          
    }
}
