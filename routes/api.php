<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['namespace' => 'API','prefix' => 'v1'], function () {
    Route::prefix('auth')->group(function () {

        Route::prefix('signup')->group(function () {
            
            // sigup_email_confirmation route
            Route::post('/', 'UserController@signupConfirm')
            ->middleware('validateConfirm');
            
            // signup_completion route
            Route::post('activate/{token}', 'UserController@signupActivate')
            ->middleware('validateActivate');
        });
        
        // user admins routes
        Route::group([
            'middleware' => 'jwt.auth', 'prefix' => 'admin'
        ], function () {
            
            // create admin
            Route::post('/create', 'UserController@createAdmin')
            ->middleware('validateConfirm');
                
            Route::middleware([
                'findUser', 'myAdmins'
                ])->group(function () {
                
                // active admin
                Route::post('/activate/{userId}', 'UserController@activateAdmin')
                ->middleware('signupSuccess');

                // block admin
                Route::post('/block/{userId}', 'UserController@blockAdmin')
                ->middleware('signupSuccess');

                // remove admin
                Route::delete('/remove/{userId}', 'UserController@removeAdmin');

                // change admin right
                Route::post('/right/{userId}', 'UserController@changeRight');
            
            });
        });


        // login route
        Route::post('login', 'UserController@login')
        ->middleware('validateLogin');


        // // Password resets routes
        Route::prefix('password-reset')->group(function () {
            
            // request password reset route
            Route::post('/request', 'PasswordResetController@create')
           ->middleware('validateUsername');
        
            // get token for reset
            Route::get('/find/{token}', 'PasswordResetController@find');    
        
            // reset password route
            Route::post('/reset/{token}', 'PasswordResetController@reset')
            ->middleware('validatePassword');

        });

    });


    Route::middleware('jwt.auth')->group(function () {
        
        // user routes
        Route::prefix('user')->group(function () {
            
            // show user
            Route::get('/', 'UserController@show');

            // update user
            Route::put('/update', 'UserController@update');
        
        });
        
        
        // church crude route
        Route::prefix('church')->group(function () {

            // fetch church
            Route::get('/', 'ChurchController@show')
            ->middleware('churchCreated');

            Route::middleware('checkDiamond')->group(function () {
                
                // create church
                Route::post('/create', 'ChurchController@create')
                ->middleware(['churchNotCreated','validateChurch']);            
                

                Route::middleware('churchCreated')->group(function () {
                    
                    // fetch church
                    Route::get('/', 'ChurchController@show');
                
                    // update church info
                    Route::put('/update', 'ChurchController@update');
            
                    // upload church image
                    Route::post('/upload-image', 'ChurchController@uploadImage')
                    ->middleware('validateImage');
            
                    // delete church image
                    Route::post('/delete-image/{image}', 'ChurchController@deleteImage')
                    ->middleware('imageExist');
                
                });
            
            });
        
        });


        Route::group([
           'prefix' => 'members', 'middleware' => 'churchCreated'
        ],function () {
            
            // create member
            Route::post('/', 'MemberController@create')
            ->middleware('memberSignup');

            // view members
            Route::get('/', 'MemberController@viewAll')
            ->middleware('membersExist');


            Route::middleware('memberExist')->group(function () {
                
                // view member
                Route::get('/{member_id}', 'MemberController@show');

                // update member
                Route::put('/{member_id}', 'MemberController@update')
                ->middleware('canUpdate');

                // update member
                Route::delete('/{member_id}', 'MemberController@delete')
                ->middleware('canDelete');
            
            });

        });
    
    });

});
