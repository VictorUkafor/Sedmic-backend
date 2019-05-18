<?php

namespace App\Http\Middleware;

use App\Church;
use Closure;
use Validator;

class ChurchNotCreated
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
        $church = Church::where('username', $user->username)
        ->first();
        
        if($church){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401);           
        }
        
        return $next($request);           
    }
}
