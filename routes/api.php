<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/process-data', function (Request $request) {
    $data = $request->input('data');
    $processedData = $data + 1;
    return response()->json(['data' => $processedData]);
});

Route::post('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
