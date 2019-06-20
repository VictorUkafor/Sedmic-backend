<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateUpdateService
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
            'start_time' => 'date_format:H:i:s',
            'end_time' => 'date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        if ($request->start_time > $request->end_time){
            return response()->json([
                'errorMessage' => 'Start time greater than end time'
            ], 400);
        }

        $serviceExist  =  $request->programme->orderOfServices()
        ->where('title', $request->title)->first();

        if($serviceExist && $serviceExist->id != $request->service->id){
            return response()->json([
                'errorMessage' => 'Service already exist'
            ], 400); 
        }

        return $next($request);

    }
}
