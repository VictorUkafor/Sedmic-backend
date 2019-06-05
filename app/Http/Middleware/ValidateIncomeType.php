<?php

namespace App\Http\Middleware;

use App\IncomeType;
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

        $incomeType = IncomeType::where([
            'name' => $request->name,
            'church_id' => $request->church->id,
        ])->first();

        if ($incomeType){
            return response()->json([
                'errorMessage' => 'Income type exist already'
            ], 400);
        }


        return $next($request);

    }
}
