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
            'first_timer' => 'exists:first_timers,id',
            'slip' => 'exists:slips,id',
            'cash' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 


        $incomeType = $request->incomeType;

        if((!$request->member || !$request->first_timer || !$request->slip) && 
        ($incomeType->format == 'individualize' || $incomeType->cash)){
            return response()->json([
                'errorMessage' => 'Please select the appropriate contact'
            ], 400);
        }

        return $next($request);

    }
}
