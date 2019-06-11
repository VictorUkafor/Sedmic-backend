<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateSignup
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
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'min:7', 'unique:users,username', 'regex:/^\S*$/u'],
            'email' => 'email',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        }

        if(!$request->email && !$request->phone){
            return response()->json([
                'errorMessage' => 'Email or Phone number must be provided'
            ], 400);  
        }

        return $next($request);
    }
}
