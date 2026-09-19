<?php

namespace App\Services;

use App\Models\Administrador;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Servicio que encapsula la lógica de autenticación JWT.
 * Maneja login, logout y obtención del usuario autenticado.
 */
class AuthService
{
    /**
     * Autentica un administrador con sus credenciales y genera un token JWT.
     *
     * @param array $credenciales Array con 'correo_electronico' y 'clave'.
     * @return array|null Array con 'token' y 'expires_in' si es exitoso, null si falla.
     */
    public function login(array $credenciales): ?array
    {
        $usuario = Usuario::where(
            "correo_electronico",
            $credenciales["correo_electronico"],
        )->first();

        if (!$usuario) {
            return null;
        }

        $administrador = Administrador::where(
            "id_usuario",
            $usuario->id_usuario,
        )->first();

        if (!$administrador || !Hash::check($credenciales["clave"], $administrador->clave)) {
            return null;
        }

        $token = JWTAuth::fromUser($administrador);

        return [
            "token" => $token,
            "expires_in" => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    /**
     * Invalida el token JWT actual para cerrar la sesión.
     *
     * @return void
     * @throws \Exception Si no se puede invalidar el token.
     */
    public function logout(): void
    {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);
    }

    /**
     * Obtiene el usuario (administrador) actualmente autenticado.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function obtenerUsuarioAutenticado()
    {
        return auth()->user();
    }
}
