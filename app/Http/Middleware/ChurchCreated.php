<?php

namespace App\Http\Middleware;

use App\Church;
use Closure;
use Validator;

class ChurchCreated
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
        $church = Church::where('username', $user->church_username)
        ->first();
        
        if(!$church){
            return response()->json([
                'errorMessage' => 'Church does not exist'
            ], 404);           
        }

        $request->church = $church;
        $request->user = $user;
        
        return $next($request);           
    }
}
