<?php
namespace GenerCode;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use GenerCode\Controllers\Auth\AuthenticatedSessionController;
use GenerCode\Controllers\Auth\ConfirmablePasswordController;
use GenerCode\Controllers\Auth\EmailVerificationNotificationController;
use GenerCode\Controllers\Auth\EmailVerificationPromptController;
use GenerCode\Controllers\Auth\NewPasswordController;
use GenerCode\Controllers\Auth\PasswordController;
use GenerCode\Controllers\Auth\PasswordResetLinkController;
use GenerCode\Controllers\Auth\RegisteredUserController;
use GenerCode\Controllers\Auth\VerifyEmailController;
use GenerCode\Controllers\Auth\ProfileController;

class GenerCodeServiceProvider extends ServiceProvider {


    public function registerRoutes() {
        $router = $this->app['router'];
        $router->group(['middleware'=>'auth'], function($router) {
            $router->get("/queue/status/{id}", 'Controllers\QueueController@status');
        });

        $router->group(['middleware'=>'auth'], function($router) {
            $router->get("/audit/{name}/{id}", 'Controllers\AuditControllers@get');
            $router->get("/audit/{name}/deleted/{id}", 'Controllers\AuditControllers@getDeleted');
            $router->get("/audit/{name}/since/{id}", 'Controllers\AuditControllers@getSince');
        });

    }


    public function registerAuthRoutes() {
        $router = $this->app['router'];
        $router->group(['middleware'=>'api'], function ($router) {
            $router->post('/user/{type}/register', [RegisteredUserController::class, 'store']);
            $router->post('/user/login', [AuthenticatedSessionController::class, 'store']);
            $router->post('/user/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');
            $router->post('/user/reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
          
        });

        $router->group(['middleware'=>['api']], function ($router) {
            //Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
              //  ->middleware(['signed', 'throttle:6,1'])
              //  ->name('verification.verify');
              $router->get('/user/check-user', [ProfileController::class, 'show']);
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

    public function publishConfigs() {
        $this->publishes([
            __DIR__.'/config/genercode.php' => config_path('genercode.php'),
        ]);
    }

    public function boot(\Illuminate\Routing\Router $router, \Illuminate\Contracts\Http\Kernel $kernel) {
        
        $this->publishConfigs();

        
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        } else {
            JsonResource::withoutWrapping();
            $this->registerAuthRoutes();
            $this->registerRoutes();
            $router->pushMiddlewareToGroup('api', \GenerCode\Middleware\JsonMiddleware::class);
            $kernel->pushMiddleware(\GenerCode\Middleware\ProfileMiddleware::class);
        }
    }
}