<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Validator;

class ValidateUsername
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
            'username' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        $user = User::where('username', $request->username)->first();

        if (!$user){
            return response()->json([
                'errorMessage' => "Invalid username"
            ], 404);
        }

        $request->user = $user;

        return $next($request);

    }
}
