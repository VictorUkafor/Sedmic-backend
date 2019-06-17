<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class IncomeTypeName
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
        $name = $request->incomeType->name; 

        $incomeType = $request->church->incomeTypes()
        ->where('name', $request->name)->first();

        if ($incomeType && $incomeType->name != $name){
            return response()->json([
                'errorMessage' => "Income type name has been taken"
            ], 404);
        }

        return $next($request);

    }
}
