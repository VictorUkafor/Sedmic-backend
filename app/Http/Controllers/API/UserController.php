<?php

namespace App\Http\Controllers\API;

use App\User;
use JWTFactory;
use JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\ConfirmEmail;
use App\Notifications\AdminConfirm;
use App\Notifications\AccountActivate;
use App\Notifications\CompleteSignup;
use App\Notifications\AdminSignup;
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
    public function signupConfirm(Request $request)
    {
        $user = new User;

        $user->username = $request->username;
        $user->email = $request->email;
        $user->church_username = $request->username;
        $user->activation_token = str_random(60);
        $user->account_type = 'diamond';
        
        if ($user->save()) {
            $user->notify(new ConfirmEmail($user));
            return response()->json([
                'successMessage' => 'Check your mail for a confirmation link',
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

    
    $active = $user->account_type === 'diamond' ? true : false;
    $image_public_id = $request->image ? $user->username : '';

    if($request->image){
    Cloudder::upload($request->image->getRealPath(), $image_public_id);
    }

    $user->active = $active;
    $user->activation_token = '';
    $user->full_name = strtolower(preg_replace('/\s+/', ' ', $request->full_name));
    $user->image = $image_public_id;
    $user->sex = $request->sex;
    $user->date_of_birth = $request->date_of_birth;
    $user->password = Hash::make($request->password);

    if($user->save()) {
        if($user->active){

            $user->notify(new AccountActivate($user));
            return response()->json([
                'successMessage' => 'Account activation successful',
                'user' => $user,
                'token' => JWTAuth::fromUser($user)
            ], 201);           
        }

        $superUser = User::where('username', $user->church_username)
        ->first();

        $user->notify(new CompleteSignup($user));
        $superUser->notify(new AdminSignup($user, $superUser));
        return response()->json([
            'successMessage' => 'Signup successful. '. 
            'Please contact your admin for activation',
            'user' => $user,
        ], 201);
    }

    return response()->json([
        'errorMessage' => 'Internal server error'
    ], 500);
}


public function login(Request $request){

    $user = User::where('username', $request->username)->first();
    $logins = $request->only('username', 'password');
    $token = JWTAuth::attempt($logins);

    try {

        if (!$user || !$token) {
            return response()->json([
                'errorMessage' => 'Invalid username or password'
            ], 401);
        } 
        
        if(!$user->active){
        return response()->json([
            'errorMessage' => 'Your account is inactive.'.
             ' Please contact your admin'
            ], 401);
        }

    } catch (JWTException $e) {

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }

    return response()->json([
        'token' => $token
    ], 201);

}


    /**
     * sign up a user.
     * 
     */
    public function createAdmin(Request $request)
    {
        $superUser = auth()->user();

        $user = new User;

        $user->username = $request->username;
        $user->email = $request->email;
        $user->church_username = $superUser->church_username;
        $user->activation_token = str_random(60);
        $user->account_type = 'gold';
        
        if ($user->save()) {
            $user->notify(new AdminConfirm($user, $superUser));
            return response()->json([
                'successMessage' => 'A confirmation mail has been sent to this user',
            ], 201); 

        } else {
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }

    }


    public function activateAdmin(Request $request, $userId)
    {
        $user = User::find($userId);
        $user->active = true;
        $user->number_of_activation = $user->number_of_activation + 1;
       
        if ($user->save()) {

            if($user->number_of_activation === 1){
              $user->notify(new AccountActivate($user));  
            }
            
            return response()->json([
                'successMessage' => 'Action successful',
            ], 201); 

        } else {
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }
    }


    public function blockAdmin(Request $request, $userId)
    {
        $user = User::find($userId);
        $user->active = false;
       
        if ($user->save()) {
            return response()->json([
                'successMessage' => 'Action successful',
            ], 201); 

        } else {
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }
    }


    public function changeRight(Request $request, $userId)
    {
        $user = User::find($userId);
        $user->account_type = $request->account_type ? 
        $request->account_type : $user->account_type;
       
        if ($user->save()) {
            return response()->json([
                'successMessage' => 'Action successful',
                'user' => $user,
            ], 201); 

        } else {
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }
    }


    public function removeAdmin(Request $request, $userId)
    {
        $soft_delete_user = User::destroy($userId);
       
        if ($soft_delete_user) {
            return response()->json([
                'successMessage' => 'Action successful',
            ], 200); 

        } else {
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }
    }


    public function show(Request $request)
    {
        $user = auth()->user();

        if($user->account_type === 'diamond'){
            $myAdmins = User::where('church_username', $user->church_username)
            ->whereNotIn('id', [$user->id])->get();

            $user->admins = $myAdmins;            
        }

        
        if ($user) {
            return response()->json([
                'successMessage' => 'User Profile',
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }

      }


      public function update(Request $request)
      {
          $user = auth()->user();
                  
          // get the RealPath of new user image
          $image_name = $request->image ? 
          $request->image->getRealPath() : '';  
  
          // updates record
          $user->email = $request->email ?
          $request->email : $user->email;
  
          $user->full_name = $request->full_name ?
          $request->full_name : $user->full_name;
  
          $user->image = $request->image ?
          $user->username : '';
  
          $user->sex = $request->sex?
          $request->sex : $user->sex;
  
          $user->date_of_birth = $request->date_of_birth ?
          $request->date_of_birth : $user->date_of_birth;
          
  
          // if an image is uploaded, save to cloudinary
          // and deletes the previous one
          if(strlen($image_name) !== 0){
              if(Cloudder::delete($user->username)){
                Cloudder::upload($image_name, $user->username);  
              }   
          } 
          
          if ($user->save()) {
              return response()->json([
                  'successMessage' => 'Profile updated successfully',
                  'user' => $user
              ], 201);
          } else {
              return response()->json([
                  'errorMessage' => 'Internal server error'
              ], 500);
          }
  
        }


}
