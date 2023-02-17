<?php
namespace GenerCode;
use Illuminate\Support\ServiceProvider;

class GenerCodeServiceProvider extends ServiceProvider {


    public function registerRoutes() {
        $router = $this->app['router'];
        $router->group(['prefix'=>'/queue'], function($router) {
            $router->get("/status/{id}", 'Controllers\QueueController@status');
        });

        $router->group(['prefix'=> "/audit"], function($router) {
            $router->get("/{name}/{id}", 'Controllers\AuditControllers@get');
            $router->get("/{name}/deleted/{id}", 'Controllers\AuditControllers@getDeleted');
            $router->get("/{name}/since/{id}", 'Controllers\AuditControllers@getSince');
        });

        $router->group(['prefix' => "/user"], function($router) {
            $router->post("/{type}/login", 'Controllers\ProfileController@login');
            $router->post("/{type}/logout", 'Controllers\ProfileController@logout');
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

    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        } else {
            $this->registerRoutes();
        }
    }
}