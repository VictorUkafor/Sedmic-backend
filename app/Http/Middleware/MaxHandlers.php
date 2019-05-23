<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class MaxHandlers
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
        $handlers = explode(" ", $request->handlers);

        if(count($handlers) > 3){
            return response()->json([
                'errorMessage' => "You can not add more than 3 handlers"
            ], 401);  
        }
        
        return $next($request);    
    }
}
