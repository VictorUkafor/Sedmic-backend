<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class AggregateHandlers
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
        $user = $request->user;

        $aggregate = $request->church->aggregates()
        ->where('id', $id)->first();
        
        $handlers = $aggregate->handlers.' '.$request->handlers;
        
        if($user->account_type === 'diamond' ||
        $user->account_type === 'gold' ||
        strpos($handlers, $user->username) !== false){
            return $next($request);            
        }
        
        return response()->json([
            'errorMessage' => 'Unauthorized'
            ], 401);        
          
    }
}
