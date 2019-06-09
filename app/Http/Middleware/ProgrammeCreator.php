<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ProgrammeCreator
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
        $programme = $request->programme;
        $user = $request->user;
        
        if($user->id != $programme->created_by){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401);                       
        }
        
        return $next($request);        
          
    }
}
