<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateChurchCreate
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
            'name_of_church' => 'required',
            'images' => 'image',
            'official_email' => 'email',
            'sms_sender_name' => 'max:11',
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
