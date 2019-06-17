<?php

namespace App\Http\Middleware;

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
        $position = $request->position;
        $exco = $request->church->members()
        ->where('id', $request->member)->first();

        $findPosition = $request->aggregate->executives()
        ->where('position', $position)->first();

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
