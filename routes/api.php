<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\PersonaJuridicaController;
use App\Http\Controllers\PersonaNaturalController;

//?? RUTAS DE AUTENTICACIÓN ?/
//** Ruta para iniciar sesión en el sistema */
Route::post("auth/login", [AuthController::class, "login"])->name("login");
//** Ruta para cerrar sesión en el sistema */
Route::post("auth/logout", [AuthController::class, "logout"])->middleware(
    "auth:api",
);
//** Ruta para renovar el token de autenticación */

//* Ruta para obtener el usuario autenticado */
Route::post("auth/me", [AuthController::class, "me"])->middleware("auth:api");

//?? RUTAS DE USUARIOS ?/
//** API de usuarios */
Route::apiResource("usuarios", UsuarioController::class);

//?? RUTAS DE ADMINISTRADORES ?/
//** API de administradores */
Route::apiResource("administradores", AdministradorController::class);

// ?? RUTAS DE PERSONAS JURÍDICAS ?/
//** API de personas jurídicas */
Route::apiResource("personas-juridicas", PersonaJuridicaController::class);

// ?? RUTAS DE PERSONAS NATURALES ?/
//** API de personas naturales */
Route::apiResource("personas-naturales", PersonaNaturalController::class);