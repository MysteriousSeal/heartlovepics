<?php

use App\Http\Controllers\Admin\ArtistApiController;
use App\Http\Controllers\Admin\ImageApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('admin.api')->prefix('admin')->group(function () {
    Route::patch('/images/{image:slug}', [ImageApiController::class, 'update'])->name('api.admin.images.update');
    Route::patch('/artists/{name}', [ArtistApiController::class, 'update'])
        ->where('name', '.*')
        ->name('api.admin.artists.update');
});
