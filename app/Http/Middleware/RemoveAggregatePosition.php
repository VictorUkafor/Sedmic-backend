<?php

namespace App\Http\Middleware;

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
        $id = $request->route('positionId');

        $findPosition = $request->aggregate->executives()
        ->where('id', $id)->first();

        $member = $request->church->members()
        ->where('id', $findPosition->member_id)->first();

        $exco = $findPosition ? $member : null;

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

        if (!$findPosition || !in_array($exco->id, $members)){
            return response()->json([
                'errorMessage' => 'Position can not be found'
            ], 401);  
        }

        $request->findPosition = $findPosition;
        return $next($request);
          
    }
}
