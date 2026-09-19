<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de autenticación.
 * Delega la lógica de JWT al AuthService.
 */
class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {
        $this->middleware("auth:api", ["except" => ["login"]]);
    }

    /**
     * Inicia sesión y genera un token JWT.
     *
     * @param AuthRequest $request Datos validados de autenticación.
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthRequest $request)
    {
        try {
            $resultado = $this->authService->login($request->validated());

            if (!$resultado) {
                return response()->json([
                    "status" => "error",
                    "message" => "Credenciales incorrectas",
                ], 401);
            }

            return response()->json([
                "status" => "success",
                "token" => $resultado["token"],
                "expires_in" => $resultado["expires_in"],
            ]);
        } catch (\Throwable $e) {
            Log::error("Error al crear token JWT: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "No se pudo crear el token",
            ], 500);
        }
    }

    /**
     * Cierra la sesión invalidando el token JWT.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $this->authService->logout();

            return response()->json([
                "status" => "success",
                "message" => "Usuario deslogueado correctamente",
            ]);
        } catch (\Throwable $e) {
            Log::error("Error al cerrar sesión: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "No se pudo cerrar la sesión",
            ], 500);
        }
    }

    /**
     * Obtiene los datos del usuario autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = $this->authService->obtenerUsuarioAutenticado();

        return response()->json([
            "status" => "success",
            "data" => $user,
        ]);
    }
}