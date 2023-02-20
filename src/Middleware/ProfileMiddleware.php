<?php
namespace GenerCode\Middleware;

use GenerCode\Profile;
use Closure;

class ProfileMiddleware {
   
   

    public function handle($request, Closure $next) {
        $app = app();
        $auth = $app->get("auth");
        $user = $auth->user();

        //if (!$user) return $next($request);
        //$profile = $app->makeWith("factory", [$user->type]);
        $profile = $app->make("factory")->create("admin");
        //$profile->id = $user->id;
        $profile->id = 1;
        $app->instance("profile", $profile);
        return $next($request);
    }
}