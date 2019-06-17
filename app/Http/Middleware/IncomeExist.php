<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class IncomeExist
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
        $id = $request->route('incomeId');
        
        $income = $request->church->incomes()
        ->where('id', $id)->first();
        

        if(!$income){
            return response()->json([
                'errorMessage' => 'Income can not be found'
            ], 404); 
        }

        
        $request->income = $income;
        return $next($request);
        
    }
}
