<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControllers\LoginController;


Route::name('admin.')->prefix('admin')->group(function(){
    
    Route::middleware('guest')->group(function(){
        Route::get('login',[LoginController::class, 'index'])->name('login');
    });
});