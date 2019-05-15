<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\AccountActivate;
use Illuminate\Support\Str;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * sign up a user.
     * 
     */
    public function signup(Request $request)
    {
        $user = new User;

        $user->username = $request->username;
        $user->email = $request->email;
        $user->church_username = $request->username;
        $user->password = Hash::make($request->password);
        $user->activation_token = str_random(60);
        $user->account_type = 'diamond';
        
        if ($user->save()) {
            $user->notify(new AccountActivate($user));
            return response()->json([
                'successMessage' => 'Check your mail for an activation link',
            ], 201); 

        } else {
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }
    }
    
    
    public function signupActivate(Request $request, $token){

    $user = User::where('activation_token', $token)->first();
    if (!$user) {
        return response()->json([
            'errorMessage' => 'This activation token is invalid.'
        ], 404);
    }

    $image_public_id = '';

    if($request->image){
    $image_public_id = str_replace(' ', '', $request->name);

    Cloudder::upload($request->image->getRealPath(), $image_public_id);
    }

    $user->active = true;
    $user->activation_token = '';
    $user->full_name = $request->full_name;
    $user->image = $image_public_id;

    if($user->save()) {
        return response()->json([
            'successMessage' => 'Account activation successful'
        ], 201);
    }

    return response()->json([
        'errorMessage' => 'Internal server error'
    ], 500);


}

}
