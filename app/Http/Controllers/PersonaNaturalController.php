<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonaNaturalRequest;
use App\Services\PersonaNaturalService;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de personas naturales.
 * Delega toda la lógica de negocio al PersonaNaturalService.
 */
class PersonaNaturalController extends Controller
{
    public function __construct(
        private PersonaNaturalService $personaNaturalService,
    ) {}

    /**
     * Obtiene el listado de todas las personas naturales.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $personas = $this->personaNaturalService->obtenerTodas();

        return response()->json([
            "status" => "success",
            "data" => $personas,
        ]);
    }

    /**
     * Crea una nueva persona natural.
     *
     * @param PersonaNaturalRequest $request Datos validados.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PersonaNaturalRequest $request)
    {
        try {
            $persona = $this->personaNaturalService->crear($request->validated());

            return response()->json([
                "status" => "success",
                "message" => "Persona natural creada exitosamente",
                "data" => $persona,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error al crear persona natural: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al crear la persona natural",
            ], 500);
        }
    }

    /**
     * Muestra el detalle de una persona natural (incluye clave de acceso).
     *
     * @param int $idPersonaNatural ID de la persona natural.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $idPersonaNatural)
    {
        $persona = $this->personaNaturalService->obtenerPorId($idPersonaNatural);

        return response()->json([
            "status" => "success",
            "data" => $persona,
        ]);
    }

    /**
     * Actualiza una persona natural existente.
     *
     * @param PersonaNaturalRequest $request Datos validados.
     * @param int $idPersonaNatural ID de la persona natural.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PersonaNaturalRequest $request, int $idPersonaNatural)
    {
        try {
            $persona = $this->personaNaturalService->actualizar(
                $idPersonaNatural,
                $request->validated(),
            );

            return response()->json([
                "status" => "success",
                "message" => "Persona natural actualizada exitosamente",
                "data" => $persona,
            ]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar persona natural: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al actualizar la persona natural",
            ], 500);
        }
    }

    /**
     * Elimina una persona natural.
     *
     * @param int $idPersonaNatural ID de la persona natural.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $idPersonaNatural)
    {
        try {
            $this->personaNaturalService->eliminar($idPersonaNatural);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error("Error al eliminar persona natural: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al eliminar la persona natural",
            ], 500);
        }
    }
}
