<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateIncome
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
            'currency' => 'required',
            'amount' => 'required',
            'member' => 'exists:members,id',
            'cash' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        
        $incomeType = $request->incomeType;
        $request->member = $incomeType->format == 'cummulative' ?
        $request->user->id : $request->member;

        if(!$request->member){
            return response()->json([
                'errorMessage' => 'The member field is required'
            ], 400);
        }

        return $next($request);

    }
}
