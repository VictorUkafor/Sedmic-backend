<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class EditProgramme
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
        $programme = $request->programme;

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 


        $time_starting = $request->time_ending ?
        $request->time_ending : $programme->time_ending;

        $time_ending = $request->time_ending ?
        $request->time_ending : $programme->time_ending;

        $date = $request->date ?
        $request->date : $programme->date;

        $report = $request->report ?
        $request->report : $programme->report;


        if ($time_ending < $time_starting){
            return response()->json([
                'errorMessage' => 'Time ending must be greater than time starting'
            ], 400);
        }


        if ($report == 0 && date('Y-m-d') < $date){
            return response()->json([
                'errorMessage' => 'Date must be in the future'
            ], 400);
        }

        if ($report == 1 && date('Y-m-d') > $date){
            return response()->json([
                'errorMessage' => 'Date must be in the past'
            ], 400);
        }

        $findProgramme = $request->church->programmes()
        ->where('title', $request->title)->first();

        if ($findProgramme && $findProgramme->title != $programme->title){
            return response()->json([
                'errorMessage' => 'Programme exist already'
            ], 400);
        }

        return $next($request);

    }
}
