<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\PersonaJuridicaController;
use App\Http\Controllers\PersonaNaturalController;

use App\Http\Controllers\ArchivoController;

//?? RUTAS DE AUTENTICACIÓN ?/
//** Ruta para iniciar sesión en el sistema */
Route::post("auth/login", [AuthController::class, "login"])->name("login");
//** Ruta para cerrar sesión en el sistema */
Route::post("auth/logout", [AuthController::class, "logout"])->middleware(
    "auth:api",
);
//** Ruta para renovar el token de autenticación */

//* Ruta para obtener el usuario autenticado */
Route::get("auth/me", [AuthController::class, "me"])->middleware("auth:api");

//?? RUTAS DE USUARIOS ?/
//** API de usuarios */
Route::apiResource("usuarios", UsuarioController::class)->middleware(
    "auth:api",
);

//?? RUTAS DE ADMINISTRADORES ?/
//** API de administradores */
Route::apiResource(
    "administradores",
    AdministradorController::class,
)->middleware("auth:api");

// ?? RUTAS DE PERSONAS JURÍDICAS ?/
//** API de personas jurídicas */
Route::apiResource(
    "personas-juridicas",
    PersonaJuridicaController::class,
)->middleware("auth:api");

// ?? RUTAS DE PERSONAS NATURALES ?/
//** API de personas naturales */
Route::apiResource(
    "personas-naturales",
    PersonaNaturalController::class,
)->middleware("auth:api");

// ?? RUTAS DE ARCHIVOS ?/
//** API de gestión de archivos por usuario */
Route::middleware("auth:api")->group(function () {
    Route::get("archivos/{id_usuario}", [ArchivoController::class, "index"]);
    Route::get("archivos/{id_usuario}/years", [ArchivoController::class, "obtenerAnios"]);
    Route::post("archivos", [ArchivoController::class, "store"]);
    Route::get("archivos/download/{id_archivo}", [ArchivoController::class, "descargar"]);
    Route::post("archivos/download-zip", [ArchivoController::class, "descargarZip"]);
    Route::delete("archivos/{id_archivo}", [ArchivoController::class, "destroy"]);
});