<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardControllers\IndexController;
use App\Http\Controllers\DashboardControllers\AuthControllers\loginController;
use App\Http\Controllers\DashboardControllers\UserController;
use App\Http\Controllers\DashboardControllers\MaskingController;
use App\Http\Controllers\DashboardControllers\ResellerController;
use App\Http\Controllers\DashboardControllers\CustomerController;
use App\Http\Controllers\DashboardControllers\MessageController;
use App\Http\Controllers\DashboardControllers\TransactionController;






/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest')->group(function(){
    Route::get('/',[loginController::class, 'loginView'])->name('login');
    Route::post('login',[loginController::class, 'login'])->name('login.post');
});
Route::get('logout', [loginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function(){

    Route::prefix('admin')->name('admin.')->middleware('auth.admin')->group(function(){    
        Route::get('index', [IndexController::class, 'index'])->name('dashboard');
        Route::post('update/sms', [IndexController::class, 'updateAdminSms'])->name('update.sms');

        
        Route::prefix('masking')->name('masking.')->group(function(){
            Route::get('index', [MaskingController::class, 'index'] )->name('index');
            Route::post('store', [MaskingController::class, 'store'] )->name('store');
            Route::delete('destroy/{id}', [MaskingController::class, 'destroy'] )->name('destroy');
            Route::post('update/{id}', [MaskingController::class, 'update'] )->name('update');
        });

        Route::prefix('reseller')->name('reseller.')->group(function(){
            Route::get('index', [ResellerController::class, 'index'] )->name('index');
            Route::get('create', [ResellerController::class, 'create'] )->name('create');
            Route::post('store', [ResellerController::class, 'store'] )->name('store');
            Route::delete('destroy/{id}', [ResellerController::class, 'destroy'] )->name('destroy');
            Route::get('edit/{id}', [ResellerController::class, 'edit'] )->name('edit');
            Route::post('update/{id}', [ResellerController::class, 'update'] )->name('update');
            Route::get('{id}/customer', [ResellerController::class, 'resellerCustomer'] )->name('customer');
        });

    });
    
    Route::middleware('auth.customer')->group(function(){
        Route::prefix('customer')->name('customer.')->group(function(){
            Route::get('index', [CustomerController::class, 'index'] )->name('index');
            Route::get('create', [CustomerController::class, 'create'] )->name('create');
            Route::post('store', [CustomerController::class, 'store'] )->name('store');
            Route::delete('destroy/{id}', [CustomerController::class, 'destroy'] )->name('destroy');
            Route::get('edit/{id}', [CustomerController::class, 'edit'] )->name('edit');
            Route::post('update/{id}', [CustomerController::class, 'update'] )->name('update');
        });
    });

    Route::prefix('message')->name('message.')->group(function(){
        Route::get('index', [MessageController::class, 'index'] )->name('index');
        Route::get('create', [MessageController::class, 'create'] )->name('create');
        Route::post('store', [MessageController::class, 'store'] )->name('store');
        Route::delete('destroy/{id}', [MessageController::class, 'destroy'] )->name('destroy');

        Route::get('campaign', [MessageController::class, 'messageCampaign'] )->name('campaign');
        Route::get('campaign/{id}/file', [MessageController::class, 'campaignFileDownload'] )->name('campaign.file');
    });


    Route::prefix('api')->name('api.')->group(function(){
        Route::get('index', [IndexController::class, 'getapi'] )->name('index');
    });
    

    Route::prefix('transaction')->name('transaction.')->group(function(){
        Route::get('index', [TransactionController::class, 'index'] )->name('index');
    });

    Route::prefix('user')->name('user.')->middleware('auth.user')->group(function(){    
        Route::get('index', [IndexController::class, 'index'])->name('dashboard');
    });
});


// require __DIR__.'/auth.php';
 