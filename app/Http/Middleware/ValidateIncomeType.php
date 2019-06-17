<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateIncomeType
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
            'format' => 'required',
            'currency' => 'required',
            'prize' => 'regex:/^\S*$/u'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        $incomeType = $request->church->incomeTypes()
        ->where('name', $request->name)->first();

        if ($incomeType){
            return response()->json([
                'errorMessage' => 'Income type exist already'
            ], 400);
        }


        return $next($request);

    }
}
