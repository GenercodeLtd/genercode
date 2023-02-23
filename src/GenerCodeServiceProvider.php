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
use GenerCode\Controllers\ProfileController;

class GenerCodeServiceProvider extends ServiceProvider {


  
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
            $router->pushMiddlewareToGroup('api', \GenerCode\Middleware\JsonMiddleware::class);
       //     $router->pushMiddlewareToGroup('api', \GenerCode\Middleware\ProfileMiddleware::class);
       //     $router->pushMiddlewareToGroup('api', \GenerCode\Middleware\CanAccessMiddleware::class);
        }
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
    }
}