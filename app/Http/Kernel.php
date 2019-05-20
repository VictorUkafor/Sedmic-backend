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
        'checkDiamond' => \App\Http\Middleware\CheckDiamond::class,
        'churchCreated' => \App\Http\Middleware\ChurchCreated::class,
        'churchNotCreated' => \App\Http\Middleware\ChurchNotCreated::class,
        'validateImage' => \App\Http\Middleware\ValidateImage::class,
        'imageExist' => \App\Http\Middleware\ImageExist::class,
        'signupSuccess' => \App\Http\Middleware\SignupSuccess::class,
        'memberSignup' => \App\Http\Middleware\ValidateMemberSignup::class,


        //jwt auth
        'jwt.auth' => \Tymon\JWTAuth\Middleware\GetUserFromToken::class,
        'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,
    ];
}
