<?php

namespace App\Http\Middleware;

use App\Programme;
use App\Handler;
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
        $church = $request->church;
        $programme = Programme::find($id);
        $handlersTo = Handler::where('user_id', $request->user->id)
        ->pluck('programme_id')->toArray();

        
        if (!$programme){
            return response()->json([
                'errorMessage' => 'Programme could not be found'
            ], 404);
        }
        

        if($programme->church_id == $church->id &&
        $programme->type_of_meeting == 'open' || 
        $programme->created_by == $request->user->id ||
        in_array($programme->id, $handlersTo)){

            $request->programme = $programme;
            return $next($request);

        }

        return response()->json([
            'errorMessage' => 'Unauthorized'
        ], 401); 
    }
}
