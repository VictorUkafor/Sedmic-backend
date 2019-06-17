<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class IncomeTypeExist
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
        $id = $request->route('incomeTypeId'); 

        $incomeType = $request->church->incomeTypes()
        ->where('id', $id)->first();

        if (!$incomeType){
            return response()->json([
                'errorMessage' => "Income type can not be found"
            ], 404);
        }

        $request->incomeType = $incomeType;
        return $next($request);

    }
}
