<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardControllers\IndexController;
use App\Http\Controllers\DashboardControllers\AuthControllers\loginController;
use App\Http\Controllers\DashboardControllers\UserController;
use App\Http\Controllers\DashboardControllers\MaskingController;
use App\Http\Controllers\DashboardControllers\ResellerController;
use App\Http\Controllers\DashboardControllers\CustomerController;




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
        });

        Route::prefix('customer')->name('customer.')->group(function(){
            Route::get('index', [CustomerController::class, 'index'] )->name('index');
            Route::get('create', [CustomerController::class, 'create'] )->name('create');
            Route::post('store', [CustomerController::class, 'store'] )->name('store');
            Route::delete('destroy/{id}', [CustomerController::class, 'destroy'] )->name('destroy');
        });

    });

    Route::prefix('user')->name('user.')->middleware('auth.user')->group(function(){    
        Route::get('index', [IndexController::class, 'index'])->name('dashboard');
    });
});


// require __DIR__.'/auth.php';
 