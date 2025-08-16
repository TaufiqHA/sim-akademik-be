<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KRSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\DosenProfileController;
use App\Http\Controllers\JadwalKuliahController;
use App\Http\Controllers\TahunAkademikController;
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
    Route::apiResource('tahun-akademik', TahunAkademikController::class);
    Route::apiResource('mata-kuliah', MataKuliahController::class);
    Route::apiResource('jadwal-kuliah', JadwalKuliahController::class);
    Route::post('tahun-akademik/{id}/set-aktif', [TahunAkademikController::class, 'setAktif']);
    Route::get('/users/{id}/profile-mahasiswa', [MahasiswaProfileController::class, 'show']);
    Route::post('/users/{id}/profile-mahasiswa', [MahasiswaProfileController::class, 'store']);
    Route::put('/users/{id}/profile-mahasiswa', [MahasiswaProfileController::class, 'update']);
    Route::get('/users/{id}/profile-dosen', [DosenProfileController::class, 'show']);
    Route::post('/users/{id}/profile-dosen', [DosenProfileController::class, 'store']);
    Route::put('/users/{id}/profile-dosen', [DosenProfileController::class, 'update']);
    Route::patch('/users/{id}/profile-dosen', [DosenProfileController::class, 'update']);
});

Route::middleware('auth:sanctum')->prefix('krs')->group(function () {
    Route::get('/', [KRSController::class, 'index']);
    Route::post('/', [KRSController::class, 'store']);
    Route::get('{id}', [KRSController::class, 'show']);
    Route::delete('{id}', [KRSController::class, 'destroy']);
    Route::post('{id}/submit', [KRSController::class, 'submit']);
    Route::post('{id}/approve', [KRSController::class, 'approve']);
    Route::get('{id}/detail', [KRSController::class, 'details']);
    Route::post('{id}/detail', [KRSController::class, 'addDetail']);
    Route::delete('{id}/detail/{detailId}', [KRSController::class, 'removeDetail']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/nilai', [NilaiController::class, 'index']);
    Route::get('/nilai/{id}', [NilaiController::class, 'show']);
    Route::post('/nilai', [NilaiController::class, 'store']);
    Route::delete('/nilai/{id}', [NilaiController::class, 'destroy']);
    Route::post('/nilai/{id}/finalize', [NilaiController::class, 'finalize']);
});
