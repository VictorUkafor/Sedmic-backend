<?php

namespace App\Http\Middleware;

use App\Church;
use Closure;
use Validator;

class ImageExist
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
        $user = auth()->user();
        $image = $request->route('image');
        $church = Church::where('username', $user->username)
        ->first();
        
        
        if(strpos($church->images, $image) !== false){
            return $next($request);   
        }

        return response()->json([
            'errorMessage' => 'Image can not be found'
        ], 404);
                    
    }
}
