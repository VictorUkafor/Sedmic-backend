<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateSlip
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
            'campaign' => 'required',
            'first_name' => 'required|regex:/^\S*$/u',
            'last_name' => 'required|regex:/^\S*$/u',
            'email' => 'email',
            'image' => 'image',
            'ministered_by' => 'required|exists:members,id'
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
