<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test', function () {
    $logFilePath = storage_path('logs/laravel.log');

    // Check if the log file exists
    if (file_exists($logFilePath)) {
        dd(file_get_contents($logFilePath));
    } else {
        return 'not exist';
    }
});
