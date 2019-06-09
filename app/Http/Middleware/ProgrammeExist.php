<?php

namespace App\Http\Middleware;

use App\Programme;
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

        if (!$programme){
            return response()->json([
                'errorMessage' => 'Programme could not be found'
            ], 404);
        }

        if($programme->church_id !== $church->id){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401); 
        }

        $request->programme = $programme;
        return $next($request);

    }
}
