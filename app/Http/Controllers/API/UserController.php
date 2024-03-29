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
use therealsmat\Ebulksms\EbulkSMS;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * sign up a user.
     * 
     */
    public function signup(Request $request, EbulkSMS $sms)
    {

        $verificationCode = 'S-'.mt_rand(1000000, 9999999);
        $user = new User;

        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->church_username = $request->username;
        $user->activation_token = $request->email ?
        str_random(60) : $verificationCode;
        $user->account_type = 'diamond';
        
        if ($user->save()) {
            
            if($user->email){
                $user->notify(new ConfirmEmail($user));            
            }


            if($user->phone){
                $message = "$verificationCode is your Sedmic verification code";
                $sms->fromSender('Sedmic')
                ->composeMessage($message)
                ->addRecipients($user->phone)
                ->send();
            }
            
            return response()->json([
                'successMessage' => $user->email ? 
                'A verification email has been sent' : 
                'A verification code has been sent to your phone',
            ], 201);   

        } else {
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }
    }


    public function tokenConfirmation(Request $request)
    {

        $token =  $request->query('token');
        $code =  $request->query('code');

        if(!$token && !$code){
            return response()->json([
                'errorMessage' => 'Invalid token or code'
            ], 400);
        }

        $user = null;

        if($token){
            $user = User::where('activation_token', $token)->first();
        }

        if($code){
            $user = User::where('activation_token', $code)->first();
        }
        

        $value = $token ? 'activation token': 'verification code';

        if(!$user){
            return response()->json([
                'errorMessage' => 'Invalid '.$value
            ], 404);
        }

        if($user){
            return response()->json([
                'successMessage' => 'verified successfully'
            ], 200);
        }

    }
    
    
    public function signupConfirmViaSMS(Request $request, EbulkSMS $sms)
    {
        
        $user = User::where('activation_token', $request->verification_code)
        ->first();

        if (!$user) {
            return response()->json([
                'errorMessage' => 'Invalid verification code'
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
        $user->email = $request->email;
        $user->date_of_birth = $request->date_of_birth;
        $user->password = Hash::make($request->password);
        

        if($user->save()) {

            if($user->active){
                if($user->email){
                    $user->notify(new AccountActivate($user));
                }

                if($user->phone){
                    $message = "Dear $user->full_name!. 
                    Your account at Sedmic is active. You can login now to
                    explore all Sedmic has to offer. Congratulation";
                    $sms->fromSender('Sedmic')
                    ->composeMessage($message)
                    ->addRecipients($user->phone)
                    ->send(); 
                }
                
                return response()->json([
                    'successMessage' => 'Account activation successful',
                    'user' => $user,
                    'token' => JWTAuth::fromUser($user)
                ], 201);
            }
            
            $superUser = User::where('username', $user->church_username)
            ->first();

            if($user->email){
                $user->notify(new CompleteSignup($user));
            }

            if($superUser->email){
                $superUser->notify(new AdminSignup($user, $superUser));                
            }
           
            //completeSignup
            if($user->phone){
                $message = "Dear $user->full_name! 
                You have completed all the requirement for signup.
                However, your account will be active when your admin
                activates it. Best";
                $sms->fromSender('Sedmic')
                ->composeMessage($message)
                ->addRecipients($user->phone)
                ->send(); 
            }

            //SuperUser
            if($superUser->phone){
                $message = "Dear $superUser->full_name! 
                $user->username has completed all requirement for signup. 
                However, the account will remain inactive until you activate it. 
                Best.";
                $sms->fromSender('Sedmic')
                ->composeMessage($message)
                ->addRecipients($superUser->phone)
                ->send();
            }
            
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
    
    
    public function signupConfirmViaEmail(Request $request, EbulkSMS $sms, $token)
    {
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
        $user->phone = $request->phone;
        $user->date_of_birth = $request->date_of_birth;
        $user->password = Hash::make($request->password);

        if($user->save()) {
            if($user->active){
                if($user->email){
                    $user->notify(new AccountActivate($user));
                }
                
                if($user->phone){
                    $message = "Dear $user->full_name! 
                    Your account at Sedmic is active. You can login now to
                    explore all Sedmic has to offer. Congratulation";
                    $sms->fromSender('Sedmic')
                    ->composeMessage($message)
                    ->addRecipients($user->phone)
                    ->send(); 
                }
                
                
                return response()->json([
                    'successMessage' => 'Account activation successful',
                    'user' => $user,
                    'token' => JWTAuth::fromUser($user)
                ], 201);
            }

            $superUser = User::where('username', $user->church_username)
            ->first();

            if($user->email){
                $user->notify(new CompleteSignup($user));
            }

            if($superUser->email){
                $superUser->notify(new AdminSignup($user, $superUser));                
            }
           
            //completeSignup
            if($user->phone){
                $message = "Dear $user->full_name! 
                You have completed all the requirement for signup.
                However, your account will be active when your admin
                activates it. Best";
                $sms->fromSender('Sedmic')
                ->composeMessage($message)
                ->addRecipients($user->phone)
                ->send(); 
            }

            //SuperUser
            if($superUser->phone){
                $message = "Dear $superUser->full_name! 
                $user->username has completed all requirement for signup. 
                However, the account will remain inactive until you activate it. 
                Best.";
                $sms->fromSender('Sedmic')
                ->composeMessage($message)
                ->addRecipients($superUser->phone)
                ->send();
            }
            
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
    
    
    public function login(Request $request)
    {

    $user = User::where('username', $request->username)->first();
    $token = JWTAuth::attempt([
        'username' => $request->username,
        'password' => $request->password,
        'active' => 1
    ]);

    try {

        if (!$user || !$token) {
            return response()->json([
                'errorMessage' => 'Invalid username or password'
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
     * sign up an admin
     * 
     */
    public function createAdmin(Request $request, EbulkSMS $sms)
    {
        $superUser = auth()->user();
        $verificationCode = 'S-'.mt_rand(1000000, 9999999);

        $user = new User;

        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->church_username = $superUser->church_username;
        $user->activation_token = $request->email ?
        str_random(60) : $verificationCode;
        $user->account_type = 'gold';
        
        if ($user->save()) {
            if($user->email){
                $user->notify(new AdminConfirm($user, $superUser));
            }

            if($user->phone){
                $url = 'http://localhost:8000/api/v1/auth/confirm-sms';
                $message = "Hi There! $superUser->full_name has created 
                an account for you at Sedmic. Please log on to $url 
                to complete the process. $verificationCode is your 
                Sedmic verification code. USERNAME: $user->username";
                $sms->fromSender('Sedmic')
                ->composeMessage($message)
                ->addRecipients($user->phone)
                ->send(); 
            }
            
            return response()->json([
                'successMessage' => 'A confirmation message has been sent to this user',
            ], 201); 

        } else {
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }

    }


    public function activateAdmin(Request $request, EbulkSMS $sms, $userId)
    {
        $user = User::find($userId);
        $user->active = true;
        $user->number_of_activation = $user->number_of_activation + 1;
       
        if ($user->save()) {

            if($user->number_of_activation === 1){
                if($user->email){
                    $user->notify(new AccountActivate($user));
                }
                if($user->phone){
                    $message = "Hi There! Your account at Sedmic is active now. 
                    Once again your username is $user->username. 
                    Thanks for using Sedmic";
                    $sms->fromSender('Sedmic')
                    ->composeMessage($message)
                    ->addRecipients($user->phone)
                    ->send(); 
                }
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
