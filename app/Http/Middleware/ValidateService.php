<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateService
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
            'anchor' => 'required',
            'title' => 'required',
            'duration' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        $lastService = $request->programme->orderOfServices()
        ->latest()->first();

        $programmeStart = $request->programme->time_starting;

        $startTime = $lastService ? $lastService->end_time : $programmeStart;

        $endTime = strtotime("+$request->duration minutes", strtotime($startTime ));

        $endTime = date('H:i:s', $endTime);

        $anchorIsInvited = $request->programme->invitees ? 
        $request->programme->invitees()->where('id', $request->anchor)
        ->first() : null;

        if(!$anchorIsInvited){
            return response()->json([
                'errorMessage' => 'Anchor must be an invitee'
            ], 401); 
        }

        if($endTime > $request->programme->time_ending){
            return response()->json([
                'errorMessage' => 
                'The duration of your service(s) is greater than the programme end time'
            ], 401);
        }

        $request->start_time = $startTime;
        $request->end_time = $endTime;
        return $next($request);

    }
}
