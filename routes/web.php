<?php

use Illuminate\Support\Facades\Route;
use App\HTTP\Controllers\ServicioSocialController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('servicio-social', ServicioSocialController::class);
    // Route::get('/servicio-social/{id}/edit', [ServicioSocialController::class, 'edit'])->name('servicio-social.edit');
    // Route::put('/servicio-social/{id}', [ServicioSocialController::class, 'update'])->name('servicio-social.update');
});
