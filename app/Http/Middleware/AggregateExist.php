<?php

namespace App\Http\Middleware;

use App\Aggregate;
use Closure;
use Validator;

class AggregateExist
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
        $id = $request->route('aggregate_id');
        $church = $request->church;
        $aggregate = Aggregate::find($id);
        

        if(!$aggregate){
            return response()->json([
                'errorMessage' => 'Aggregate can not be found'
            ], 404); 
        }

        if($aggregate->church_id !== $church->id){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401); 
        }
        
        $request->aggregate = $aggregate;
        return $next($request);
        
    }
}
