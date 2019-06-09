<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'validateActivate' => \App\Http\Middleware\ValidateActivate::class,
        'validateConfirm' => \App\Http\Middleware\ValidateConfirm::class,
        'validateLogin' => \App\Http\Middleware\ValidateLogin::class,
        'findUser' => \App\Http\Middleware\FindUser::class,
        'myAdmins' => \App\Http\Middleware\MyAdmins::class,
        'validateUsername' => \App\Http\Middleware\ValidateUsername::class,
        'validatePassword' => \App\Http\Middleware\ValidatePassword::class,
        'validateChurch' => \App\Http\Middleware\ValidateChurchCreate::class,
        'validateUnit' => \App\Http\Middleware\ValidateUnit::class,
        'validateAggregate' => \App\Http\Middleware\ValidateAggregate::class,
        'validateUpgrade' => \App\Http\Middleware\ValidateAggregateUpgrade::class,
        'validateIncomeType' => \App\Http\Middleware\ValidateIncomeType::class,
        'incomeTypeName' => \App\Http\Middleware\IncomeTypeName::class,
        'incomeTypeExist' => \App\Http\Middleware\IncomeTypeExist::class,
        'incomeExist' => \App\Http\Middleware\IncomeExist::class,
        'updateIncomeType' => \App\Http\Middleware\UpdateIncomeType::class,
        'upgradeAggregate' => \App\Http\Middleware\UpgradeAggregate::class,
        'checkDiamond' => \App\Http\Middleware\CheckDiamond::class,
        'diamondOrGold' => \App\Http\Middleware\DiamondOrGoldUser::class,
        'churchCreated' => \App\Http\Middleware\ChurchCreated::class,
        'churchNotCreated' => \App\Http\Middleware\ChurchNotCreated::class,
        'validateImage' => \App\Http\Middleware\ValidateImage::class,
        'validateIncome' => \App\Http\Middleware\ValidateIncome::class,
        'imageNotRequired' => \App\Http\Middleware\ValidateImageNotRequired::class,
        'imageExist' => \App\Http\Middleware\ImageExist::class,
        'signupSuccess' => \App\Http\Middleware\SignupSuccess::class,
        'memberSignup' => \App\Http\Middleware\ValidateMemberSignup::class,
        'membersExist' => \App\Http\Middleware\MembersExist::class,
        'unitsExist' => \App\Http\Middleware\UnitsExist::class,
        'firstTimerExist' => \App\Http\Middleware\FirstTimerExist::class,
        'slipExist' => \App\Http\Middleware\SlipExist::class,
        'aggregatesExist' => \App\Http\Middleware\AggregatesExist::class,
        'unitExist' => \App\Http\Middleware\UnitExist::class,
        'aggregateExist' => \App\Http\Middleware\AggregateExist::class,
        'memberExist' => \App\Http\Middleware\MemberExist::class,
        'canUpdate' => \App\Http\Middleware\CanUpdateMember::class,
        'canDelete' => \App\Http\Middleware\CanDeleteMember::class,
        'uniqueName' => \App\Http\Middleware\UniqueUnitName::class,
        'uniqueAggName' => \App\Http\Middleware\UniqueAggregateName::class,
        'maxHandlers' => \App\Http\Middleware\MaxHandlers::class,
        'validateHandlers' => \App\Http\Middleware\ValidateHandlers::class,
        'validatePosition' => \App\Http\Middleware\ValidatePosition::class,
        'validateProgramme' => \App\Http\Middleware\ValidateProgramme::class,
        'editProgramme' => \App\Http\Middleware\EditProgramme::class,
        'programmeExist' => \App\Http\Middleware\ProgrammeExist::class,
        'programmeHandlers' => \App\Http\Middleware\ProgrammeHandlers::class,
        'programmeCreator' => \App\Http\Middleware\ProgrammeCreator::class,
        'validateFirstTimer' => \App\Http\Middleware\ValidateFirstTimer::class,
        'validateSlip' => \App\Http\Middleware\ValidateSlip::class,
        'unitPosition' => \App\Http\Middleware\UnitPositionExist::class,
        'aggregatePosition' => \App\Http\Middleware\AggregatePositionExist::class,
        'removeUnitPosition' => \App\Http\Middleware\RemoveUnitPosition::class,
        'removeAggregatePosition' => \App\Http\Middleware\RemoveAggregatePosition::class,
        'addUnitHandlers' => \App\Http\Middleware\AddUnitHandlers::class,
        'addAggregateHandlers' => \App\Http\Middleware\AddAggregateHandlers::class,
        'unitHandlers' => \App\Http\Middleware\UnitHandlers::class,
        'aggregateHandlers' => \App\Http\Middleware\AggregateHandlers::class,
        'noSubs' => \App\Http\Middleware\NoSubs::class,
        'subNotYours' => \App\Http\Middleware\SubNotYours::class,
        'isMember' => \App\Http\Middleware\IsMember::class,
        'notMember' => \App\Http\Middleware\NotMember::class,
        'freeSub' => \App\Http\Middleware\FreeSub::class,
        'unitMembers' => \App\Http\Middleware\UnitMembers::class,
        'aggregateMembers' => \App\Http\Middleware\AggregateMembers::class,
        'addHandlers' => \App\Http\Middleware\AddHandlers::class,
        'programmeHandlerExist' => \App\Http\Middleware\ProgrammeHandlerExist::class,

        //jwt auth
        'jwt.auth' => \Tymon\JWTAuth\Middleware\GetUserFromToken::class,
        'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,
    ];
}
