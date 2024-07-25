<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Usuario;
use App\Models\Administrador;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\VarDumper\VarDumper;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("auth:api", ["except" => ["login"]]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthRequest $authRequest)
    {
        $credenciales = $authRequest->validated();

        $usuario = Usuario::where(
            "correo_electronico",
            $credenciales["correo_electronico"],
        )->first();

        if (!$usuario) {
            return response()->json(
                ["message" => "Credenciales incorrectas"],
                401,
            );
        }

        $administrador = Administrador::where(
            "id_usuario",
            $usuario->id_usuario,
        )->first();

        if (
            !$administrador ||
            !Hash::check($credenciales["clave"], $administrador->clave)
        ) {
            return response()->json(
                ["status" => "error", "message" => "Credenciales incorrectas"],
                401,
            );
        }

        try {
            $token = JWTAuth::fromUser($administrador);
            return response()->json(
                [
                    "token" => $token,
                    "expires_in" => JWTAuth::factory()->getTTL() * 60,
                ],
                200,
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "No se pudo crear el token",
                    "errors" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(["message" => "Successfully logged out"]);
    }
}