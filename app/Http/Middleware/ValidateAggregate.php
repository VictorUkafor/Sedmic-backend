<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateAggregate
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
            'name' => 'required',
            'level' => 'required',
            'sub_unit_type' => 'required',
            'type' => 'required',
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
