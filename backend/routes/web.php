<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/serve-image/{filename}', function ($filename) {
    $path = storage_path("app/public/admin/products/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});

