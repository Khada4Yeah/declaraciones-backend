<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de usuarios base.
 * Los usuarios se crean a través de los controladores de
 * PersonaNatural, PersonaJuridica o Administrador.
 * Este controlador maneja operaciones directas sobre el modelo Usuario.
 */
class UsuarioController extends Controller
{
    /**
     * Obtiene el listado de todos los usuarios.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $usuarios = Usuario::all();

        return response()->json([
            "status" => "success",
            "data" => $usuarios,
        ]);
    }

    /**
     * Crea un nuevo usuario.
     *
     * @param UsuarioRequest $request Datos validados del usuario.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UsuarioRequest $request)
    {
        try {
            $usuario = Usuario::create($request->validated());

            return response()->json([
                "status" => "success",
                "message" => "Usuario creado exitosamente",
                "data" => $usuario,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error al crear usuario: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al crear el usuario",
            ], 500);
        }
    }

    /**
     * Muestra el detalle de un usuario.
     *
     * @param int $idUsuario ID del usuario.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);

        return response()->json([
            "status" => "success",
            "data" => $usuario,
        ]);
    }

    /**
     * Actualiza un usuario existente.
     *
     * @param UsuarioRequest $request Datos validados.
     * @param int $idUsuario ID del usuario.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UsuarioRequest $request, int $idUsuario)
    {
        try {
            $usuario = Usuario::findOrFail($idUsuario);
            $usuario->update($request->validated());

            return response()->json([
                "status" => "success",
                "message" => "Usuario actualizado exitosamente",
                "data" => $usuario,
            ]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar usuario: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al actualizar el usuario",
            ], 500);
        }
    }

    /**
     * Elimina un usuario.
     *
     * @param int $idUsuario ID del usuario.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $idUsuario)
    {
        try {
            $usuario = Usuario::findOrFail($idUsuario);
            $usuario->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error("Error al eliminar usuario: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al eliminar el usuario",
            ], 500);
        }
    }
}