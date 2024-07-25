<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\PersonaJuridicaController;
use App\Http\Controllers\PersonaNaturalController;

//?? RUTAS DE AUTENTICACIÓN ?/
Route::post("auth/login", [AuthController::class, "login"]);

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