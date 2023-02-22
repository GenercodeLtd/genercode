<?php
return [
    //login details to genercode site
    "username" => env("GC_USERNAME"),
    "password" => env("GC_PASSWORD"),

    //project id taken from genercode
    "project_id" => env("GC_PROJECT_ID"),

    //download directory
    "download_dir" => env("GC_DOWNLOAD_DIR", dirname(__DIR__)),

    //add any directories or files to exclude from the download / upload process
    "download_excludes" => [],

    //link to node_modules folder for compiling webpack
    "node_modules" => env("GC_NODE_MODULES", dirname(__DIR__) . "/node_modules"),

    //aws cloudfront distribution id for running invalidations
    "cloudfront_distribution_id" => env("GC_CFDIST_ID"),

    "dictionary" => env("GC_DICTIONARY_ROOT"),

    "api_type" => "slim" //slim or laravel
];