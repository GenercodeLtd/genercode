<?php
namespace GenerCode\Middleware;

use GenerCode\Profile;
use Illuminate\Support\Facades\Auth;
use Closure;

class ProfileMiddleware {
   
   

    public function handle($request, Closure $next) {
        //Auth::check();
        $user = Auth::user();

        $app = app();
        //if (!$user) return $next($request);
        //$profile = $app->makeWith("factory", [$user->type]);
        if (!$user) {
            $profile = $app->make("factory")->create("public");
        } else {
            $profile = $app->make("factory")->create($user->type);
            $profile->id = $user->id;
        }
        $app->instance("profile", $profile);
        return $next($request);
    }
}