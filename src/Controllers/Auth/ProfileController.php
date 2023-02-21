<?php
namespace GenerCode\Controllers\Auth;
use Illuminate\Http\Request;

class ProfileController {

    function __construct() {
      
    }

  
    function show(Request $request) {
        return $request->user;
    }



}