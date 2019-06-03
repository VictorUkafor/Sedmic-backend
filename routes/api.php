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
    'namespace' => 'API',
    'prefix' => 'v1'
], function () {

    // routes that don't need authentication
    Route::prefix('auth')->group(function () {

        // signup routes
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

    // routes requring authentication
    Route::middleware('jwt.auth')->group(function () {

        // user admins routes
        Route::group([
            'prefix' => 'admins',
            'middleware' => 'checkDiamond'
        ], function () {
            
            // create admin
            Route::post('/', 'UserController@createAdmin')
            ->middleware('validateConfirm');
                
            Route::group([
                'prefix' => '{userId}',
                'middleware' => ['findUser', 'myAdmins']
            ], function () {

                Route::middleware('signupSuccess')->group(function () {
                
                    // activate admin
                    Route::post('/activate', 'UserController@activateAdmin');

                    // block admin
                    Route::post('/block', 'UserController@blockAdmin');
                
                });

                // remove admin
                Route::delete('/remove', 'UserController@removeAdmin');

                // change admin right
                Route::post('/right', 'UserController@changeRight');
            
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
                
                // routes for single member
                Route::group([
                    'prefix' => '{member_id}',
                    'middleware' => 'memberExist'
                ], function () {
                    
                    // view member
                    Route::get('/', 'MemberController@show');

                    // update member
                    Route::put('/', 'MemberController@update')
                    ->middleware('canUpdate');

                    // delete member
                    Route::delete('/', 'MemberController@delete')
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
                
                // routes for single unit
                Route::group([
                    'prefix' => '{unit_id}',
                    'middleware' => 'unitExist'
                ], function () {
                    
                    Route::middleware('unitHandlers')->group(function () {
                        
                        // view a single unit
                        Route::get('/', 'UnitController@show');
                            
                        // update a single unit
                        Route::put('/', 'UnitController@update')
                        ->middleware(['imageNotRequired', 'uniqueName']);
                            
                        // routes for unit positions
                        Route::group([
                            'prefix' => 'positions',
                            'middleware' => 'unitMembers',                          
                        ], function () {

                            // get all unit positions
                            Route::get('/', 'ExecutiveController@unitExcos');
                            
                            // add unit position
                            Route::post('/', 'ExecutiveController@addUnitExco')
                            ->middleware(['validatePosition', 'unitPosition']);

                            // remove unit position
                            Route::delete('/{positionId}', 'ExecutiveController@removeUnitExco')
                            ->middleware('removeUnitPosition');
                        
                        });
                        
                        // routes for unit member 
                        Route::group([
                            'prefix' => '/members/{member_id}',
                            'middleware' => 'memberExist'
                        ], function () {
                            
                            // add member to unit
                            Route::post('/', 'UnitMemberController@addMember')
                            ->middleware('isMember');

                            // remove member from unit
                            Route::delete('/', 'UnitMemberController@removeMember')
                            ->middleware('notMember');
                        
                        });
                    
                    });
                    
                    
                    Route::middleware('diamondOrGold')->group(function () {
                        
                        // adds an handler
                        Route::post('/add-handlers', 'UnitController@addHandlers')
                        ->middleware('addUnitHandlers');

                        // removes an handler
                        Route::post('/remove-handler/{handler}', 
                        'UnitController@removeHandler');

                        // deletes a unit
                        Route::delete('/', 'UnitController@delete');
                    
                    });
                
                });
            
            });


            // aggregates
            Route::prefix('aggregates')->group(function () {
                
                // create an aggregate
                Route::post('/', 'AggregateController@create')
                ->middleware(['diamondOrGold', 'validateAggregate', 
                'uniqueAggName', 'maxHandlers', 'noSubs', 'validateHandlers']);

                // view all aggregates
                Route::get('/', 'AggregateController@viewAll')
                ->middleware('aggregatesExist');
                
                
                Route::group([
                    'prefix' => '{aggregate_id}',
                    'middleware' => 'aggregateExist'
                ], function () {
                    
                    Route::middleware('aggregateHandlers')->group(function () {
                        
                        // view a single aggregate
                        Route::get('/', 'AggregateController@show');

                        // update a single aggregate
                        Route::put('/', 'AggregateController@update')
                        ->middleware(['imageNotRequired', 'uniqueAggName']);


                        // routes for aggregate positions
                        Route::group([
                            'prefix' => 'positions',
                            'middleware' => 'aggregateMembers',                          
                        ], function () {

                            // get all aggregate positions
                            Route::get('/', 'ExecutiveController@aggregateExcos');
                            
                            // add aggregate position
                            Route::post('/', 'ExecutiveController@addAggregateExco')
                            ->middleware(['validatePosition', 'aggregatePosition']);

                            // remove aggregate position
                            Route::delete('/{positionId}', 'ExecutiveController@removeAggregateExco')
                            ->middleware('removeAggregatePosition');
                        
                        });


                        Route::prefix('/subs/{subId}')->group(function () {
                            
                            // add sub to aggregate
                            Route::post('/', 'SubController@addSub')
                            ->middleware('freeSub');

                            // remove sub to aggregate
                            Route::delete('/', 'SubController@removeSub')
                           ->middleware('subNotYours');

                        });
                    
                    });
                    
                    
                    Route::middleware('diamondOrGold')->group(function () {
                        
                        // adds an handler
                        Route::post('/add-handlers', 'AggregateController@addHandlers')
                        ->middleware('addAggregateHandlers');

                        // removes an handler
                        Route::post('/remove-handler/{handler}', 
                        'AggregateController@removeHandler');

                        // deletes a unit
                        Route::delete('/', 'AggregateController@delete');

                        // upgrade an aggregate
                        Route::put('/upgrade', 'AggregateController@upgrade')
                        ->middleware(['validateUpgrade', 'upgradeAggregate', 'noSubs']);
                    
                    });
                
                 });
            
            });

        
        });
    
    });

});
