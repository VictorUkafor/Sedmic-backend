<?php

namespace App\Http\Middleware;

use App\AggregateExecutive;
use App\Member;
use Closure;
use Validator;

class AggregatePositionExist
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
        $aggregate = $request->aggregate;
        $memberId = $request->member;
        $position = $request->position;
        $exco = Member::find($memberId);

        $findPosition = AggregateExecutive::where([
            'aggregate_id' => $aggregate->id,
            'position' => $position
        ])->first();

        $members = [];

        if($aggregate->level == 1){
            foreach($aggregate->units as $unit){
                foreach($unit->members as $member){
                    array_push($members, $member->id);
                }
            }
        }
        
        if($aggregate->level > 1){
            for($x=1; $x <= count($aggregate->subs); $x++ ){
                foreach($aggregate->subs as $subs){
                    foreach($subs->units as $unit){
                        foreach($unit->members as $member){
                            array_push($members, $member->id);
                        }
                    }
                }
            }
        }

        if (!$exco || $exco->church_id != $aggregate->church_id || !in_array($exco->id, $members)){
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
