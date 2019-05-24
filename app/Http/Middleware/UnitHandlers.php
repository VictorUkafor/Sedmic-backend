<?php

namespace App\Http\Middleware;

use App\Unit;
use Closure;
use Validator;

class UnitHandlers
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
        $id = $request->route('unit_id');
        $user = $request->user;
        $unit = Unit::find($id);
        $handlers = $unit->handlers.' '.$request->handlers;
        
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
