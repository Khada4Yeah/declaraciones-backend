<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdministradorRequest;
use App\Services\AdministradorService;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de administradores.
 * Delega toda la lógica de negocio al AdministradorService.
 */
class AdministradorController extends Controller
{
    public function __construct(
        private AdministradorService $administradorService,
    ) {}

    /**
     * Obtiene el listado de todos los administradores.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $administradores = $this->administradorService->obtenerTodos();

        return response()->json([
            "status" => "success",
            "data" => $administradores,
        ]);
    }

    /**
     * Crea un nuevo administrador.
     *
     * @param AdministradorRequest $request Datos validados.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AdministradorRequest $request)
    {
        try {
            $administrador = $this->administradorService->crear($request->validated());

            return response()->json([
                "status" => "success",
                "message" => "Administrador creado exitosamente",
                "data" => $administrador,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error al crear administrador: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al crear el administrador",
            ], 500);
        }
    }

    /**
     * Muestra el detalle de un administrador.
     *
     * @param int $idAdministrador ID del administrador.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $idAdministrador)
    {
        $administrador = $this->administradorService->obtenerPorId($idAdministrador);

        return response()->json([
            "status" => "success",
            "data" => $administrador,
        ]);
    }

    /**
     * Actualiza un administrador existente.
     *
     * @param AdministradorRequest $request Datos validados.
     * @param int $idAdministrador ID del administrador.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AdministradorRequest $request, int $idAdministrador)
    {
        try {
            $administrador = $this->administradorService->actualizar(
                $idAdministrador,
                $request->validated(),
            );

            return response()->json([
                "status" => "success",
                "message" => "Administrador actualizado exitosamente",
                "data" => $administrador,
            ]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar administrador: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al actualizar el administrador",
            ], 500);
        }
    }

    /**
     * Elimina un administrador.
     *
     * @param int $idAdministrador ID del administrador.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $idAdministrador)
    {
        try {
            $this->administradorService->eliminar($idAdministrador);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error("Error al eliminar administrador: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al eliminar el administrador",
            ], 500);
        }
    }
}