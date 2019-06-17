<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ProgrammeExist
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
        $id = $request->route('programmeId');
        $programme = $request->church->programmes()
        ->where('id', $id)->first();

        $handlers = $programme->handlers()
        ->pluck('user_id')->toArray();

        
        if (!$programme){
            return response()->json([
                'errorMessage' => 'Programme could not be found'
            ], 404);
        }
        

        if($programme->type_of_meeting == 'open' || 
        $programme->created_by == $request->user->id ||
        in_array($request->user->id, $handlers)){

            $request->programme = $programme;
            return $next($request);

        }

        return response()->json([
            'errorMessage' => 'Unauthorized'
        ], 401); 
    }
}
