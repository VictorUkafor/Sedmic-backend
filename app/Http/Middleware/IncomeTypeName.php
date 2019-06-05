<?php

namespace App\Http\Middleware;

use App\IncomeType;
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

        $incomeType = IncomeType::where([
            'church_id'=> $request->church->id,
            'name' => $request->name
        ])->first();

        if ($incomeType && $incomeType->name != $name){
            return response()->json([
                'errorMessage' => "Income type name has been taken"
            ], 404);
        }

        return $next($request);

    }
}
