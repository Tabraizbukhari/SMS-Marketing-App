<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardControllers\IndexController;
use App\Http\Controllers\DashboardControllers\AuthControllers\loginController;
use App\Http\Controllers\DashboardControllers\UserController;
use App\Http\Controllers\DashboardControllers\MaskingController;
use App\Http\Controllers\DashboardControllers\CustomerController;
use App\Http\Controllers\DashboardControllers\MessageController;
use App\Http\Controllers\DashboardControllers\TransactionController;
use App\Http\Controllers\DashboardControllers\ContactController;







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



Route::name('user.')->group(function ()
{

    Route::middleware('guest')->group(function(){
        Route::get('/',[loginController::class, 'loginView'])->name('login');
        Route::post('login',[loginController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth')->group(function ()
    {
        Route::get('dashboard', [IndexController::class, 'index'])->name('dashboard');
        Route::get('logout', [IndexController::class, 'destroy'])->name('logout');
        

        Route::get('contacts', [ContactController::class, 'index'])->name('contacts');
        Route::get('contacts/create', [ContactController::class, 'create'])->name('contacts.create');
        Route::post('contacts/store', [ContactController::class, 'store'])->name('contacts.store');
        Route::get('contacts/{id}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
        Route::post('contacts/{id}/update', [ContactController::class, 'update'])->name('contacts.update');
        Route::delete('contacts/destroy/{id}', [ContactController::class, 'destroy'] )->name('contacts.destroy');


        Route::prefix('customer')->name('customer.')->group(function(){
            Route::get('index/{id?}', [CustomerController::class, 'index'] )->name('index');
            Route::get('create/{type?}', [CustomerController::class, 'create'] )->name('create');
            Route::post('store/{type}', [CustomerController::class, 'store'] )->name('store');
            Route::delete('destroy/{id}', [CustomerController::class, 'destroy'] )->name('destroy');
            Route::get('edit/{id}', [CustomerController::class, 'edit'] )->name('edit');
            Route::post('update/{id}', [CustomerController::class, 'update'] )->name('update');
            Route::post('amount/{id}', [CustomerController::class, 'addCustomerAmount'])->name('amount.add');

            Route::get('details/{id}', [CustomerController::class, 'customerDetails'])->name('details');

        });


        Route::prefix('api')->name('api.')->group(function(){
            Route::get('index', [IndexController::class, 'getapi'] )->name('index');
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

        Route::prefix('transaction')->name('transaction.')->group(function(){
            Route::get('index', [TransactionController::class, 'index'] )->name('index');
            Route::post('add/amount/{id}', [TransactionController::class, 'addAmount'] )->name('amount.post');

        });
    
    Route::post('upload/logo',[IndexController::class, 'updateLogo'])->name('logo.update');

    });    

});

require __DIR__.'/admin.php';

// require __DIR__.'/auth.php';
 