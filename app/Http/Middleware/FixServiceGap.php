<?php

namespace App\Http\Middleware;

use Closure;

class FixServiceGap
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

        if($services && $services[0]['start_time'] !=
         $programme['time_starting']){
            $gaps[] = [
                'start_time' => $programme['time_starting'],
                'end_time' => $services[0]['start_time'],
                'order' => 1
            ];
        }

        for($x = 0; $x < count($services)-1; $x++){
                if($services[$x]['end_time'] != $services[$x+1]['start_time']){
                    $gaps[] = [
                        'start_time' => $services[$x]['end_time'],
                        'end_time' => $services[$x+1]['start_time'],
                        'order' => $x+2
                    ];
                }
        }

        if(!count($gaps)){
            return response()->json([
                'errorMessage' => 'There are no service gaps'
            ], 404);
        }
            

        $request->gaps = $gaps;
        return $next($request);   
    }
}
