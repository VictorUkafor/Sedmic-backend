<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateInvitees
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
            'invitees' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        }

        $invitees = json_decode(json_encode($request->invitees), true);
        $invitees = json_decode($invitees, true);

        if(!$attendees['members'] || 
        !$attendees['firstTimers'] || 
        !$attendees['slips']){
            return response()->json([
                'successMessage' => 'Contacts could not be found'
            ], 404);             
        }

        $request->invitees = $invitees;
        return $next($request);
    }
}
