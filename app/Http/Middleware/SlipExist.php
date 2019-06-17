<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class SlipExist
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
        $id = $request->route('slipId');

        $slip = $request->church->slips()
        ->where('id', $id)->first();
        

        if(!$slip){
            return response()->json([
                'errorMessage' => 'Slip can not be found'
            ], 404); 
        }
        
        $request->slip = $slip;
        return $next($request);
        
    }
}
