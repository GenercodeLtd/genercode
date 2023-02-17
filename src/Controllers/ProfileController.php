<?php
namespace GenerCodeOrm\Controllers;

use \GenerCodeOrm\Exceptions as Exceptions;
use \GenerCodeOrm\Permissions;
use \App\Models\User;

class ProfileController  {

    protected $profile;
    
    function __construct() {
        $this->profile = app()->get("profile");
    }
 
    function checkUser() {
        return $this->profile->toArr();
    }


    function create(Request $request, $type) {
        if (!Permissions::canAssume($name)) {
            throw new Exceptions\PtjException("Cannot create profile " . $name);
        }
        $user = new User();
        $user->password = Password::make($request->password);
        $user->type = $type;

        $user->save();
        return $user->id;
    }


    function userDetails($id) {
        return User::find($id);
    }


    function login(Request $request, $type) {

        if (!Permissions::canAssume($type)) {
            throw new Exceptions\PtjException("Cannot login to profile " . $type);
        }

        $auth = app()->get("auth");
        if ($auth->attempt(["email"=>$request->input("email"), "type"=>$type, "password"=>$request->input("password")])) {
            $user = $auth->user();
            $response->setContent(json_encode(["--id"=>$user->getAuthIdentifier()]));
            $request->session()->regenerate();
            return $response;
        } else {
            throw new Exceptions\PtjException("This username / password was not recognised");
        }
    }


    function logout($request, $response) {
        $auth = app()->get("auth");
        $auth->logout();
        $response->setContent(json_encode("success"));
        return $response;
    }


    public function updatePasswordRequest($params) {
        $params->fields = ["--id"];
        if (!isset($params->data["code"]) OR !isset($params->data["password"])) {
            throw new Exceptions\PtjException("Incorrect parameters");
        }
        $params->limit = 1;
        $repo = new \PressToJam\Repos\UserLogin($pdo, $params);
        $obj = $repo->get();
        if (!$obj) {
            throw new Exceptions\PtjException("This username was not recognised");
        }

        $nparams = new Params();
        $nparams->data = ["password"=>$params->data["password"], "id"=>$obj->{"--id"}];
        $model = new PressToJam\Models\UserLogin($pdo, $params);
        $model->update();
        return "success";
    }


    
    
    public function getResetPasswordRequest($username) {
        $field = new Cells\String();
        $params->data = ["username"=>$username];
        $params->limit = 1;
        $repo = ModelFactory::create("UserLogin");
        $repo->select(["--id"]);
        $repos->username = $username;
        $obj = $repo->get();
        if (!$obj) {
            throw new Exceptions\PtjException("This username was not recognised");
        }

        $params = new Params();
        $params->data = ["--whisper-id"=>$field->getRandom(75), "id"=>$obj->{"--id"}];
        $model = new \PressToJam\Models\UserLogin($pdo, $params);
        $model->update();
        return "success";
    }

    public function getDictionary() {
        $dict_root = configs("dictionary.root");
        return json_encode(file_get_contents($dict_root . "/" . $this->profile->name . ".json"));
    }

 
    public function getSitemap() {
        $map = $this->profile->getSitemap(app()->get("entity_factory"));
        return $this->trigger("site-map", "get", $map);
    }
}