<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class AggregateMembers
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


        if(!count($members)){
            return response()->json([
                'errorMessage' => 'Members can not be found'
            ], 404); 
        }


        return $next($request);           
    }
}
