<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ProgrammeType
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
        $validator = Validator::make($request->all(), [
            'type_of_meeting' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        $programme = $request->programme;

        if($request->type_of_meeting !== 'closed' && $request->type_of_meeting !== 'open'){
            return response()->json([
                'errorMessage' => 'Invalid meeting type'
            ], 401);
        }

        if($request->type_of_meeting == $programme->type_of_meeting){
            return response()->json([
                'errorMessage' => 'The programme is already '.$programme->type_of_meeting
            ], 401);
        }
        
        return $next($request);
 
    }
}
