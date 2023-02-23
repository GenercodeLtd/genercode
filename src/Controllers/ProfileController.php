<?php
namespace GenerCode\Controllers;

use \GenerCode\Exceptions as Exceptions;
use \App\Models\User;
use Illuminate\Support\Facades\Config; 
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;

class ProfileController  {

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
 
    function checkUser(Request $request) {
        $user = $request->user();
        if (!$user) {
            return [
                "name" => "public"
            ];
        } else {
            return [
                "name" => $user->type,
                "id" => $user->id
            ];
        }
    }


    public function getDictionary(Request $request) {
        $user = $request->user();
        $type = (!$user) ? "public" : $user->type; 
        return file_get_contents(app_path() . "/Dictionary/" . $type . ".json");
    }

 
    public function getSitemap(Request $request) {
        $user = $request->user();
        $handler = $this->app->make(\GenerCode\ProfileHandler::class);
        if (!$user) {
            $profile = $handler->create("public");
        } else {
            $profile = $handler->create($user->type);
            $profile->id = $user->id;
        }
        $map = $profile->getSitemap(app()->get("entity_factory"));
        return $map;
        return $this->trigger("site-map", "get", $map);
    }

}