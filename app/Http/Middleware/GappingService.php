<?php

namespace App\Http\Middleware;

use Closure;

class GappingService
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
        $services = $request->programme->orderOfServices()
        ->orderBy('order', 'asc')->get();
        
        $services = $services ? $services->toArray() : [];
        $gaps = [];

        if(count($services) && $services[0]['start_time'] !=
         $programme['time_starting']){
            $gaps[] = [1];
        }

        for($x = 0; $x < count($services)-1; $x++){
                if($services[$x]['end_time'] != $services[$x+1]['start_time']){
                    $gaps[] = [$x+2];
                }
        }

        if(count($gaps)){
            return response()->json([
                'errorMessage' => 'Please fix the service gaps'
            ], 404);
        }
            

        return $next($request);   
    }
}
