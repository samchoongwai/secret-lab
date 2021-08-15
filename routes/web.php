<?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\TransactionController;

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


    Route::get('/', function ()
    {
        return view('welcome');
    });

    Route::get('/object/get_all_records', [App\Http\Controllers\TransactionController::class, 'list']);
    Route::get('/object/{key}', [App\Http\Controllers\TransactionController::class, 'find']);
    Route::post('/object', [App\Http\Controllers\TransactionController::class, 'create']);
