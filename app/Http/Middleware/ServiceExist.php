<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ServiceExist
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
        $id = $request->route('serviceId');
        $service = $request->programme->orderOfServices()
        ->where('id', $id)->first();
        
        if (!$service){
            return response()->json([
                'errorMessage' => 'Service could not be found'
            ], 404);
        }
        
        $request->service = $service;
        return $next($request);

    }
}
