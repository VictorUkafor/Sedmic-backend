<?php

namespace App\Http\Middleware;

use App\Programme;
use Closure;
use Validator;

class ValidateProgramme
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
            'title' => 'required',
            'date' => 'required|date',
            'time_starting' => 'required',
            'time_ending' => 'required',
            'type_of_meeting' => 'required',
            'invitees' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 


        if ($request->time_ending < $request->time_starting){
            return response()->json([
                'errorMessage' => 'Time ending must be greater than time starting'
            ], 400);
        }


        if ($request->report == 0 && date('Y-m-d') > $request->date){
            return response()->json([
                'errorMessage' => 'Date must be in the future'
            ], 400);
        }

        if ($request->report == 1 && date('Y-m-d') < $request->date){
            return response()->json([
                'errorMessage' => 'Date must be in the past'
            ], 400);
        }


        $programme = Programme::where([
            'title' => $request->title,
            'church_id' => $request->church->id,
        ])->first();

        if ($programme){
            return response()->json([
                'errorMessage' => 'Programme exist already'
            ], 400);
        }

        return $next($request);

    }
}
