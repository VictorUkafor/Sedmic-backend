<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class CheckChurch
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
        $user = auth()->user();
        $church = Church::where('username', )
        
        if($user->church !== 'diamond'){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 404);           
        }
        
        return $next($request);           
    }
}
