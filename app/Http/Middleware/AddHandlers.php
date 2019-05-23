<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class AddHandlers
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
        $old_handlers = explode(" ", $request->unit->handlers);
        $new_handlers = explode(" ", $request->handlers);
        $handlers = $old_handlers + $new_handlers;

        if(count($handlers) > 3){
            return response()->json([
                'errorMessage' => "You can not have more than 3 handlers"
            ], 401);  
        }
        
        return $next($request);    
    }
}
