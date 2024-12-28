<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;

Route::post('/', [LoginController::class, 'index'])->name('auth.login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('login.logout');
Route::group(['middleware' => ['auth:sanctum', 'admin']], function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
});
