<?php
use Illuminate\Support\Facades\Route;
use \GenerCode\Controllers\AuditController;
use \GenerCode\Controllers\ProfileController;
use \GenerCode\Controllers\QueueController;

Route::get("/queue/status/{id}", [QueueController::class, 'status']);

Route::group(['middleware'=>'api'], function() {
    Route::get("/audit/{name}/{id}", [AuditController::class, 'get']);
    Route::get("/audit/{name}/deleted/{id}", [AuditController::class, 'getDeleted']);
    Route::get("/audit/{name}/since/{id}", [AuditController::class, 'getSince']);
});


Route::get('/user/check-user', [ProfileController::class, 'checkUser'])->middleware("api");
Route::get('/user/dictionary', [ProfileController::class, 'getDictionary'])->middleware("api");
Route::get('/user/site-map', [ProfileController::class, 'getSitemap'])->middleware("api");

