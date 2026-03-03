<?php 

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// 認証不要
// ユーザー登録
Route::post('/auth/register', [AuthController::class, 'register']);
// ログイン
Route::post('/auth/login', [AuthController::class, 'login']);

// 認証必要
Route::middleware('auth:sanctum')->group(function () {
    // ログアウト
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});