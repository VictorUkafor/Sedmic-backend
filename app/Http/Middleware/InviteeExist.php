<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class InviteeExist
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
        $id = $request->route('inviteeId');
        $invitee = $request->programme->invitees()
        ->where('id', $id)->first();
        
        if (!$invitee){
            return response()->json([
                'errorMessage' => 'Invitee could not be found'
            ], 404);
        }
        
        $request->invitee = $invitee;
        return $next($request);

    }
}
