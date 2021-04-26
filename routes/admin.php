<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControllers\LoginController;
use App\Http\Controllers\AdminControllers\DashboardController;
use App\Http\Controllers\AdminControllers\MaskingController;
use App\Http\Controllers\AdminControllers\ContactController;
use App\Http\Controllers\AdminControllers\MessageController;
use App\Http\Controllers\AdminControllers\UserController;





Route::name('admin.')->prefix('admin')->group(function(){
    
    Route::middleware('guest')->group(function(){
        Route::get('login',[LoginController::class, 'index'])->name('login');
        Route::post('login',[LoginController::class, 'login'])->name('login.post');
    });
    
    Route::middleware('auth:admin')->group(function ()
    {
        Route::get('logout',[LoginController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('update/sms', [DashboardController::class, 'updateAdminSms'])->name('update.sms');
        
        Route::prefix('masking')->name('masking.')->group(function(){
            Route::get('index', [MaskingController::class, 'index'] )->name('index');
            Route::post('store', [MaskingController::class, 'store'] )->name('store');
            Route::delete('destroy/{id}', [MaskingController::class, 'destroy'] )->name('destroy');
            Route::post('update/{id}', [MaskingController::class, 'update'] )->name('update');
        });


        Route::prefix('contacts')->name('contact.')->group(function (){
            Route::get('index', [ContactController::class, 'index'])->name('index');
            Route::get('create', [ContactController::class, 'create'])->name('create');
            Route::post('store', [ContactController::class, 'store'])->name('store');
            Route::get('{id}/edit', [ContactController::class, 'edit'])->name('edit');
            Route::post('{id}/update', [ContactController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [ContactController::class, 'destroy'] )->name('destroy');
        });

        Route::prefix('user')->name('user.')->group(function(){
            Route::get('reseller', [UserController::class, 'resellerIndex'] )->name('reseller');
            Route::get('customer', [UserController::class, 'customerIndex'] )->name('customer');
            Route::get('create/{type?}', [UserController::class, 'create'] )->name('create');
            Route::post('store/{type}', [UserController::class, 'store'] )->name('store');
            Route::delete('destroy/{id}', [UserController::class, 'destroy'] )->name('destroy');
            Route::get('edit/{id}', [UserController::class, 'edit'] )->name('edit');
            Route::post('update/{id}', [UserController::class, 'update'] )->name('update');
            Route::get('{id}/customer', [UserController::class, 'ResellerCustomer'] )->name('customer');

            Route::get('user/{id?}', [UserController::class, 'userBlocked'] )->name('blocked');
        });


        Route::prefix('message')->name('message.')->group(function(){
            Route::get('index', [MessageController::class, 'index'] )->name('index');
            Route::get('create', [MessageController::class, 'create'] )->name('create');
            Route::post('store', [MessageController::class, 'store'] )->name('store');
            Route::delete('destroy/{id}', [MessageController::class, 'destroy'] )->name('destroy');
            Route::post('exportExcel', [MessageController::class, 'exportExcel'] )->name('data.export');    
            Route::get('campaign', [MessageController::class, 'messageCampaign'] )->name('campaign');
            Route::get('campaign/{id}/file', [MessageController::class, 'campaignFileDownload'] )->name('campaign.file');
        });
        

        Route::prefix('api')->name('api.')->group(function(){
            Route::get('index', [DashboardController::class, 'getapi'] )->name('index');
        });


        Route::prefix('transaction')->name('transaction.')->group(function(){
            Route::get('index', [DashboardController::class, 'transaction'] )->name('index');
            Route::post('add/amount/{id}', [DashboardController::class, 'addAmount'] )->name('amount.post');
        });

    });
});