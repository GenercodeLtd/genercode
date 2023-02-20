<?php

namespace GenerCode;


class QueryResource {

    protected $dictionary;
    protected $to;
    protected $fields;
    protected $order;
    protected $limit;
    protected $group;
    protected $data;

    function convertKeys($arr) {

    }

    function convertValues($arr) {

    }

    function buildParent($parent) {

    }


    function apply($request) {
        if ($request->has("__fields")) {
            $this->fields = $this->convertValues($request->__fields); 
        }

        if ($request->has("__order")) {
            $this->order = $this->convertKeys($request->__order);
        }

        if ($request->has("__to")) {
            $this->to = $this->buildParent($request->__to);
        }

        if ($request->has("__group")) {
            $this->group = $this->convertValues($request->__group);
        }
    }


    function getWith() {
        return [];
    }
}