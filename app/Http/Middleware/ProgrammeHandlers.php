<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ProgrammeHandlers
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
        $userId = $request->user->id;
        $handlers = $request->programme->handlers()
        ->pluck('user_id')->toArray();

        
        if(!in_array($userId, $handlers)){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401);                       
        }
        
        return $next($request);        
          
    }
}
