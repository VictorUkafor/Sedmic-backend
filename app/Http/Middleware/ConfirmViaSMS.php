<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ConfirmViaSMS
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
            'verification_code' => 'required',
            'full_name' => 'required',
            'email' => 'email|required',
            'image' => 'nullable|image',
            'date_of_birth' => 'date',
            'password' => 'required|min:7|alpha_num|confirmed',
            'password_confirmation' => 'required|same:password',
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
