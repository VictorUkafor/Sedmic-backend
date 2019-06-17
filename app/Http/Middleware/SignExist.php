<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class SignExist
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
        $id = $request->route('signId');
        $sign = $request->invitee->signs()
        ->where('id', $id)->first();
        
        if (!$sign){
            return response()->json([
                'errorMessage' => 'Sign could not be found'
            ], 404);
        }
        
        $request->sign = $sign;
        return $next($request);

    }
}
