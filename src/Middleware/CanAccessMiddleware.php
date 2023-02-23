<?php
namespace GenerCode\Middleware;

use GenerCode\Profile;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Contracts\Foundation\Application;

class CanAccessMiddleware {


    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
   
   

    public function handle($request, Closure $next) {
        //Auth::check();
        $url = explode("/", trim($request->url(), "/"));
        if (count($url) == 0) {
            abort(500, "URL can't be empty");
        }

        $model = $url[0];
        $profile = $this->app->get("profile");
        if (!$profile->hasPermission($model, $request->method)) {
         //   abort(403, "Unauthorized action");
        }
        return $next($request);
    }
}