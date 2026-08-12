<?php

use App\Http\Controllers\Admin\ArtistApiController;
use App\Http\Controllers\Admin\ImageApiController;
use App\Http\Controllers\Admin\TagApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('admin.api')->prefix('admin')->group(function () {
    Route::get('/tags', [TagApiController::class, 'index'])->name('api.admin.tags.index');
    Route::delete('/tags/{name}', [TagApiController::class, 'destroy'])
        ->where('name', '.*')
        ->name('api.admin.tags.destroy');
    Route::get('/images/{idOrSlug}', [ImageApiController::class, 'show'])->name('api.admin.images.show');
    Route::patch('/images/{image:slug}', [ImageApiController::class, 'update'])->name('api.admin.images.update');
    Route::patch('/artists/{name}', [ArtistApiController::class, 'update'])
        ->where('name', '.*')
        ->name('api.admin.artists.update');
});
