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
    "download_excludes" => []
];