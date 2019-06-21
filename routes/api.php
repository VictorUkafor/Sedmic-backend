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
        
        // sign up account
        Route::post('/signup', 'UserController@signup')
        ->middleware('validateSignup');
            
        // account confirmation via email
        Route::post('confirm-email/{token}', 'UserController@signupConfirmViaEmail')
        ->middleware('confirmViaEmail');

        // account confirmation via sms
        Route::post('confirm-sms', 'UserController@signupConfirmViaSMS')
        ->middleware('confirmViaSMS');

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
            ->middleware('validateSignup');
                
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
        Route::prefix('profile')->group(function () {
            
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


            // income types routes
            Route::prefix('income-types')->group(function () {
                
                // create income type
                Route::post('/', 'IncomeTypeController@create')
                ->middleware(['diamondOrGold', 'validateIncomeType']);

                // view all income types
                Route::get('/', 'IncomeTypeController@viewAll');
            
                // all incomes 
                Route::get('/all/incomes', 'IncomeController@viewAll') 
                ->middleware('diamondOrGold'); 

                // all incomes created 
                Route::get('/my/incomes', 'IncomeController@createdIncomes'); 
                    
                // routes for a single income type
                Route::group([
                    'prefix' => '{incomeTypeId}',
                    'middleware' => 'incomeTypeExist'
                ], function () {
                    
                    // get income type
                    Route::get('/', 'IncomeTypeController@show'); 

                    //
                    Route::middleware('diamondOrGold')->group(function () {
                    
                        // all type incomes 
                        Route::get('/income', 'IncomeTypeController@typeIncomes'); 
        
                        // update income type
                        Route::put('/', 'IncomeTypeController@update')
                        ->middleware('incomeTypeName');

                        // delete income type
                        Route::delete('/', 'IncomeTypeController@delete');                        
                    
                    });
                        

                    // income routes
                    Route::prefix('incomes')->group(function () {
                        
                        // create income 
                        Route::post('/', 'IncomeController@create')
                        ->middleware('validateIncome'); 

                        // all incomes 
                        Route::get('/', 'IncomeTypeController@typeIncomes') 
                        ->middleware('diamondOrGold'); 

                        // routes for single income
                        Route::group([
                            'prefix' => '{incomeId}',
                            'middleware' => 'incomeExist',
                        ], function () {                            
                            
                            // get income
                            Route::get('/', 'IncomeController@show')
                            ->middleware('incomeCreatorDiamondGold'); 

                            // update income
                            Route::put('/', 'IncomeController@update')
                            ->middleware('incomeCreator'); 

                            // delete income
                            Route::delete('/', 'IncomeController@delete')
                            ->middleware('incomeCreator'); 
                        
                        });
                    
                    });

                });                 

            });


            // first timer routes
            Route::prefix('firsttimers')->group(function () {

                // create first timer 
                Route::post('/', 'FirstTimerController@create')
                ->middleware('validateFirstTimer'); 

                // all first timers 
                Route::get('/', 'FirstTimerController@viewAll'); 


                // routes for single first timer
                Route::group([
                    'prefix' => '{firstTimerId}',
                    'middleware' => 'firstTimerExist',
                ], function () {
                    
                    // update first timer
                    Route::put('/', 'FirstTimerController@update')
                    ->middleware('imageNotRequired'); 

                    // update first timer
                    Route::get('/', 'FirstTimerController@show');

                    // delete first timer
                    Route::delete('/', 'FirstTimerController@delete');

                });

            });


            // slip routes
            Route::prefix('slips')->group(function () {

                // create slip 
                Route::post('/', 'SlipController@create')
                ->middleware('validateSlip'); 

                // all slips 
                Route::get('/', 'SlipController@viewAll'); 


                // routes for single slip
                Route::group([
                    'prefix' => '{slipId}',
                    'middleware' => 'slipExist',
                ], function () {
                    
                    // update slip
                    Route::put('/', 'SlipController@update')
                    ->middleware('imageNotRequired'); 

                    // update slip
                    Route::get('/', 'SlipController@show'); 

                    // delete slip
                    Route::delete('/', 'SlipController@delete');

                });

            });


            // programme routes
            Route::prefix('programmes')->group(function () {                    
                
                // create programme
                Route::post('/', 'ProgrammeController@create')
                ->middleware('validateProgramme');
 
                // view all programmes
                Route::get('/', 'ProgrammeController@viewAll'); 
                
                // routes for single programme
                Route::group([
                    'prefix' => '{programmeId}',
                    'middleware' => 'programmeExist',
                ], function () {
                    
                    // view a programme
                    Route::get('/', 'ProgrammeController@show');

                    // routes for programme creator
                    Route::middleware('programmeCreator')->group(function () {                        
                        
                        // update programme
                        Route::put('/', 'ProgrammeController@update')
                        ->middleware('editProgramme');

                        // cancel programme
                        Route::delete('/', 'ProgrammeController@cancel');

                        // suspend programme
                        Route::post('/suspend', 'ProgrammeController@suspend');

                        // change type programme
                        Route::post('/type', 'ProgrammeController@changeType')
                        ->middleware('programmeType');
                        

                        // routes for programme services
                        Route::prefix('services')->group(function () {

                            Route::middleware('gappingService')->group(function () {
                                
                                // create service
                                Route::post('/', 'ServiceController@create')
                                ->middleware('validateService');

                                // update service
                                Route::put('/{serviceId}', 'ServiceController@update')
                                ->middleware(['serviceExist', 'validateUpdateService']);

                                // view a single service
                                Route::delete('/{serviceId}', 'ServiceController@delete')
                                ->middleware('serviceExist');
                            
                            });

                            Route::middleware('fixServiceGap')->group(function () {
                                
                                // show service gaps
                                Route::get('/gaps', 'ServiceController@gaps');

                                // show single service gaps
                                Route::get('/gaps/{gapId}', 'ServiceController@gap');

                                // fix gaps
                                Route::post('/restore', 'ServiceController@restoreServices');

                                // squash gaps
                                Route::post('/squash', 'ServiceController@squash');
                            
                            });

                        });

                        // program handler routes
                        Route::prefix('handlers')->group(function () {
                            
                            // get programme handlers
                            Route::get('/', 'ProgrammeController@getHandlers');

                            // add programme handlers
                            Route::post('/', 'ProgrammeController@addHandlers')
                            ->middleware('addHandlers');

                            // remove programme handler
                            Route::delete('/{userId}', 'ProgrammeController@removeHandler')
                            ->middleware('programmeHandlerExist');

                        });

                    });

                    // routes for programme handlers
                    Route::middleware('programmeHandlers')->group(function () {

                        // get programme invitees
                        Route::post('/invitees', 'ProgrammeController@addInvitees')
                        ->middleware('validateAddInvitees');
                        
                        // get programme invitees
                        Route::get('/invitees', 'AttendanceController@invitees');

                        // search programme invitees
                        Route::post('/search', 'AttendanceController@search')
                        ->middleware('validateSearch');

                        // get programme attendees
                        Route::get('/attendees', 'AttendanceController@attendees');

                        // get programme absentees
                        Route::get('/absentees', 'AttendanceController@absentees');
                            
                        // get programme attendee signs
                        Route::get('/signs', 'AttendanceController@signs');

                        Route::group([
                            'prefix' => 'services',
                            'middleware' => 'gappingService',
                        ], function () {
                            
                            // view all services
                            Route::get('/', 'ServiceController@viewAll');

                            // view a single service
                            Route::get('/{serviceId}', 'ServiceController@show')
                            ->middleware('serviceExist');                              

                        });

                        // routes for single invitee
                        Route::group([
                            'prefix' => 'invitees/{inviteeId}',
                            'middleware' => 'inviteeExist',
                        ], function () {
                            
                            // remove programme invitee
                            Route::delete('/', 'AttendanceController@removeInvitee')
                            ->middleware('programmeCreator');

                           // add programme attendee and signs
                           Route::post('/signs', 'AttendanceController@addSign')
                           ->middleware('validateValue');                           
                           
                           // show attendee sign
                           Route::get('/', 'AttendanceController@invitee');
                            
                           // get programme attendee signs
                           Route::get('/signs', 'AttendanceController@attendeeSigns');
                            
                            // routes for single attendee signing
                            Route::group([
                                'prefix' => 'signs/{signId}',
                                'middleware' => 'signExist',
                            ], function () {
                                
                                // get single sign
                                Route::get('/', 'AttendanceController@getSign');

                                // update single sign
                                Route::put('/', 'AttendanceController@editSign');

                                // remove single sign
                                Route::delete('/', 'AttendanceController@removeSign');

                            });

                        });

                    });

                });

            });
        
        });
    
    });

});
