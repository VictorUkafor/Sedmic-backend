<?php

namespace App\Http\Middleware;

use App\Slip;
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
        $church = $request->church;
        $slip = Slip::find($id);
        

        if(!$slip){
            return response()->json([
                'errorMessage' => 'Slip can not be found'
            ], 404); 
        }

        if($slip->church_id !== $church->id){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401); 
        }
        
        $request->slip = $slip;
        return $next($request);
        
    }
}
