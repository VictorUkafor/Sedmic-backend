<?php

namespace App\Http\Middleware;

use App\AggregateExecutive;
use App\Member;
use Closure;
use Validator;

class RemoveAggregatePosition
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
        $positionId = $request->route('positionId');
        $findPosition = AggregateExecutive::find($positionId);
        $exco = $findPosition ? Member::find($findPosition->member_id) : null;

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

        if (!$findPosition || $exco->church_id != $unit->church_id || !in_array($exco->id, $members)){
            return response()->json([
                'errorMessage' => 'Position can not be found'
            ], 401);  
        }

        $request->findPosition = $findPosition;
        return $next($request);
          
    }
}
