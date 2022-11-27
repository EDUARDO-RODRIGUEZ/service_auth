<?php

use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('usuario')->group(function () {
    Route::post("/register", [UsuarioController::class, "register"])->name("usuario.register");
    Route::post("/compare", [UsuarioController::class, "compareFace"])->name("usuario.compare");
    Route::post("/login", [UsuarioController::class, "login"])->name("usuario.login");
});
