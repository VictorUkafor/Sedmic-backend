<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Validator;

class ValidateHandlers
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
        if($request->handlers){
            
            $handlers = explode(" ", $request->handlers);
            $invalid_handlers = [];

            foreach($handlers as $handler){
                $own_user = User::where(['username' => $handler])->first();
                if(!$own_user || $own_user->church_username !==
                 $request->church->username){

                    array_push($invalid_handlers, $handler);
                }
            }
            
            if(count($invalid_handlers)){
                $bad_handlers = implode(", ", $invalid_handlers);
                return response()->json([
                    'errorMessage' => 'Invalid handler(s): '.$bad_handlers
                ], 401);
            }
        }

        
        return $next($request);    
    }
}
