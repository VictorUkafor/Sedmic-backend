<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class AddUnitHandlers
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
        $old_handlers = explode(" ", trim($request->unit->handlers));
        $new_handlers = explode(" ", $request->handlers);
        $handlers_number = count($old_handlers) + count($new_handlers);

        if($handlers_number > 3){
            return response()->json([
                'errorMessage' => "You can not have more than 3 handlers"
            ], 401);  
        }
        
        return $next($request);    
    }
}
