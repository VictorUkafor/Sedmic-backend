<?php

namespace App\Http\Middleware;

use App\Handler;
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
        $id = $request->route('userId');
        $handlers = Handler::where('programme_id',$request->programme->id)
        ->pluck('user_id');


        if(!in_array($id, $handlers->toArray()) || $id == $request->programme->created_by){
            return response()->json([
                'errorMessage' => 'Handler could not be found'
            ], 404);     
        }
        
        $handlerId =  Handler::where([
            'programme_id' => $request->programme->id ,
            'user_id' => $id
        ])->first()->id;

        $request->handlerId = $handlerId;
        return $next($request);
        
    }
}
