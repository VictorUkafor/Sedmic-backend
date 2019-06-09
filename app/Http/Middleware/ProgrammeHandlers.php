<?php

namespace App\Http\Middleware;

use App\Handler;
use App\Programme;
use Closure;
use Validator;

class ProgrammeHandlers
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
        $programmeId = $request->programme->id;
        $userId = $request->user->id;
        $handlers = Handler::where('programme_id', $programmeId)
        ->pluck('user_id');

        
        if(!in_array($userId, $handlers->toArray())){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401);                       
        }
        
        return $next($request);        
          
    }
}
