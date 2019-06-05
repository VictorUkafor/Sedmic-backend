<?php

namespace App\Http\Middleware;

use App\Income;
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
        $church = $request->church;
        $income = Income::find($id);
        

        if(!$income){
            return response()->json([
                'errorMessage' => 'Income can not be found'
            ], 404); 
        }

        if($income->church_id !== $church->id){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401); 
        }
        
        $request->income = $income;
        return $next($request);
        
    }
}
