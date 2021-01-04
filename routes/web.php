<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardControllers\IndexController;
use App\Http\Controllers\DashboardControllers\AuthControllers\loginController;
use App\Http\Controllers\DashboardControllers\UserController;
use App\Http\Controllers\DashboardControllers\MaskingController;



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
    Route::get('{type?}/login',[loginController::class, 'loginView'])->name('login');
    Route::post('login',[loginController::class, 'login'])->name('login.post');
});
Route::post('logout', [loginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function(){

    Route::prefix('admin')->name('admin.')->middleware(['authtype'])->group(function(){    
        Route::get('index', [IndexController::class, 'index'])->name('dashboard');
        
        Route::prefix('masking')->name('masking.')->group(function(){
            Route::get('index', [MaskingController::class, 'index'] )->name('index');
        });
    });

    Route::prefix('user')->name('user.')->middleware(['authtype'])->group(function(){    
        Route::get('index', [IndexController::class, 'index'])->name('dashboard');
    });
});


// require __DIR__.'/auth.php';
 