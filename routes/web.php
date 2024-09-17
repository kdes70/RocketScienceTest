<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::any('/api/{any}', static function (): JsonResponse {
    return response()->json([
        'message' => 'Api method not found',
    ], 404);
})->where('any', '.*');
