<?php
namespace GenerCode\Controllers;

use \GenerCodeOrm\Exceptions as Exceptions;
use \GenerCodeOrm\Permissions;
use \App\Models\User;
use Illuminate\Support\Facades\Config; 

class ProfileController  {

    protected $profile;
    
    function __construct() {
        $this->profile = app()->get("profile");
    }
 
    function checkUser() {
        return $this->profile->toArr();
    }


    public function getDictionary() {
        $dict_root = app()["config"]->get("genercode.dictionary");
        return file_get_contents(base_path() . "/" . $dict_root . "/" . $this->profile->name . ".json");
    }

 
    public function getSitemap() {
        $map = $this->profile->getSitemap(app()->get("entity_factory"));
        return $this->trigger("site-map", "get", $map);
    }
}