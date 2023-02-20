<?php
namespace GenerCode;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Controllers\Auth\AuthenticatedSessionController;
use Controllers\Auth\ConfirmablePasswordController;
use Controllers\Auth\EmailVerificationNotificationController;
use Controllers\Auth\EmailVerificationPromptController;
use Controllers\Auth\NewPasswordController;
use Controllers\Auth\PasswordController;
use Controllers\Auth\PasswordResetLinkController;
use Controllers\Auth\RegisteredUserController;
use Controllers\Auth\VerifyEmailController;

class GenerCodeServiceProvider extends ServiceProvider {


    public function registerRoutes() {
        $router = $this->app['router'];
        $router->group(function($router) {
            $router->get("/queue/status/{id}", 'Controllers\QueueController@status');
        });

        $router->group(function($router) {
            $router->get("/audit/{name}/{id}", 'Controllers\AuditControllers@get');
            $router->get("/audit/{name}/deleted/{id}", 'Controllers\AuditControllers@getDeleted');
            $router->get("/audit/{name}/since/{id}", 'Controllers\AuditControllers@getSince');
        });

    }


    public function registerAuthRoutes() {
        $router = $this->app['router'];
        $router->middleware('guest')->group(function ($router) {
            $router->post('/user/{type}/register', [RegisteredUserController::class, 'store']);
            $router->post('/user/login', [AuthenticatedSessionController::class, 'store']);
            $router->post('/user/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    //Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
           //     ->name('password.reset');
            $router->post('/user/reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
        });

        $router->middleware('auth')->group(function ($router) {
            //Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
              //  ->middleware(['signed', 'throttle:6,1'])
              //  ->name('verification.verify');

            $router->post('/user/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    //Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
      //          ->name('password.confirm');

            $router->post('/user/confirm-password', [ConfirmablePasswordController::class, 'store']);

            $router->put('/user/password', [PasswordController::class, 'update'])->name('password.update');

            $router->post('/user/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
        });
    }


    public function registerCommands() {
        $this->commands([
            Commands\DictionaryCommand::class,
            Commands\DownloadCommand::class,
            Commands\PublishCommand::class,
            Commands\UploadCommand::class,
            Commands\CdnCommand::class
        ]);
    }

    public function boot(\Illuminate\Routing\Router $router, \Illuminate\Contracts\Http\Kernel $kernel) {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        } else {
            JsonResource::withoutWrapping();
            $this->registerAuthRoutes();
            $this->registerRoutes();
            $kernel->pushMiddleware(\GenerCode\Middleware\ProfileMiddleware::class);
        }
    }
}