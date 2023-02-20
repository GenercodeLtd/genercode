<?php
namespace GenerCode;

class Permissions {

    function checkPerms($model, $method) {
        $perms = app()->get("profile")->perms;
        if (!isset($perms[$model]) OR !in_array($method, $perms[$model])) {
            return false;
        } else {
            return $perms[$model];
        }
    }


    function hasPost($model) {
        return ($this->checkPerms($model, "post")) ? true : false;
    }


    function hasPut($model) {
        return ($this->checkPerms($model, "put")) ? true : false;
    }

    function hasDelete($model) {
        return ($this->checkPerms($model, "delete")) ? true : false;
    }

    function hasGet($model) {
        return ($this->checkPerms($model, "get")) ? true : false;
    }

    function canGet($model) {
        $perms = $this->checkPerms($model, "get");
        if (!$perms) return false;
        if ($perms AND isset($perms["get_fields"])) {
            return $perms["get_fields"];
        } else {
            return true;
        }
    }

    function canPut($model) {
        $perms = $this->checkPerms($model, "put");
        if (!$perms) return false;
        if ($perms AND isset($perms["put_fields"])) {
            return $perms["put_fields"];
        } else {
            return true;
        }
    }
}

