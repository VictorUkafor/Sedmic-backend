<?php

namespace App\Http\Middleware;

use App\Aggregate;
use Closure;
use Validator;

class UniqueAggregateName
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
        $church = $request->church;
        $aggregate = Aggregate::where([
            'church_id' => $church->id,
            'name' => $request->name
        ])->first();

        if(!$aggregate){
            return $next($request);
        }

        return response()->json([
            'errorMessage' => 'Name has already been taken'
        ], 400);
        
    }
}
