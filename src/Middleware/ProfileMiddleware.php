<?php
namespace GenerCode\Middleware;

use GenerCode\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Foundation\Application;
use Closure;

class ProfileMiddleware {
   
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }


    public function handle(\Illuminate\Http\Request $request, Closure $next) {
        //Auth::check();
        $user = $request->user('api');

        //if (!$user) return $next($request);
        //$profile = $app->makeWith("factory", [$user->type]);
        if (!$user) {
            $profile = $this->app->make(\GenerCode\ProfileHandler::class)->create("public");
        } else {
            $profile = $this->app->make("factory")->create($user->type);
            $profile->id = $user->id;
        }
        $this->app->instance("profile", $profile);
        return $next($request);
    }
}