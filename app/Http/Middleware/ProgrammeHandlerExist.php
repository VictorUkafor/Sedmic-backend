<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ProgrammeHandlerExist
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
        $userId = $request->route('userId');
        $handlers = $request->programme->handlers()
        ->pluck('user_id')->toArray();


        if(!in_array($userId, $handlers)){
            return response()->json([
                'errorMessage' => 'Handler could not be found'
            ], 404);     
        }


        if($userId == $request->programme->created_by){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401);     
        }
        
        $handlerId =  $request->programme->handlers()
        ->where('user_id', $userId)->first()->id;

        $request->handlerId = $handlerId;
        return $next($request);
        
    }
}
