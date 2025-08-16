<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\DosenProfileController;
use App\Http\Controllers\MahasiswaProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('fakultas', FakultasController::class);
    Route::apiResource('prodi', ProdiController::class);
    Route::apiResource('jurusan', JurusanController::class);
    Route::get('/users/{id}/profile-mahasiswa', [MahasiswaProfileController::class, 'show']);
    Route::post('/users/{id}/profile-mahasiswa', [MahasiswaProfileController::class, 'store']);
    Route::put('/users/{id}/profile-mahasiswa', [MahasiswaProfileController::class, 'update']);
    Route::get('/users/{id}/profile-dosen', [DosenProfileController::class, 'show']);
    Route::post('/users/{id}/profile-dosen', [DosenProfileController::class, 'store']);
    Route::put('/users/{id}/profile-dosen', [DosenProfileController::class, 'update']);
    Route::patch('/users/{id}/profile-dosen', [DosenProfileController::class, 'update']);
});