<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class AddAggregateHandlers
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
        $old_handlers = explode(" ", trim($request->aggregate->handlers));
        $new_handlers = explode(" ", $request->handlers);
        $handlers_number = count($old_handlers) + count($new_handlers);

        if($handlers_number > 3){
            return response()->json([
                'errorMessage' => $handlers_number
            ], 401);  
        }
        
        return $next($request);    
    }
}
