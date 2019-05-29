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

        // user admins routes
        Route::group([
            'middleware' => 'checkDiamond', 'prefix' => 'admins'
        ], function () {
            
            // create admin
            Route::post('/', 'UserController@createAdmin')
            ->middleware('validateConfirm');
                
            Route::middleware(['findUser', 'myAdmins'])->group(function () {

                Route::middleware('signupSuccess')->group(function () {
                
                    // activate admin
                    Route::post('/{userId}/activate', 'UserController@activateAdmin');

                    // block admin
                    Route::post('/{userId}/block', 'UserController@blockAdmin');
                
                });

                // remove admin
                Route::delete('/{userId}/remove', 'UserController@removeAdmin');

                // change admin right
                Route::post('/{userId}/right', 'UserController@changeRight');
            
            });
        });
        
        
        // user routes
        Route::prefix('user')->group(function () {
            
            // show user
            Route::get('/', 'UserController@show');

            // update user
            Route::put('/update', 'UserController@update');
        
        });
        
        
        // church routes
        Route::prefix('church')->group(function () {
            
            Route::middleware('checkDiamond')->group(function () {
                
                // create church
                Route::post('/', 'ChurchController@create')
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


        Route::middleware('churchCreated')->group(function () {
            
            Route::prefix('members')->group(function () {
                
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
            
            
            // units
            Route::prefix('units')->group(function () {
                
                // create unit
                Route::post('/', 'UnitController@create')
                ->middleware(['diamondOrGold', 'validateUnit', 
                'uniqueName', 'maxHandlers', 'validateHandlers']);

                // view all units
                Route::get('/', 'UnitController@viewAll')
                ->middleware('unitsExist');
                
                
                Route::middleware('unitExist')->group(function () {
                    
                    Route::middleware('unitHandlers')->group(function () {
                        
                        // view a single unit
                        Route::get('/{unit_id}', 'UnitController@show');

                        // update a single unit
                        Route::put('/{unit_id}', 'UnitController@update')
                        ->middleware(['imageNotRequired', 'uniqueName']);

                        Route::middleware('memberExist')->group(function () {
                            
                            // add member to unit
                            Route::post('/{unit_id}/members/{member_id}', 
                            'UnitMemberController@addMember')->middleware('isMember');

                            // remove member from unit
                            Route::delete('/{unit_id}/members/{member_id}', 
                            'UnitMemberController@removeMember')->middleware('notMember');

                        });

                    
                    });
                    
                    
                    Route::middleware('diamondOrGold')->group(function () {
                        
                        // adds an handler
                        Route::post('/{unit_id}/add-handlers', 'UnitController@addHandlers')
                        ->middleware('addUnitHandlers');

                        // removes an handler
                        Route::post('/{unit_id}/remove-handler/{handler}', 
                        'UnitController@removeHandler');

                        // deletes a unit
                        Route::delete('/{unit_id}', 'UnitController@delete');
                    
                    });
                
                });
            
            });

            // units
            Route::prefix('aggregates')->group(function () {
                
                // create an aggregate
                Route::post('/', 'AggregateController@create')
                ->middleware(['diamondOrGold', 'validateAggregate', 
                'uniqueAggName', 'maxHandlers', 'noSubs', 'validateHandlers']);

                // view all aggregates
                Route::get('/', 'AggregateController@viewAll')
                ->middleware('aggregatesExist');
                
                
                Route::middleware('aggregateExist')->group(function () {
                    
                    Route::middleware('aggregateHandlers')->group(function () {
                        
                        // view a single aggregate
                        Route::get('/{aggregate_id}', 'AggregateController@show');

                        // update a single aggregate
                        Route::put('/{aggregate_id}', 'AggregateController@update')
                        ->middleware(['imageNotRequired', 'uniqueAggName']);

                        // add sub to aggregate
                        Route::post('/{aggregate_id}/subs/{subId}', 'SubController@addSub')
                        ->middleware('freeSub');

                        // remove sub to aggregate
                        Route::delete('/{aggregate_id}/subs/{subId}', 'SubController@removeSub')
                        ->middleware('subNotYours');
                    
                    });
                    
                    
                    Route::middleware('diamondOrGold')->group(function () {
                        
                        // adds an handler
                        Route::post('/{aggregate_id}/add-handlers', 'AggregateController@addHandlers')
                        ->middleware('addAggregateHandlers');

                        // removes an handler
                        Route::post('/{aggregate_id}/remove-handler/{handler}', 
                        'AggregateController@removeHandler');

                        // deletes a unit
                        Route::delete('/{aggregate_id}', 'AggregateController@delete');

                        // upgrade an aggregate
                        Route::put('/{aggregate_id}/upgrade', 'AggregateController@upgrade')
                        ->middleware(['validateUpgrade', 'upgradeAggregate', 'noSubs']);
                    
                    });
                
                 });
            
            });

        
        });
    
    });

});
