<?php

namespace GenerCode;

class Validation {

    protected $rules = [];

    protected function addRule($cell, $additional_rules = []) {
        $rules = $cell->asRules();
        $rules = array_merge($rules, $additional_rules);
        if (count($rules) > 0) $this->rules[$cell->alias] = implode("|", $rules);
    }

    protected function validate($data) {
        app()->get("validator")->validate($data, $this->rules);
    }

}