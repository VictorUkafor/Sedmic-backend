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


Route::group([
    'namespace' => 'API','prefix' => 'v1'
], function () {
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
            Route::post('/', 'UserController@createAdmin')
            ->middleware('validateConfirm');
                
            Route::middleware([
                'findUser', 'myAdmins'
                ])->group(function () {
                
                // active admin
                Route::post('/activate/{userId}', 'UserController@activateAdmin');

                // block admin
                Route::post('/block/{userId}', 'UserController@blockAdmin');

                // remove admin
                Route::post('/remove/{userId}', 'UserController@removeAdmin');
            
            });

        });

        // login route
        Route::post('login', 'UserController@login')
        ->middleware('validateLogin');
    
    });


    Route::group([
        'middleware' => 'jwt.auth', 'prefix' => 'user'
    ], function () {
        
        // update user
        Route::post('/update', 'UserController@update');

    });
});
