<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateMemberSignup
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
            'first_name' => ['required', 'regex:/^\S*$/u'],
            'last_name' => ['required', 'regex:/^\S*$/u'],
            'image' => 'image',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        return $next($request);
    }
}
